<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\AddonCategory;
use App\Models\Banner;
use App\Models\Branch;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Meal;
use App\Models\Scheme;
use App\Models\Subscription;
use App\Models\SubscriptionUser;
use App\Models\Transaction;
use App\Models\UserScheme;
use App\Models\Brand;
use App\Models\City;
use App\Models\Coupon;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemGroup;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class RestaurantDatatableController extends Controller
{

    public function getAllRestaurants()
    {
        $restaurantIds = Auth::User()->restaurants->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $restaurants = Restaurant::where('id', $restaurant->id)->with('restaurantCategory', 'city')->get();
        return Datatables::of($restaurants)
            ->addColumn('id', function ($restaurant) {
                return $restaurant->id;
            })
            ->addColumn('name', function ($restaurant) {
                return $restaurant->name;
            })
            ->addColumn('restaurantCategory', function ($restaurant) {
                if ($restaurant->restaurantCategory)
                    return $restaurant->restaurantCategory->name;
                else
                    return '--';
            })
            ->addColumn('image', function ($restaurant) {
                $html = '<a class="transaction-image popup-image product-card"
                            href="' . asset($restaurant->image) . '" target="_blank">
                            <img class="w-100 rounded-top" style="width: 5rem;height:5rem"
                                src="' . asset($restaurant->image) . '" alt="">
                            <span class="transaction-preview" style="background:#00000060">
                                <div class="transaction-text">
                                    <em class="icon ni ni-eye "></em><span
                                        style="margin-left:2px ">preview</span>
                                </div>
                            </span>
                        </a>';
                if ($restaurant->image)
                    return $html;
                else
                    return "-No Image-";
            })
            ->addColumn('phone', function ($restaurant) {
                if ($restaurant->phone)
                    return $restaurant->phone;
                else
                    return '--';
            })
            ->addColumn('status', function ($restaurant) {
                $success = '<a class="btn btn-icon btn-success"
                href="' . route('restaurantOwner.toggleRestaurant', $restaurant->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('restaurantOwner.toggleRestaurant', $restaurant->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($restaurant->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($restaurant) {
                $html = '
                <ul class="nk-tb-actions gx-1" >
                 <li>
                    <div class="drodown"><a href="#"
                        class="dropdown-toggle btn btn-icon btn-trigger"
                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                            <li>
                                <a   href="' . route('restaurantOwner.editRestaurant', $restaurant->id) . '" >
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit Restaurant</span></span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteRestaurant' . $restaurant->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete Restaurant</span></span>
                                </a>
                            </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name',  'image', 'phone', 'restaurantCategory', 'status', 'action'])
            ->make(true);
    }

    public function getAllItemCategories()
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $itemCategories = ItemCategory::where('restaurant_id', $restaurant->id)->with('restaurant')->get();
        return Datatables::of($itemCategories)
            ->addColumn('id', function ($itemCategory) {
                return $itemCategory->id;
            })
            ->addColumn('name', function ($itemCategory) {
                return $itemCategory->name;
            })
            ->addColumn('image', function ($itemCategory) {
                $html = '<a class="transaction-image popup-image product-card"
                            href="' . asset($itemCategory->image) . '" target="_blank">
                            <img class="w-100 rounded-top" style="width: 5rem;height:5rem"
                                src="' . asset($itemCategory->image) . '" alt="">
                            <span class="transaction-preview" style="background:#00000060">
                                <div class="transaction-text">
                                    <em class="icon ni ni-eye "></em><span
                                        style="margin-left:2px ">preview</span>
                                </div>
                            </span>
                        </a>';
                if ($itemCategory->image)
                    return $html;
                else
                    return "-No Image-";
            })
            ->addColumn('restaurant', function ($itemCategory) {
                if ($itemCategory->restaurant)
                    return $itemCategory->restaurant->name;
                else
                    return '--';
            })
            ->addColumn('status', function ($itemCategory) {
                $success = '<a class="btn btn-icon btn-success"
                href="' . route('restaurantOwner.toggleItemCategory', $itemCategory->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('restaurantOwner.toggleItemCategory', $itemCategory->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($itemCategory->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($itemCategory) {
                $html = '
                <ul class="nk-tb-actions gx-1" >
                 <li>
                    <div class="drodown"><a href="#"
                        class="dropdown-toggle btn btn-icon btn-trigger"
                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#editItemCategory' . $itemCategory->id . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit Item Category</span></span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteItemCategory' . $itemCategory->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete Item Category</span></span>
                                </a>
                            </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name',  'image', 'restaurant', 'status', 'action'])
            ->make(true);
    }

    public function getAllItemGroups()
    {
        $itemGroups = ItemGroup::with('restaurantCategory')->get();
        return Datatables::of($itemGroups)
            ->addColumn('id', function ($itemGroup) {
                return $itemGroup->id;
            })
            ->addColumn('name', function ($itemGroup) {
                return $itemGroup->name;
            })
            ->addColumn('restaurant_category', function ($itemGroup) {
                if ($itemGroup->restaurantCategory)
                    return $itemGroup->restaurantCategory->name;
                else
                    return '--';
            })
            ->addColumn('image', function ($itemGroup) {
                $html = '<a class="transaction-image popup-image product-card"
                            href="' . asset($itemGroup->image) . '" target="_blank">
                            <img class="w-100 rounded-top" style="width: 5rem;height:5rem"
                                src="' . asset($itemGroup->image) . '" alt="">
                            <span class="transaction-preview" style="background:#00000060">
                                <div class="transaction-text">
                                    <em class="icon ni ni-eye "></em><span
                                        style="margin-left:2px ">preview</span>
                                </div>
                            </span>
                        </a>';
                if ($itemGroup->image)
                    return $html;
                else
                    return "-No Image-";
            })
            ->addColumn('background_image', function ($itemGroup) {
                $html = '<a class="transaction-image popup-image product-card"
                            href="' . asset($itemGroup->background_image) . '" target="_blank">
                            <img class="w-100 rounded-top" style="width: 5rem;height:5rem"
                                src="' . asset($itemGroup->background_image) . '" alt="">
                            <span class="transaction-preview" style="background:#00000060">
                                <div class="transaction-text">
                                    <em class="icon ni ni-eye "></em><span
                                        style="margin-left:2px ">preview</span>
                                </div>
                            </span>
                        </a>';
                if ($itemGroup->background_image)
                    return $html;
                else
                    return "-No Image-";
            })

            ->addColumn('status', function ($itemGroup) {
                $success = '<a class="btn btn-icon btn-success"
                href="' . route('restaurantOwner.toggleItemGroup', $itemGroup->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('restaurantOwner.toggleItemGroup', $itemGroup->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($itemGroup->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($itemGroup) {
                $html = '
                <ul class="nk-tb-actions gx-1" >
                 <li>
                    <div class="drodown"><a href="#"
                        class="dropdown-toggle btn btn-icon btn-trigger"
                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#editItemGroup' . $itemGroup->id . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit Item Category</span></span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteItemGroup' . $itemGroup->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete Item Category</span></span>
                                </a>
                            </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name',  'image', 'background_image', 'restaurant_category', 'status', 'action'])
            ->make(true);
    }

    public function getAllAddonCategories()
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $addonCategories = AddonCategory::where('restaurant_id', $restaurant->id)->with('restaurant')->get();
        return Datatables::of($addonCategories)
            ->addColumn('id', function ($addonCategory) {
                return $addonCategory->id;
            })
            ->addColumn('name', function ($addonCategory) {
                return $addonCategory->name;
            })
            ->addColumn('restaurant', function ($addonCategory) {
                if ($addonCategory->restaurant)
                    return $addonCategory->restaurant->name;
                else
                    return '--';
            })
            ->addColumn('type', function ($addonCategory) {
                return $addonCategory->type;
            })
            ->addColumn('description', function ($addonCategory) {
                return $addonCategory->description;
            })
            ->addColumn('status', function ($addonCategory) {
                $success = '<a class="btn btn-icon btn-success"
                href="' . route('restaurantOwner.toggleAddonCategory', $addonCategory->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('restaurantOwner.toggleAddonCategory', $addonCategory->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($addonCategory->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($addonCategory) {
                $html = '
                <ul class="nk-tb-actions gx-1" >
                 <li>
                    <div class="drodown"><a href="#"
                        class="dropdown-toggle btn btn-icon btn-trigger"
                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#editAddonCategory' . $addonCategory->id . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit Addon Category</span></span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteAddonCategory' . $addonCategory->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete Addon Category</span></span>
                                </a>
                            </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name',  'type', 'description', 'restaurant', 'status', 'action'])
            ->make(true);
    }

    public function getAllAddons()
    {
        $addons = Addon::with('addonCategory')->get();
        return Datatables::of($addons)
            ->addColumn('id', function ($addon) {
                return $addon->id;
            })
            ->addColumn('name', function ($addon) {
                return $addon->name;
            })
            ->addColumn('addon_category', function ($addon) {
                if ($addon->addonCategory)
                    return $addon->addonCategory->name;
                else
                    return '--';
            })
            ->addColumn('price', function ($addon) {
                return $addon->price;
            })

            ->addColumn('status', function ($addon) {
                $success = '<a class="btn btn-icon btn-success"
                href="' . route('restaurantOwner.toggleAddon', $addon->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('restaurantOwner.toggleAddon', $addon->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($addon->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($addon) {
                $html = '
                <ul class="nk-tb-actions gx-1" >
                 <li>
                    <div class="drodown"><a href="#"
                        class="dropdown-toggle btn btn-icon btn-trigger"
                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#editAddon' . $addon->id . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit Addon</span></span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteAddon' . $addon->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete Addon</span></span>
                                </a>
                            </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['name',  'price',  'addon_category', 'status', 'action'])
            ->make(true);
    }

    public function getAllItems()
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $items = Item::where('restaurant_id', $restaurant->id)->with('itemCategory', 'restaurant')->get();
        return Datatables::of($items)
            ->addColumn('id', function ($item) {
                return $item->id;
            })
            ->addColumn('name', function ($item) {
                return $item->name;
            })
            ->addColumn('price', function ($item) {
                return $item->selling_price ?? '0.00';
            })
            ->addColumn('itemCategory', function ($item) {
                if ($item->itemCategory)
                    return $item->itemCategory->name;
                else
                    return '--';
            })
            ->addColumn('restaurant', function ($item) {
                if ($item->restaurant)
                    return $item->restaurant->name;
                else
                    return '--';
            })
            ->addColumn('image', function ($item) {
                $html = '<a class="transaction-image popup-image product-card"
                            href="' . asset($item->image) . '" target="_blank">
                            <img class="w-100 rounded-top" style="width: 5rem;height:5rem"
                                src="' . asset($item->image) . '" alt="">
                            <span class="transaction-preview" style="background:#00000060">
                                <div class="transaction-text">
                                    <em class="icon ni ni-eye "></em><span
                                        style="margin-left:2px ">preview</span>
                                </div>
                            </span>
                        </a>';
                if ($item->image)
                    return $html;
                else
                    return "-No Image-";
            })

            ->addColumn('status', function ($item) {
                $success = '<a class="btn btn-icon btn-success"
                href="' . route('restaurantOwner.toggleItem', $item->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('restaurantOwner.toggleItem', $item->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($item->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($item) {
                $html = '
                <ul class="nk-tb-actions gx-1" >
                 <li>
                    <div class="drodown"><a href="#"
                        class="dropdown-toggle btn btn-icon btn-trigger"
                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                        <li>
                        <a href="' . route('restaurantOwner.editItem', $item->id) . '">
                            <em class="icon ni ni-edit"></em>
                            <span>Edit User</span>
                        </a>
                    </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteItem' . $item->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete Item</span>
                                </a>
                            </li>
                            <li>
                            <a href="' . route('restaurantOwner.viewItemAddonCategory', $item->id) . '">
                                <em class="icon ni ni-link"></em>
                                <span>Item Addon Category</span>
                            </a>
                        </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name',  'image', 'price', 'itemCategory', 'restaurant', 'status', 'action'])
            ->make(true);
    }


    public function getAllBanners()
    {
        $banners = Banner::with('city')->get();
        return Datatables::of($banners)
            ->addColumn('id', function ($banner) {
                return $banner->id;
            })
            ->addColumn('name', function ($banner) {
                return $banner->name;
            })
            ->addColumn('latitude', function ($banner) {
                return $banner->latitude;
            })
            ->addColumn('longitude', function ($banner) {
                return $banner->longitude;
            })
            ->addColumn('radius', function ($banner) {
                return $banner->radius;
            })
            ->addColumn('city', function ($banner) {
                if ($banner->city)
                    return $banner->city->name;
                else
                    return '--';
            })
            ->addColumn('image', function ($banner) {
                $html = '<a class="transaction-image popup-image product-card"
                            href="' . asset($banner->image) . '" target="_blank">
                            <img class="w-100 rounded-top" style="width: 5rem;height:5rem"
                                src="' . asset($banner->image) . '" alt="">
                            <span class="transaction-preview" style="background:#00000060">
                                <div class="transaction-text">
                                    <em class="icon ni ni-eye "></em><span
                                        style="margin-left:2px ">preview</span>
                                </div>
                            </span>
                        </a>';
                if ($banner->image)
                    return $html;
                else
                    return "-No Image-";
            })

            ->addColumn('status', function ($banner) {
                $success = '<a class="btn btn-icon btn-success"
                href="' . route('restaurantOwner.toggleBanner', $banner->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('restaurantOwner.toggleBanner', $banner->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($banner->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($banner) {
                $html = '
                <ul class="nk-tb-actions gx-1" >
                 <li>
                    <div class="drodown"><a href="#"
                        class="dropdown-toggle btn btn-icon btn-trigger"
                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                            <li>
                                <a href="' . route('restaurantOwner.editBanner', $banner->id) . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit User</span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteBanner' . $banner->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete Banner</span>
                                </a>
                            </li>

                        </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name',  'image', 'price', 'itemCategory', 'restaurant', 'status', 'action'])
            ->make(true);
    }

    public function getAllCoupons()
    {
        $coupons = Coupon::with('city')->get();
        return Datatables::of($coupons)
            ->addColumn('id', function ($coupon) {
                return $coupon->id;
            })
            ->addColumn('name', function ($coupon) {
                return $coupon->name;
            })
            ->addColumn('type', function ($coupon) {
                return $coupon->coupon_type;
            })
            ->addColumn('coupon_code', function ($coupon) {
                return $coupon->coupon_code;
            })
            ->addColumn('discount_type', function ($coupon) {
                return $coupon->discount_type;
            })
            ->addColumn('coupon_discount', function ($coupon) {
                return $coupon->coupon_discount;
            })
            ->addColumn('expiry_date', function ($coupon) {
                return $coupon->created_at->diffForHumans();
            })
            ->addColumn('image', function ($coupon) {
                $html = '<a class="transaction-image popup-image product-card"
                            href="' . asset($coupon->image) . '" target="_blank">
                            <img class="w-100 rounded-top" style="width: 5rem;height:5rem"
                                src="' . asset($coupon->image) . '" alt="">
                            <span class="transaction-preview" style="background:#00000060">
                                <div class="transaction-text">
                                    <em class="icon ni ni-eye "></em><span
                                        style="margin-left:2px ">preview</span>
                                </div>
                            </span>
                        </a>';
                if ($coupon->image)
                    return $html;
                else
                    return "-No Image-";
            })

            ->addColumn('status', function ($coupon) {
                $success = '<a class="btn btn-icon btn-success"
                href="' . route('restaurantOwner.toggleCoupon', $coupon->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('restaurantOwner.toggleCoupon', $coupon->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($coupon->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($coupon) {
                $html = '
                <ul class="nk-tb-actions gx-1" >
                 <li>
                    <div class="drodown"><a href="#"
                        class="dropdown-toggle btn btn-icon btn-trigger"
                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                            <li>
                                <a href="' . route('restaurantOwner.editCoupon', $coupon->id) . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit User</span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteCoupon' . $coupon->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete Coupon</span>
                                </a>
                            </li>

                        </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name',  'image', 'type', 'coupon_code', 'discount_type', 'coupon_discount', 'expiry_date', 'status', 'action'])
            ->make(true);
    }
}
