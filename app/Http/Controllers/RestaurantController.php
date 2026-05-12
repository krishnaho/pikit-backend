<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ItemCategory;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\saveBatchToFleet;
use LaravelQRCode\Facades\QRCode;

class RestaurantController extends Controller
{
    public function viewRestaurantCategories()
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $restaurantCategories = RestaurantCategory::get();
        return view('admin.restaurantCategories.restaurantCategories', [
            'restaurantCategories' => $restaurantCategories,
            'restaurant' => $restaurant
        ]);
    }
    public function addRestaurantCategory(Request $request)
    {
        $restaurantCategory = new RestaurantCategory();
        $restaurantCategory->name = $request->name;
        $request->validate([
            'images' => 'image|mimes:jpeg,jpg,max:50',
        ]);
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/restaurant-category/'), $imageName);
            $restaurantCategory->image = 'restaurant-category/' . $imageName;
        }
        $restaurantCategory->save();
        return redirect()->back()->with('success', 'Restaurant Category Created Successfully');
    }

    public function updateRestaurantCategory(Request $request)
    {
        $restaurantCategory = RestaurantCategory::where('id', $request->id)->first();
        $restaurantCategory->name = $request->name;
        $request->validate([
            'images' => 'image|mimes:jpeg,png,jpg,jfif,pjpeg,pjp,svg,webp|max:50',
        ]);
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/restaurant-category/'), $imageName);
            @unlink(public_path($restaurantCategory->image));
            $restaurantCategory->image = 'restaurant-category/' . $imageName;
        }
        $restaurantCategory->save();
        return redirect()->back()->with('success', 'City Updated Successfully');
    }



    public function restaurantCategories($id)
    {
        $itemCategories  = ItemCategory::where('restaurant_id', $id)->orderBy('order_column', 'ASC')->get();
        return view('admin.restaurant.sortItemCategories', [
            'itemcategories' =>   $itemCategories
        ]);
    }


    public function sortItemCategory(Request $request)
    {
        $itemCategories = ItemCategory::whereIn('id', $request->newOrder)->get();
        foreach ($request->newOrder as $key => $newOrder) {
            $itemCategory = $itemCategories->where('id', $newOrder)->first();
            $itemCategory->order_column = $key + 1;
            $itemCategory->save();
        }
        $response = [
            'success' => true,
        ];
        return response()->json($response);
    }
    public function deleteRestaurantCategory($id)
    {
        $city = RestaurantCategory::find($id);

        if ($city) {
            $city->delete();
            return redirect()->back()->with(['success' => 'City Deleted Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function toggleRestaurantCategory($id)
    {
        $city = RestaurantCategory::find($id);
        if ($city) {
            $city->toggleActive();
            $city->save();
            return redirect()->back()->with(['success' => 'Status Changeed Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }



    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    // -------------------------------------------- Restaurant ------------------------------------------
    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


    public function viewRestaurant()
    {

        $restaurants = Restaurant::get();

        // foreach ($restaurants as $key => $restaurant) {
        //     $slug = str_replace(' ', '-', strtolower($restaurant->name));
        //     $restaurant->slug = $slug;
        //     $restaurant->save();
        // }

        $restaurantCategories = RestaurantCategory::where('is_active', 1)->get();
        $cities = City::where('is_active', 1)->get();
        return view('admin.restaurant.restaurant', [
            'restaurants' => $restaurants,
            'restaurantCategories' => $restaurantCategories,
            'cities' => $cities
        ]);
    }

    public function addRestaurant(Request $request)
    {
        $restaurant = new Restaurant();
        $restaurant->name = $request->name;
        $request->validate([
            'images' => 'image|mimes:jpeg,jpg,max:50',
        ]);
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/restaurant/'), $imageName);
            $restaurant->image = 'restaurant/' . $imageName;
        }

        $restaurant->description = $request->description;
        $restaurant->phone = $request->phone;
        $restaurant->restaurant_category_id = $request->restaurant_category_id;
        $restaurant->city_id = $request->city_id;
        $restaurant->address = $request->address;
        $restaurant->land_mark = $request->land_mark;
        $restaurant->latitude = $request->latitude;
        $restaurant->longitude = $request->longitude;
        $restaurant->delivery_radius = $request->delivery_radius;
        $restaurant->rating = $request->rating;
        $restaurant->restaurant_charges = $request->restaurant_charges;
        $restaurant->min_order_price = $request->min_order_price;
        $restaurant->commission_rate = $request->commission_rate;
        $restaurant->approx_time_delivery = $request->approx_time_delivery;
        $restaurant->offer_text1 = $request->offer_text1;
        $restaurant->offer_text2 = $request->offer_text2;
        $restaurant->howin_fleet_team_id = $request->howin_fleet_team_id;

        if ($request->is_veg == "on") {
            $restaurant->is_veg = 1;
        } else {
            $restaurant->is_veg = 0;
        }

        if ($request->is_popular == "on") {
            $restaurant->is_popular = 1;
        } else {
            $restaurant->is_popular = 0;
        }

        if ($request->is_recommended == "on") {
            $restaurant->is_recommended = 1;
        } else {
            $restaurant->is_recommended = 0;
        }
        if ($request->is_freedelivery == "on") {
            $restaurant->is_freedelivery = 1;
        } else {
            $restaurant->is_freedelivery = 0;
        }

        $restaurant->slug = str_replace(' ', '-', strtolower($restaurant->name));
        $restaurant->save();

        $this->generateRestaurantQRCode($restaurant->slug);

        //Save Batch to Fleet
        dispatch(new saveBatchToFleet($restaurant));

        return redirect()->back()->with('success', 'Restaurant  Created Successfully');
    }

    public function editRestaurant($id)
    {
        $restaurant = Restaurant::where('id', $id)->first();
        $restaurantCategories = RestaurantCategory::where('is_active', 1)->get();
        $cities = City::where('is_active', 1)->get();
        return view('admin.restaurant.editRestaurant', [
            'restaurant' => $restaurant,
            'restaurantCategories' => $restaurantCategories,
            'cities' => $cities
        ]);
    }

    public function updateRestaurant(Request $request)
    {
        $restaurant = Restaurant::where('id', $request->id)->first();
        $restaurant->name = $request->name;
        $request->validate([
            'images' => 'image|mimes:jpeg,png,jpg,jfif,pjpeg,pjp,svg,webp|max:50',
        ]);
        if ($request->file('image')) {
            $file = $request->file('image');
            $imageName = time() . $file->getClientOriginalName();
            $file->move(public_path('/restaurant/'), $imageName);
            @unlink(public_path($restaurant->image));
            $restaurant->image = 'restaurant/' . $imageName;
        }
        $restaurant->description = $request->description;
        $restaurant->phone = $request->phone;
        $restaurant->restaurant_category_id = $request->restaurant_category_id;
        $restaurant->city_id = $request->city_id;
        $restaurant->address = $request->address;
        $restaurant->land_mark = $request->land_mark;
        $restaurant->latitude = $request->latitude;
        $restaurant->longitude = $request->longitude;
        $restaurant->delivery_radius = $request->delivery_radius;
        $restaurant->rating = $request->rating;
        $restaurant->restaurant_charges = $request->restaurant_charges;
        $restaurant->min_order_price = $request->min_order_price;
        $restaurant->commission_rate = $request->commission_rate;
        $restaurant->tax = $request->tax;
        $restaurant->approx_time_delivery = $request->approx_time_delivery;
        $restaurant->offer_text1 = $request->offer_text1;
        $restaurant->offer_text2 = $request->offer_text2;
        $restaurant->howin_fleet_team_id = $request->howin_fleet_team_id;
        $restaurant->slug = str_replace(' ', '-', strtolower($restaurant->name));


        if ($request->is_veg == "on") {
            $restaurant->is_veg = 1;
        } else {
            $restaurant->is_veg = 0;
        }

        if ($request->is_popular == "on") {
            $restaurant->is_popular = 1;
        } else {
            $restaurant->is_popular = 0;
        }

        if ($request->is_recommended == "on") {
            $restaurant->is_recommended = 1;
        } else {
            $restaurant->is_recommended = 0;
        }
        if ($request->is_freedelivery == "on") {
            $restaurant->is_freedelivery = 1;
        } else {
            $restaurant->is_freedelivery = 0;
        }
        $restaurant->save();

        @unlink(public_path('qrcodes/' . $restaurant->qr_code));

        $this->generateRestaurantQRCode($restaurant->slug);

        //Save Batch to Fleet
        dispatch(new saveBatchToFleet($restaurant, $update = true));
        return redirect()->back()->with('success', 'Restaurant Updated Successfully');
    }

    public function deleteRestaurant($id)
    {
        $city = Restaurant::find($id);

        if ($city) {
            $city->delete();
            return redirect()->back()->with(['success' => 'Restaurant Deleted Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function toggleRestaurant($id)
    {
        $city = Restaurant::find($id);
        if ($city) {
            $city->toggleActive();
            $city->save();
            return redirect()->back()->with(['success' => 'Status Changed Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong']);
        }
    }

    public function syncTeamToFeet($id)
    {
        $restaurant = Restaurant::find($id);
        dispatch(new saveBatchToFleet($restaurant, $update = true));
        return redirect()->back()->with(['success' => 'Batch Synced Successfully']);
    }

    public function downloadRestaurantQRCode($id)
    {
        $restaurant = Restaurant::find($id);
        $path = public_path($restaurant->qr_code);
        return response()->download($path);
    }


    public function generateRestaurantQRCode($slug = null)
    {
        if ($slug) {
            $restaurants = Restaurant::where('slug', $slug)->get();
        } else {
            $restaurants = Restaurant::get();
        }

        $count = 0;
        foreach ($restaurants as $restaurant) {
            $url = 'https://zeatoapp.com/shop-home/' . $restaurant->slug .'?ref=qr';
            $filename = 'QR-' . $restaurant->id . '.png';
            $path = public_path('qrcodes/' . $filename);

            if (!file_exists(public_path('qrcodes'))) {
                mkdir(public_path('qrcodes'), 0755, true);
            }

            $restaurant->qr_code = 'qrcodes/' . $filename;
            $restaurant->save();

            $count++;

            QRCode::text($url)
                ->setSize(8)
                ->setMargin(4)
                ->setOutfile($path)
                ->png();
        }

        return response(['message' => $count . ' QR Code Generated Successfully']);
    }
}
