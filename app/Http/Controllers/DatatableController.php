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
use App\Models\Review;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DatatableController extends Controller
{
    public function getAllUsers()
    {
        $users = User::get();
        return Datatables::of($users)
            ->addColumn('id', function ($user) {
                return $user->id;
            })
            ->addColumn('name', function ($user) {
                if (isset($user->name))
                    return $user->name;
                else
                    return 'Name Not Given';
            })
            ->addColumn('address', function ($user) {
                if ($user->address)
                    return $user->address;
                else
                    return "-no address-";
            })
            ->addColumn('phone', function ($user) {
                if ($user->phone)
                    return $user->phone;
                else
                    return '-no phone-';
            })
            ->addColumn('roles', function ($user) {
                $roles = "No Role Assigned";
                foreach ($user->roles as $role) {
                    $roles = '<span class="badge bg-info">' . $role->name . '</span>';
                }
                return $roles;
            })
            ->addColumn('status', function ($user) {

                $success = '<a class="btn btn-icon btn-success"
                href="' . route('admin.toggleUser', $user->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleUser', $user->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($user->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($user) {

                $editBranch = '
                    <ul class="nk-tb-actions gx-1">
                     <li>
                        <div class="drodown"><a href="#"
                            class="dropdown-toggle btn btn-icon btn-trigger"
                            data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <ul class="link-list-opt no-bdr">
                                <li>
                                    <a href="' . route('admin.editUser', $user->id) . '">
                                        <em class="icon ni ni-edit"></em>
                                        <span>Edit User</span>
                                    </a>
                                </li>
                                <li>
                                    <a data-bs-toggle="modal"
                                        data-bs-target="#deleteUser' . $user->id . '">
                                        <em class="icon ni ni-trash"></em>
                                        <span>Delete User</span></span>
                                    </a>
                                </li>';
                if (count($user->roles) > 0 && $user->roles[0]->name == 'Branch Admin') {
                    $editBranch .= '
                                                <li>
                                                    <a href="' . route('admin.manageBranch', $user->id) . '">
                                                        <em class="icon ni ni-account-setting-alt"></em>
                                                        <span>Manage Branch</span>
                                                    </a>
                                                </li>';
                }
                if (count($user->roles) > 0 && $user->roles[0]->name == 'Customer') {
                    $editBranch .= '
                                    <li>
                                    <a  href="' . route('admin.viewSingleCustomerUserSchemes', $user->id) . '">
                                        <em class="icon ni ni-eye-fill"></em>
                                        <span>View User Schemes</span></span>
                                        </a>
                                </li>';
                }

                $editBranch .= '
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>';

                return $editBranch;
            })
            ->rawColumns(['id', 'name', 'address',  'phone', 'roles', 'status', 'action'])
            ->make(true);
    }

    // //customer
    public function getAllCustomers()
    {
        $users = User::role('Customer')->get();
        return Datatables::of($users)
            ->addColumn('id', function ($user) {
                return $user->id;
            })
            ->addColumn('name', function ($user) {
                return $user->name;
            })
            ->addColumn('address', function ($user) {
                if ($user->address)
                    return $user->address;
                else
                    return "-no address-";
            })
            ->addColumn('phone', function ($user) {
                return $user->phone;
            })
            ->addColumn('status', function ($user) {

                $success = '<a class="btn btn-icon btn-success"
                href="' . route('admin.toggleUser', $user->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleUser', $user->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($user->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($user) {
                $html = '
                <ul class="nk-tb-actions gx-1">
                 <li>
                    <div class="drodown"><a href="#"
                        class="dropdown-toggle btn btn-icon btn-trigger"
                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                            <li>
                                <a href="' . route('admin.editUser', $user->id) . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit User</span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteUser' . $user->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete User</span></span>
                                </a>
                            </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name', 'address',  'phone', 'status',  'action'])
            ->make(true);
    }



    public function getAllRestaurantOwners()
    {
        $users = User::role('Restaurant Owner')->get();
        return Datatables::of($users)
            ->addColumn('id', function ($user) {
                return $user->id;
            })
            ->addColumn('name', function ($user) {
                return $user->name;
            })
            ->addColumn('address', function ($user) {
                if ($user->address)
                    return $user->address;
                else
                    return "-no address-";
            })
            ->addColumn('phone', function ($user) {
                return '+971' . $user->phone;
            })

            ->addColumn('login', function ($user) {
                $restaurantIds = $user->restaurants->pluck('id')->toArray();

                if (!empty($restaurantIds)) {
                    $html = '<div class="btn-group">
                    <a type="button" href="' . route('admin.impersonate', $user->id) . '"
                        class="btn btn-dark btn-sm" title="Login to Restaurant">
                        <em class="icon ni ni-edit"></em></a>
                </div>';
                    return  $html;
                } else {
                    return 'No Restaurant Assigned';
                }
            })
            ->addColumn('status', function ($user) {

                $success = '<a class="btn btn-icon btn-success"
                href="' . route('admin.toggleUser', $user->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleUser', $user->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($user->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($user) {
                $html = '
                <ul class="nk-tb-actions gx-1" >
                 <li>
                    <div class="drodown"><a href="#"
                        class="dropdown-toggle btn btn-icon btn-trigger"
                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <ul class="link-list-opt no-bdr">
                            <li>
                                <a href="' . route('admin.editUser', $user->id) . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit User</span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteUser' . $user->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete User</span></span>
                                </a>
                            </li>
                            <li>
                                <a href="' . route('admin.manageRestaurant', $user->id) . '">
                                    <em class="icon ni ni-account-setting-alt"></em>
                                    <span>Manage Restaurant</span>
                                </a>
                            </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name', 'address', 'phone', 'login', 'status', 'action'])
            ->make(true);
    }



    public function getAllRestaurantCategories()
    {
        $restaurantCategories = RestaurantCategory::get();
        return Datatables::of($restaurantCategories)
            ->addColumn('id', function ($restaurantCategory) {
                return $restaurantCategory->id;
            })
            ->addColumn('name', function ($restaurantCategory) {
                return $restaurantCategory->name;
            })
            ->addColumn('image', function ($restaurantCategory) {
                $html = '<a class="transaction-image popup-image product-card"
                            href="' . asset($restaurantCategory->image) . '" target="_blank">
                            <img class="w-100 rounded-top" style="width: 5rem;height:5rem"
                                src="' . asset($restaurantCategory->image) . '" alt="">
                            <span class="transaction-preview" style="background:#00000060">
                                <div class="transaction-text">
                                    <em class="icon ni ni-eye "></em><span
                                        style="margin-left:2px ">preview</span>
                                </div>
                            </span>
                        </a>';
                if ($restaurantCategory->image)
                    return $html;
                else
                    return "-No Image-";
            })
            ->addColumn('status', function ($restaurantCategory) {

                $success = '<a class="btn btn-icon btn-success"
                href="' . route('admin.toggleRestaurantCategory', $restaurantCategory->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleRestaurantCategory', $restaurantCategory->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($restaurantCategory->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('action', function ($restaurantCategory) {
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
                                    data-bs-target="#editRestaurantCategory' . $restaurantCategory->id . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit Category</span></span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteRestaurantCategory' . $restaurantCategory->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete Category</span></span>
                                </a>
                            </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name',  'image', 'status',  'action'])
            ->make(true);
    }

    public function getAllReviews()
    {
        $reviews = Review::get();
        return Datatables::of($reviews)
            ->addColumn('id', function ($review) {

                return $review->id;
            })
            ->addColumn('order_id', function ($review) {
                if ($review->order) {
                    $orderId = '<a 
                    href="' . route('admin.orderView', $review->order->unique_order_id) . '">' . $review->order->unique_order_id . '</a>';
                } else {
                    $orderId = '--';
                }

                return $orderId;
            })
            ->addColumn('user_name', function ($review) {
                return $review->user ? $review->user->name : "--";
            })
            ->addColumn('store_name', function ($review) {
                return $review->restaurant ? $review->restaurant->name : '--';
            })
            ->addColumn('review', function ($review) {
                return $review->feedback ? $review->feedback : 'No Review';
            })
            ->addColumn('rating', function ($review) {
                return $review->rating;
            })
            ->addColumn('rated_at', function ($review) {
                return $review->created_at->diffForHumans();
            })
            ->rawColumns(['id', 'order_id', 'user_name',  'store_name', 'review',  'rating', 'rated_at'])
            ->make(true);
    }

    public function getAllCities()
    {
        $cities = City::get();
        return Datatables::of($cities)
            ->addColumn('id', function ($city) {
                return $city->id;
            })
            ->addColumn('name', function ($city) {
                return $city->name;
            })
            ->addColumn('latitude', function ($city) {
                return $city->latitude;
            })
            ->addColumn('city_user', function ($city) {
                return $city->longitude;
            })
            ->addColumn('status', function ($city) {
                $success = '<a class="btn btn-icon btn-success"
                href="' . route('admin.toggleCity', $city->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleCity', $city->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                if ($city->is_active == 1)
                    return $success;
                else
                    return $danger;
            })
            ->addColumn('city_user', function ($city) {
                $city_user = '<a class="btn btn-info" data-bs-toggle="modal"
                data-bs-target="#cityUserModel' . $city->id . '">
                <span>View Users</span></span>
                </a>';
                return $city_user;
            })
            ->addColumn('action', function ($city) {
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
                                    data-bs-target="#editCity' . $city->id . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit city</span></span>
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="modal"
                                    data-bs-target="#deleteCity' . $city->id . '">
                                    <em class="icon ni ni-trash"></em>
                                    <span>Delete city</span></span>
                                </a>
                            </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';

                return $html;
            })
            ->rawColumns(['id', 'name', 'latitude', 'longitude', 'surge', 'status', 'city_user',  'action'])
            ->make(true);
    }


    public function getAllRestaurants()
    {
        $restaurants = Restaurant::with('restaurantCategory', 'city')->get();
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
                href="' . route('admin.toggleRestaurant', $restaurant->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleRestaurant', $restaurant->id) . '">
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
                                <a   href="' . route('admin.editRestaurant', $restaurant->id) . '" >
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
                             <li>
                                <a  href="' . route('admin.restaurantCategories', $restaurant->id) . '"   >
                                    <em class="icon ni ni-edit"></em>
                                    <span>Sort Category</span></span>
                                </a>
                            </li>
                            <li>
                                <a   href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#showQRCode_' . $restaurant->id . '" >
                                    <em class="icon ni ni-eye"></em>
                                    <span>Show QRCode</span></span>
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
        $itemCategories = ItemCategory::with('restaurant')->get();
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
                href="' . route('admin.toggleItemCategory', $itemCategory->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleItemCategory', $itemCategory->id) . '">
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
                href="' . route('admin.toggleItemGroup', $itemGroup->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleItemGroup', $itemGroup->id) . '">
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
        $addonCategories = AddonCategory::with('restaurant')->get();
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
                href="' . route('admin.toggleAddonCategory', $addonCategory->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleAddonCategory', $addonCategory->id) . '">
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
                href="' . route('admin.toggleAddon', $addon->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleAddon', $addon->id) . '">
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
        $items = Item::with('itemCategory', 'restaurant')->get();
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
                href="' . route('admin.toggleItem', $item->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleItem', $item->id) . '">
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
                        <a href="' . route('admin.editItem', $item->id) . '">
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
                            <a href="' . route('admin.viewItemAddonCategory', $item->id) . '">
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
                href="' . route('admin.toggleBanner', $banner->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleBanner', $banner->id) . '">
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
                                <a href="' . route('admin.editBanner', $banner->id) . '">
                                    <em class="icon ni ni-edit"></em>
                                    <span>Edit Banner</span>
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
                href="' . route('admin.toggleCoupon', $coupon->id) . '">
                <em class="icon ni ni-power"></em>
                </a>';
                $danger = ' <a class="btn btn-icon btn-danger"
                href="' . route('admin.toggleCoupon', $coupon->id) . '">
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
                                <a href="' . route('admin.editCoupon', $coupon->id) . '">
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





public function detailedReviewAjax(Request $request)
{
    $user = Auth::user();

    $restaurantIds = DB::table('restaurant_user')
        ->where('user_id', $user->id)
        ->pluck('restaurant_id');

    $reviews = DB::table('reviews')
        ->join('users', 'users.id', '=', 'reviews.user_id')
        ->leftJoin('orders', 'orders.id', '=', 'reviews.order_id')
        ->join('restaurants', 'restaurants.id', '=', 'reviews.restaurant_id')
        ->whereIn('reviews.restaurant_id', $restaurantIds)
        ->select(
            'reviews.id',
            'reviews.feedback',
            'reviews.rating',
            'reviews.created_at',
            'users.name as user_name',
            'orders.unique_order_id'
        );

    return DataTables::of($reviews)
        ->addColumn('id', fn($review) => $review->id)
        ->addColumn('order_id', function ($review) {
            return $review->unique_order_id
                ? '<a href="' . route('admin.orderView', $review->unique_order_id) . '">' . $review->unique_order_id . '</a>'
                : '--';
        })
        ->addColumn('user_name', fn($review) => $review->user_name ?? '--')
        ->addColumn('review', fn($review) => $review->feedback ? $review->feedback : 'No Review')
        ->addColumn('rating', fn($review) => $review->rating)
        ->addColumn('rated_at', fn($review) => Carbon::parse($review->created_at)->diffForHumans())
        ->rawColumns(['order_id'])
        ->make(true);
}

public function usersWithOrdersAjax(Request $request)
{
    $user = Auth::user();
    $restaurantIds = DB::table('restaurant_user')
        ->where('user_id', $user->id)
        ->pluck('restaurant_id');

    if ($restaurantIds->isEmpty()) {
        return DataTables::of(collect([]))->make(true);
    }
    $subQuery = DB::table('users')
        ->join('orders', 'users.id', '=', 'orders.user_id')
        ->whereIn('orders.restaurant_id', $restaurantIds)
        ->select(
            'users.id as user_id',
            'users.name',
            'users.phone',
            DB::raw('COUNT(orders.id) as order_count'),
            DB::raw('MAX(orders.created_at) as last_order_date')
        )
        ->groupBy('users.id', 'users.name', 'users.phone');

    $users = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
        ->mergeBindings($subQuery);

    return DataTables::of($users)
        ->addColumn('user_id', fn($u) => $u->user_id)
        ->addColumn('name', fn($u) => '<strong>' . e($u->name) . '</strong>')
        ->addColumn('phone', fn($u) => $u->phone ?: '--')
        ->addColumn('order_count', fn($u) => $u->order_count)
        ->addColumn('last_order', fn($u) => $u->last_order_date ? \Carbon\Carbon::parse($u->last_order_date)->diffForHumans() : '--')
        ->rawColumns(['name'])
        ->make(true);
}




}
