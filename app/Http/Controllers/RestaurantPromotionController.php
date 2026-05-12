<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Coupon;
use App\Models\Item;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantPromotionController extends Controller
{
    public function viewAllBanners()
    {
        $restaurantIds = Auth::User()->restaurants->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $cities = Auth::user()->cities;
        $cityIds = $cities->pluck('id')->toArray();
        $banners = Banner::whereIn('city_id', $cityIds)->where('is_active', 1)->with('restaurants')->whereHas('restaurants', function ($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })->get();
        $restaurantCategories = RestaurantCategory::where('is_active', 1)->get();
        $items = Item::where('restaurant_id', $restaurant->id)->where('is_active', 1)->get();
        return view('restaurantOwner.banners.viewBanner', [
            'banners' => $banners,
            'restaurantCategories' => $restaurantCategories,
            'cities' => $cities,
            'restaurant' => $restaurant,
            'items' => $items
        ]);
    }

    public function createBanner(Request $request)
    {
        $banner = new Banner();
        $banner->name = $request->name;
        $banner->longitude = $request->longitude;
        $banner->latitude = $request->latitude;
        $banner->radius = $request->radius;
        $banner->restaurant_category_id = $request->restaurant_category_id;
        $banner->type = $request->type;
        $banner->city_id = $request->city_id;
        if ($request->image) {
            $image = $request->file('image');
            $imageName = time() . $image->getClientOriginalName();
            $image->move(public_path('/assets/images/banners/'), $imageName);
            $banner->image = '/assets/images/banners/' . $imageName;
        }
        $banner->save();
        if ($request->type == 'MULTI_RESTAURANT') {
            $banner->restaurants()->sync($request->restaurant_ids);
            $banner->items()->sync([]);
        }
        if ($request->type == 'SINGLE_RESTAURANT') {
            $banner->restaurants()->sync($request->restaurant_id);
            $banner->items()->sync([]);
        }
        if ($request->type == 'ITEM') {
            $banner->items()->sync($request->item_id);
            $banner->restaurants()->sync([]);
        }
        return redirect()->back()->with('success', 'Banner Created Successfully');
    }
    public function editBanner($id)
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $banner = Banner::with('restaurants', 'restaurant_category')->find($id);
        $restaurantCategories = RestaurantCategory::where('is_active', 1)->get();
        if ($banner) {
            $bannerItems = $banner->items()->pluck('item_id')->toArray();
        } else {
            $bannerItems = [];
        }
        $items = Item::where('restaurant_id', $restaurant->id)->where('restaurant_id', $restaurantIds)->get();
        $cities = Auth::user()->cities;
        try {
            if ($banner) {
                return view('restaurantOwner.banners.editBanner', [
                    'banner' => $banner,
                    'restaurantCategories' => $restaurantCategories,
                    'cities' => $cities,
                    'bannerItems' => $bannerItems,
                    'items' => $items,
                    'restaurant' => $restaurant,
                ]);
            } else {
                return redirect()->back()->with(['error' => 'Something Went Wrong, Try Again']);
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }
    public function getestaurantCategoryItems(Request $request)
    {
        if ($request->city_id) {
            $restaurantIds = Restaurant::where('city_id', $request->city_id)->where('restaurant_category_id', $request->restaurant_category_id)->where('is_deleted', 0)->pluck('id')->toArray();
        } else {
            $restaurantIds = Restaurant::where('restaurant_category_id', $request->restaurant_category_id)->where('is_deleted', 0)->pluck('id')->toArray();
        }
        $items = Item::where('is_deleted', 0)->where('restaurant_id', $restaurantIds)->get();
        return response()->json($items);
    }
    public function getRestaurantCategoryRestaurant(Request $request)
    {
        if ($request->city_id) {
            $restaurants = Restaurant::where('is_deleted', 0)->where('restaurant_category_id', $request->restaurant_category_id)->where('city_id', $request->city_id)->get();
        } else {
            $restaurants = Restaurant::where('is_deleted', 0)->where('restaurant_category_id', $request->restaurant_category_id)->get();
        }
        return response()->json($restaurants);
    }
    public function getRestaurantCategoryRestaurants(Request $request)
    {
        $restaurants = Restaurant::where('is_deleted', 0)->where('restaurant_category_id', $request->restaurant_category_id)->where('city_id', $request->city_id)->get();
        return response()->json($restaurants);
    }

    public function updateBanner(Request $request)
    {
        $banner = Banner::find($request->id);
        if ($banner) {
            $banner->name = $request->name;
            $banner->longitude = $request->longitude;
            $banner->latitude = $request->latitude;
            $banner->radius = $request->radius;
            $banner->restaurant_category_id = $request->restaurant_category_id;
            $banner->type = $request->type;
            $banner->city_id = $request->city_id;
            if ($request->image) {
                $image = $request->file('image');
                $imageName = time() . $image->getClientOriginalName();
                $image->move(public_path('/assets/images/banners/'), $imageName);
                $banner->image = '/assets/images/banners/' . $imageName;
            }
            $banner->save();
            if ($request->type == 'MULTI_RESTAURANT') {
                $banner->restaurants()->sync($request->restaurant_ids);
                $banner->items()->sync([]);
            }
            if ($request->type == 'SINGLE_RESTAURANT') {
                $banner->restaurants()->sync($request->restaurant_id);
                $banner->items()->sync([]);
            }
            if ($request->type == 'ITEM') {
                $banner->items()->sync($request->item_id);
                $banner->restaurants()->sync([]);
            }
            return redirect()->back()->with(['success' => 'Banner Updated Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong. Try Again']);
        }
    }

    public function deleteBanner($id)
    {
        $banner = Banner::find($id);
        try {

            if ($banner) {
                $banner->items()->detach();
                $banner->is_deleted = 1;
                $banner->save();
                return redirect()->back()->with(['success' => 'Banner Deleted Successfully']);
            } else {
                return redirect()->back()->with(['error' => 'Banner Deletion Failed']);
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }
    public function toggleBanner($id)
    {
        $banner = Banner::find($id);
        try {
            if ($banner) {
                $banner->toggleActive()->save();
                return redirect()->back()->with(['success' => 'Operation Successfull']);
            } else {
                return redirect()->back()->with(['error' => ' Operation Failed']);
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }



    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    // ------------------------------------------Coupon---------------------------------------------------
    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    public function viewAllCoupons()
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $cities = Auth::user()->cities;
        $cityIds = $cities->pluck('id')->toArray();
        $coupons = Coupon::whereIn('city_id', $cityIds)->where('is_active', 1)->with('restaurants')->whereHas('restaurants', function ($query) use ($restaurantIds) {
            $query->whereIn('restaurant_id', $restaurantIds);
        })->get();
        $restaurantCategories = RestaurantCategory::where('is_active', 1)->get();
        $items = Item::where('restaurant_id', $restaurant->id)->where('is_active', 1)->get();
        return view('restaurantOwner.coupons.viewCoupon', [
            'coupons' => $coupons,
            'restaurantCategories' => $restaurantCategories,
            'cities' => $cities,
            'restaurant' => $restaurant,
            'items' => $items
        ]);
    }

    public function createCoupon(Request $request)
    {
        $coupon = new Coupon();
        $coupon->name = $request->name;
        $coupon->description = $request->description;
        $coupon->coupon_code = $request->coupon_code;
        $coupon->coupon_type = $request->coupon_type;
        $coupon->discount_type = $request->discount_type;
        $coupon->coupon_discount = $request->coupon_discount;
        $coupon->max_discount = $request->max_discount;
        $coupon->start_date = $request->start_date;
        $coupon->end_date = $request->end_date;
        $coupon->max_count = $request->max_count;
        $coupon->min_sub_total = $request->min_sub_total;
        $coupon->sub_total_message = $request->sub_total_message;
        $coupon->user_type = $request->user_type;
        $coupon->longitude = $request->longitude;
        $coupon->latitude = $request->latitude;
        $coupon->radius = $request->radius;
        $coupon->city_id = $request->city_id;

        if ($request->user_type == 'CUSTOM') {
            $coupon->max_count_per_user = $request->max_count_per_user;
        }
        if ($request->image) {
            $image = $request->file('image');
            $imageName = time() . $image->getClientOriginalName();
            $image->move(public_path('/assets/images/Coupons/'), $imageName);
            $coupon->image = '/assets/images/Coupons/' . $imageName;
        }
        $coupon->save();
        if ($request->coupon_type == 'RESTAURANT') {
            $coupon->restaurants()->sync($request->restaurant_ids);
            $coupon->items()->sync([]);
        }
        if ($request->coupon_type == 'ITEM') {
            $coupon->items()->sync($request->item_id);
            $coupon->restaurants()->sync([]);
        }
        return redirect()->back()->with('success', 'Coupon Created Successfully');
    }
    public function editCoupon($id)
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();

        $coupon = Coupon::with('restaurant_category')->find($id);
        $restaurantCategories = RestaurantCategory::where('is_active', 1)->get();
        $couponItems = $coupon->items()->pluck('item_id')->toArray();
        $couponRestaurants = $coupon->restaurants()->pluck('restaurant_id')->toArray();
        $cities = Auth::user()->cities;
        $restaurantIds = Restaurant::where('city_id', $coupon->city_id)->where('is_deleted', 0)->pluck('id')->toArray();
        $items = Item::where('restaurant_id', $restaurantIds)->get();
        // dd($bannerRestaurants);
        try {
            if ($coupon) {
                return view('restaurantOwner.coupons.editCoupon', [
                    'coupon' => $coupon,
                    'restaurantCategories' => $restaurantCategories,
                    'cities' => $cities,
                    'couponItems' => $couponItems,
                    'items' => $items,
                    'restaurant' => $restaurant,
                    'couponRestaurants' => $couponRestaurants,
                ]);
            } else {
                return redirect()->back()->with(['error' => 'Something Went Wrong, Try Again']);
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }

    public function updateCoupon(Request $request)
    {
        $coupon = Coupon::find($request->id);
        if ($coupon) {
            $coupon->name = $request->name;
            $coupon->description = $request->description;
            $coupon->coupon_code = $request->coupon_code;
            $coupon->coupon_type = $request->coupon_type;
            $coupon->discount_type = $request->discount_type;
            $coupon->coupon_discount = $request->coupon_discount;
            $coupon->max_discount = $request->max_discount;
            $coupon->start_date = $request->start_date;
            $coupon->end_date = $request->end_date;
            $coupon->max_count = $request->max_count;
            $coupon->min_sub_total = $request->min_sub_total;
            $coupon->sub_total_message = $request->sub_total_message;
            $coupon->user_type = $request->user_type;
            $coupon->city_id = $request->city_id;

            if ($request->image) {
                $image = $request->file('image');
                $imageName = time() . $image->getClientOriginalName();
                $image->move(public_path('/assets/images/Coupons/'), $imageName);
                $coupon->image = '/assets/images/Coupons/' . $imageName;
            }
            if ($request->coupon_type == 'RESTAURANT') {
                $coupon->restaurants()->sync($request->restaurant_ids);
                $coupon->items()->sync([]);
            }
            if ($request->coupon_type == 'ITEM') {
                $coupon->items()->sync($request->item_id);
                $coupon->restaurants()->sync([]);
            }
            $coupon->save();
            return redirect()->back()->with(['success' => 'Coupon Updated Successfully']);
        } else {
            return redirect()->back()->with(['error' => 'Something Went Wrong. Try Again']);
        }
    }

    public function deleteCoupon($id)
    {
        $coupon = Coupon::find($id);
        try {

            if ($coupon) {
                $coupon->items()->detach();
                $coupon->is_deleted = 1;
                $coupon->save();
                return redirect()->back()->with(['success' => 'Coupon Deleted Successfully']);
            } else {
                return redirect()->back()->with(['error' => 'Coupon Deletion Failed']);
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }
    public function toggleCoupon($id)
    {
        $coupon = Coupon::find($id);
        try {
            if ($coupon) {
                $coupon->toggleActive()->save();
                return redirect()->back()->with(['success' => 'Operation Successfull']);
            } else {
                return redirect()->back()->with(['error' => ' Operation Failed']);
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }


    public function getCityItems(Request $request)
    {
        $restaurantIds = Restaurant::where('city_id', $request->city_id)->where('is_deleted', 0)->pluck('id')->toArray();
        $items = Item::where('is_deleted', 0)->where('restaurant_id', $restaurantIds)->get();
        return response()->json($items);
    }
    public function getCityRestaurants(Request $request)
    {
        $restaurants = Restaurant::where('is_deleted', 0)->where('city_id', $request->city_id)->get();
        return response()->json($restaurants);
    }
}
