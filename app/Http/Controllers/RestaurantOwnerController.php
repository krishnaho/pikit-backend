<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\ItemCategory;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RestaurantOwnerController extends Controller
{
    public function dashboard()
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $totalUsers = User::role('Customer')->get();
        $runningOrders = Order::where('restaurant_id', $restaurant->id)->where('order_status_id', [1, 2, 3, 4, 5, 6, 9, 10, 11])->get();
        $orders = Order::where('restaurant_id', $restaurant->id)->with('user', 'restaurant', 'orderstatus')->get();
        $totalSales = Order::where('restaurant_id', $restaurant->id)->where('order_status_id', 7)->sum('total');
        return view('restaurantOwner.dashboard.dashboard', [
            'totalUsers' => $totalUsers,
            'orders' => $orders,
            'totalSales' => $totalSales,
            'runningOrders' => $runningOrders,
        ]);
    }

    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    // -------------------------------------------- Restaurant ------------------------------------------
    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


    public function viewRestaurant()
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $restaurants = Restaurant::where('id', $restaurant->id)->get();
        $restaurantCategories = RestaurantCategory::where('is_active', 1)->get();
        $cities = City::where('is_active', 1)->get();
        return view('restaurantOwner.restaurant.restaurant', [
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
        $restaurant->tax = $request->tax;

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
        return redirect()->back()->with('success', 'Restaurant  Created Successfully');
    }

    public function editRestaurant($id)
    {
        $restaurant = Restaurant::where('id', $id)->first();
        $restaurantCategories = RestaurantCategory::where('is_active', 1)->get();
        $cities = City::where('is_active', 1)->get();
        return view('restaurantOwner.restaurant.editRestaurant', [
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
    
}
