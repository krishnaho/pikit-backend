<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\AddonCategory;
use App\Models\Address;
use App\Models\Banner;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemGroup;
use App\Models\MarketCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ScratchCardWinner;
use App\Models\Slider;
use App\Models\Store;
use App\Models\StoreRating;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use App\Models\Category;
use App\Models\Review;
use App\Models\Notification;





use App\Models\DeviceToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CustomerApiController extends Controller
{
    private function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        try {

            $latFrom = deg2rad($latitudeFrom);
            $lonFrom = deg2rad($longitudeFrom);
            $latTo = deg2rad($latitudeTo);
            $lonTo = deg2rad($longitudeTo);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            return $angle * 6371;
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(array('message' => $latitudeFrom));
        } catch (Exception $e) {
            return redirect()->back()->with(array('message' => $e->getMessage()));
        } catch (\Throwable $th) {
            return redirect()->back()->with(array('message' => $th));
        }
    }

    public function coordinatesToAddress(Request $request)
    {
        $response = \Geocoder::getAddressForCoordinates($request->lat, $request->lng);
        if ($response) {

            $allowedTypes = ['plus_code', 'landmark', 'route', 'street_address', 'sublocality', 'subpremise', 'premise', 'street_number', 'floor', 'establishment', 'point_of_interest', 'parking', 'post_box', 'postal_town', 'room', 'bus_station', 'train_station', 'transit_station'];
            $finalAddress = '';
            $count = count($response['address_components']);
            $address_components = $response['address_components'];
            foreach ($address_components as $key => $address) {
                if (isset($address->types)) {
                    foreach ($address->types as $type) {
                        $allowed = false;
                        if (!in_array($type, $allowedTypes)) {
                            $allowed = true;
                        }
                    }
                    if ($allowed) {
                        $finalAddress .= $address->long_name;
                        if ($key + 1 != $count) {
                            $finalAddress .= ', ';
                        }
                    }
                }
            }
        } else {
            $finalAddress = false;
        }
        return response()->json($finalAddress);
    }

    public function getAllHomeDatas(Request $request)
    {
        $user = auth()->user();

        //Food
        $AllFoodBanners = Banner::where('restaurant_category_id', 2)->where('is_active', 1)->get();
        $foodBanners = collect([]);
        foreach ($AllFoodBanners as $item) {
            $distance = $this->getDistance(
                $request->latitude,
                $request->longitude,
                $item->latitude,
                $item->longitude
            );
            if ($distance <= $item->radius) {
                $foodBanners->push($item);
            }
        }

        $AllFoodItemCategories = ItemGroup::where('is_active', 1)->get();

        $AllFoodStores = Restaurant::where('restaurant_category_id', 1)
            ->get();

        $foodStores = collect([]);
        $PopularRestaurants = collect([]);
        foreach ($AllFoodStores as $item) {
            $item->is_favorited = $item->isFavorited();

            $distance = round($this->getDistance($request->latitude, $request->longitude, $item->latitude, $item->longitude), 2);

            if ($distance <= $item->delivery_radius) {
                $foodStores->push($item);
                if ($item->is_popular) {
                    $PopularRestaurants->push($item);
                }
            }
        }

        $AllFoodCoupons = Coupon::where('is_active', 1)->get();

        $AllDealsUnderIds = Item::where('selling_price', '<=', 30)->pluck('restaurant_id')->toArray();
        $AllDealsUnder30 = $foodStores->whereIn('id', $AllDealsUnderIds)->toArray();;

        //Grocery
        $AllGroceryBanners = Banner::where('restaurant_category_id', 2)
            ->where('is_active', 1)
            ->get();

        $AllGroceryStoresId = Restaurant::where('restaurant_category_id', 1)
            ->pluck('id')->toArray();

        $itemCategoryIds = Category::whereIn('restaurant_category_id', $AllGroceryStoresId)
            ->where('is_active', 1)->pluck('id')->toArray();
        $AllGroceryItemCategories = RestaurantCategory::where('is_active', 1)
            ->with(['itemCategories' => function ($query) use ($itemCategoryIds) {
                $query->whereIn('id', $itemCategoryIds)
                    ->where('is_active', 1);
            }])->get();

        $AllGroceryStores = Restaurant::where('restaurant_category_id', 2)
            ->get();

        $groceryStores = collect([]);

        foreach ($AllGroceryStores as $item) {

            $item->is_favorited = $item->isFavorited();
            $item->distance = round($this->getDistance(
                $request->latitude,
                $request->longitude,
                $item->latitude,
                $item->longitude
            ), 2);
            if ($item->distance <= $item->delivery_radius_in_km) {
                $groceryStores->push($item);
            }
        }

        $response = [
            'success' => true,
            'data' => [
                'AllFoodBanners' => $foodBanners,
                'AllFoodItemCategories' => $AllFoodItemCategories,
                'AllFoodStores' => $foodStores,
                'AllFoodCoupons' => $AllFoodCoupons,
                'AllPopularRestaurants' => $PopularRestaurants,
                'AllDealsUnder30' => $AllDealsUnder30,
                'AllGroceryBanners' => $AllGroceryBanners,
                'AllGroceryItemCategories' => $AllGroceryItemCategories,
                'AllGroceryStores' => $AllGroceryStores
            ]
        ];
        return response()->json($response);
    }

    public function getAllRestaurants(Request $request)
    {
        $AllRestaurants = Restaurant::where('is_active', 1)->get();
        $response = [
            'success' => true,
            'data' => [
                'AllRestaurants' => $AllRestaurants,
            ]
        ];
        return response()->json($response);
    }

    public function getSingleStore(Request $request)
    {

        $restaurant_id = $request->restaurant_id;


        if (is_numeric($restaurant_id)) {


            $restaurant = Restaurant::where('id', $request->restaurant_id)
                ->with('city')
                ->withCount('reviews')
                ->first();
        } else {

            $restaurant = Restaurant::where('slug', $request->restaurant_id)
                ->with('city')
                ->withCount('reviews')
                ->first();
        }
        
        if(!$restaurant){
            $response = [
                'success' => false,
                'data' => 'Store not found',
            ];
            return response()->json($response, 404);
        }



        $restaurant->is_favorited = $restaurant->isFavorited();
        $itemCategories = ItemCategory::where('restaurant_id', $restaurant->id)
            ->where('is_active', 1)
            ->whereHas('items', function ($query) use ($restaurant) {
                $query->where('restaurant_id', $restaurant->id)
                      ->where('is_active', 1);
            })
            ->with(['items' => function ($query) use ($restaurant) {
                $query->with(
                    'addonCategories',
                    'restaurant.city',
                    'addonCategories.addons'
                )->where('restaurant_id', $restaurant->id)
                 ->where('is_active', 1);
            }])->orderBy('order_column', 'asc')->get();

        $response = [
            'success' => true,
            'restaurant' => $restaurant,
            'itemCategories' => $itemCategories
        ];
        return response()->json($response);
    }

    public function getSingleStoreCategory(Request $request)
    {
        $itemCategories = ItemCategory::where('id', $request->restaurant_catgory_id)->where('is_active', 1)->with(['items' => function ($query) {
            $query->with('addonCategories', 'restaurant.city', 'addonCategories.addons')->where('is_active', 1);
        }])->first();
        $response = [
            'success' => true,
            'itemCategories' => $itemCategories
        ];
        return response()->json($response);
    }

    public function getSingleSlider(Request $request)
    {
        $slider = Banner::where('id', $request->id)->where('is_active', 1)->with('items', function ($query) {
            $query->with('restaurant', 'addonCategories', 'addonCategories.addons')->where('is_active', 1);
        })->with('restaurants', function ($query) {
            $query->orderBy('is_active', 'desc');
        })->first();

        $response = [
            'success' => true,
            'slider' => $slider,
        ];
        return response()->json($response);
    }

    public function getSingleBanner(Request $request)
    {
        $banners = Banner::where('id', $request->banner_id)->where('is_active', 1)->with('items', function ($query) {
            $query->with('restaurant', 'addonCategories', 'addonCategories.addons')->where('is_active', 1);
        })->with('restaurants', function ($query) {
            $query->orderBy('is_active', 'desc');
        })->first();
        $response = [
            'success' => true,
            'banners' => $banners
        ];
        return response()->json($response);
    }

    public function getSingleCategory(Request $request)
    {
        // Try ItemCategory first
        $category = ItemCategory::where('id', $request->category_id)
            ->where('is_active', 1)
            ->with(['items' => function ($query) {
                $query->where('is_active', 1)
                      ->whereHas('restaurant', function ($q) {
                          $q->where('is_active', 1);
                      })
                      ->with('restaurant');
            }])->first();

        // If not found, try ItemGroup
        if (!$category) {
            $category = ItemGroup::where('id', $request->category_id)
                ->where('is_active', 1)
                ->with(['items' => function ($query) {
                    $query->where('is_active', 1)
                          ->whereHas('restaurant', function ($q) {
                              $q->where('is_active', 1);
                          })
                          ->with('restaurant');
                }])->first();
        }

        $response = [
            'success' => true,
            'category' => $category
        ];
        return response()->json($response);
    }

    public function getSingleProduct(Request $request)
    {
        $product = Item::where('id', $request->product_id)
            ->where('is_active', 1)
            ->with(
                'restaurant',
                'addonCategories',
                'addonCategories.addons',
            )
            ->first();


        $response = [
            'success' => true,
            'data' => [
                'product' => $product,
                'success' => true,
            ]
        ];
        return response()->json($response);
    }

    public function getAllSearchDatas(Request $request)
    {
        $restaurants = Restaurant::where('is_active', 1)
            ->where('name', 'like', '%' . $request->input . '%')->get();

        $stores = collect([]);
        foreach ($restaurants as $item) {
            $item->is_favorited = $item->isFavorited();

            $distance = round($this->getDistance($request->latitude, $request->longitude, $item->latitude, $item->longitude), 2);

            $stores->push($item);
        }

        $items = Item::where('name', 'like', '%' . $request->input . '%')
            ->where('is_active', 1)->with(
                'restaurant',
            )->get();

        $response = [
            'success' => true,
            'data' => [
                'items' => $items,
                'restaurant' => $restaurants
            ]
        ];
        return response()->json($response);
    }

    public function getUserProfile(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $defaultAddress = Address::where('id', $user->default_address_id)->first();
        $response = [
            'success' => true,
            'data' => [
                'id' => $user->id,
                'auth_token' => $user->auth_token,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'default_address_id' => $user->default_address_id,
                'bank_details_id' => $user->bank_details_id,
                'defaultAddress' => $defaultAddress,
                'delivery_pin' => $user->delivery_pin,
                'wallet_balance' => $user->balance,
                'is_active' => $user->is_active,
                'referral_code' => $user->referral_code,
                'dob' => $user->dob,
            ],
        ];
        return response()->json($response, 201);
    }

    public function getUserData(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if ($user) {
            $response = [
                'success' => true,
                'user' => $user,
            ];
        } else {
            $response = [
                'success' => false,
                'user' => "No user found",
            ];
        }
        return response()->json($response, 201);
    }
    public function getFaqData(Request $request)
    {
        $faq = Faq::get();

        $response = [
            'success' => true,
            'Data' =>  $faq
        ];
        return response()->json($response, 201);
    }

    public function updateUserData(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if ($user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            if ($request->image && $request->image != null &&  $request->image != "null") {
                if ($user->image) {
                    @unlink(public_path($user->image));
                    $image = $request->file('image');
                    $imageName = time() . $image->getClientOriginalName();
                    $image->move(public_path('/assets/images/users/'), $imageName);
                    $user->image = 'https://zeato.howincloud.com/public/assets/images/users/' . $imageName;
                } else {
                    $image = $request->file('image');
                    $imageName = time() . $image->getClientOriginalName();
                    $image->move(public_path('/assets/images/users/'), $imageName);
                    $user->image = 'https://zeato.howincloud.com/public/assets/images/users/' . $imageName;
                }
            }
            $user->save();
            $defaultAddress = Address::where('id', $user->default_address_id)->first();
            $response = [
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'auth_token' => $user->auth_token,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'default_address_id' => $user->default_address_id,
                    'bank_details_id' => $user->bank_details_id,
                    'defaultAddress' => $defaultAddress,
                    'delivery_pin' => $user->delivery_pin,
                    'wallet_balance' => $user->balance,
                    'is_active' => $user->is_active,
                    'referral_code' => $user->referral_code,
                    'dob' => $user->dob,
                ],
            ];
        } else {
            $response = [
                'success' => false,
                'user' => "No user found",
            ];
        }
        return response()->json($response, 201);
    }

    public function getAllAddress(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $defaultAddress = Address::where('id', $user->default_address_id)->first();
        $addresses = Address::where('user_id', $user->id)->where('id', '!=', $user->default_address_id)->get();
        $user->save();
        $response = [
            'success' => true,
            'data' => [
                'addresses' => $addresses,
                'defaultAddress' => $defaultAddress,
            ],
        ];
        return response()->json($response, 201);
    }

    public function deleteAddress(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $address = Address::where('id', $request->address_id)->first();
        $address->delete();
        $defaultAddress = Address::where('id', $user->default_address_id)->first();
        $addresses = Address::where('user_id', $user->id)->where('id', '!=', $user->default_address_id)->get();
        $user->save();
        $response = [
            'success' => true,
            'data' => [
                'addresses' => $addresses,
                'defaultAddress' => $defaultAddress,
            ],
        ];
        return response()->json($response, 201);
    }

    public function addNewAddress(Request $request)
    {
        $address = Address::where('id', $request->address_id)->first();
        if ($address) {
            $address->user_id = $request->user_id;
            $address->address = $request->address;
            $address->landmark = $request->landmark;
            $address->house = $request->house;
            $address->latitude = $request->latitude;
            $address->longitude = $request->longitude;
            $address->tag = $request->addressType;
            $address->save();
        } else {
            $address = new Address();
            $address->user_id = $request->user_id;
            $address->address = $request->address;
            $address->landmark = $request->landmark;
            $address->house = $request->house;
            $address->latitude = $request->latitude;
            $address->longitude = $request->longitude;
            $address->tag = $request->addressType;
            $address->save();
        }
        $user = User::where('id', $request->user_id)->first();
        if ($user->default_address_id) {
            $user->default_address_id = $address->id;
            $user->save();
            $defaultAddress = Address::where('id', $user->default_address_id)->first();
            $addresses = Address::where('user_id', $user->id)->where('id', '!=', $user->default_address_id)->get();
        } else {
            $user->default_address_id = $address->id;
            $user->save();
            $defaultAddress = Address::where('id', $address->id)->first();
            $addresses = Address::where('user_id', $user->id)->where('id', '!=', $address->id)->get();
        }
        $response = [
            'success' => true,
            'data' => [
                'addresses' => $addresses,
                'defaultAddress' => $defaultAddress,
            ],
        ];
        return response()->json($response, 201);
    }



    public function changeDefaultAddress(Request $request)
    {
        $address = Address::where('id', $request->address_id)->first();
        $user = User::where('id', $request->user_id)->first();
        $user->default_address_id = $address->id;
        $user->save();
        $defaultAddress = Address::where('id', $user->default_address_id)->first();
        $addresses = Address::where('user_id', $user->id)->where('id', '!=', $user->default_address_id)->get();
        $response = [
            'success' => true,
            'data' => [
                'addresses' => $addresses,
                'defaultAddress' => $defaultAddress,
            ],
        ];
        return response()->json($response, 201);
    }

    public function getAllOrders(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $AllOrders = Order::where('user_id', $user->id)
                ->with(
                    'orderItems',
                    'restaurant',
                    'orderstatus'
                )->with(['orderItems' => function ($query) {
                    $query->with(
                        'order',
                        'item',
                        'orderitemaddons'
                    );
                }])->orderBy('created_at', 'desc')->get();
            $OnGoingOrders = Order::with(
                'orderItems',
                'restaurant',
                'orderstatus',
            )
                ->where('user_id', $user->id)
                ->whereIn('order_status_id', [1, 2, 3, 4, 5, 6, 9, 10, 11])->with(['orderItems' => function ($query) {
                    $query->with(
                        'order',
                        'item',
                        'orderitemaddons'
                    );
                }])->orderBy('created_at', 'desc')->get();
            $DeliveredOrders = Order::where('user_id', $user->id)
                ->with(
                    'orderItems',
                    'restaurant',
                    'orderstatus'
                )->whereIn('order_status_id', [7])->with(['orderItems' => function ($query) {
                    $query->with(
                        'order',
                        'item',
                        'orderitemaddons'
                    );
                }])->orderBy('created_at', 'desc')->get();
            $response = [
                'success' => true,
                'data' => [
                    'AllOrders' => $AllOrders,
                    'OnGoingOrders' => $OnGoingOrders,
                    'DeliveredOrders' => $DeliveredOrders,
                ],
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'User not found',
            ];
        }
        return response()->json($response, 201);
    }




    public function deleteUserData(Request $request)
    {
        $userId = $request->input('id');

        $user = User::find($userId);

        $user->phone = $user->phone . rand(0, 99999);

        $user->save();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }

    public function cancelNewOrder(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $data = $request->all();
            $data['adjustment'] = $data['adjustment'] ?? 'deposit';

            $validator = Validator::make($data, [
                'order_id' => 'required|integer',
                'cancellation_reason' => 'required|string|max:255',
                'adjustment' => 'required|string|in:deposit,withdraw',
                'message' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()], 400);
            }

            try {
                $order = Order::where('id', $request->order_id)->first();
                if (!$order) {
                    return response()->json(['success' => false, 'message' => 'Order not found'], 404);
                }

                // Cancel the order
                $order->order_status_id = 8;
                $order->order_cancelled_at = Carbon::now();
                $order->cancellation_reason = $request->cancellation_reason;
                $order->save();

                // Determine the amount based on payment_mode and walletamount
                $amount = null;
                $defaultNote = 'Refund for order cancellation #' . $order->unique_order_id;
                if ($order->payment_mode === 'ONLINE' || $order->payment_mode === 'WALLET') {
                    $amount = $order->total;
                } elseif ($order->payment_mode === 'COD' && $order->walletamount > 0) {
                    $amount = $order->walletamount;
                    $defaultNote = 'Refund for KoCash on order cancellation #' . $order->unique_order_id;
                }

                $note = $request->message ?: $defaultNote;

                // Log the values for debugging
                Log::info('Order refund amount:', ['amount' => $amount, 'payment_mode' => $order->payment_mode]);

                // Initialize response
                $response = [
                    'success' => true,
                    'message' => 'Order cancelled successfully.',
                    'data' => []
                ];

                // Attempt to add money to the wallet based on the adjustment type
                if ($amount !== null && $amount > 0) {
                    if ($data['adjustment'] == 'deposit') {
                        $user->deposit($amount, ['description' => $note]);
                    } else {
                        if ($user->balance >= $amount) {
                            $user->withdraw($amount, ['description' => $note]);
                        } else {
                            $response['message'] = 'Order cancelled, but insufficient balance for withdrawal.';
                        }
                    }
                }

                // Retrieve running and previous orders
                $runningOrders = Order::where('user_id', $user->id)
                    ->with('orderItems', 'user', 'restaurant', 'city', 'orderstatus')
                    ->whereIn('order_status_id', [1, 2, 3, 4, 5, 6, 9, 10, 11])
                    ->with(['orderItems' => function ($query) {
                        $query->with('order', 'item', 'orderitemaddons');
                    }])
                    ->orderBy('created_at', 'desc')
                    ->get();

                $previousOrders = Order::with('orderItems', 'user', 'restaurant', 'city', 'orderstatus')
                    ->where('user_id', $user->id)
                    ->where('order_status_id', 8)
                    ->with(['orderItems' => function ($query) {
                        $query->with('order', 'item', 'orderitemaddons');
                    }])
                    ->orderBy('created_at', 'desc')
                    ->get();

                $response['data'] = [
                    'previousOrders' => $previousOrders,
                    'runningOrders' => $runningOrders,
                ];


                $heading = 'Order Cancel Request Successful!';
                $message = 'Your order has been cancelled, and the restaurant has been notified. If payment was made, your refund will be processed shortly. For assistance, please contact customer support.';
                \App\Jobs\pushNotification::dispatch($user->id, $message, $heading, 'customer');


                // Notification message for cancelled order
                $vHeading = '❌ Order Cancelled';
                $vMessage = 'The customer has cancelled order #' . $order->unique_order_id . '. Please update your inventory accordingly and take no further action on this order. We apologize for any inconvenience this may cause.';

                // Send notification to restaurant vendors
                $vendors = $order->restaurant->users; // Assuming the restaurant has users/vendors related
                foreach ($vendors as $vendor) {
                    \App\Jobs\pushNotification::dispatch($vendor->id, $vMessage, $vHeading, 'vendor');
                }
            } catch (\Illuminate\Database\QueryException $qe) {
                Log::error('QueryException:', ['message' => $qe->getMessage()]);
                $response = [
                    'success' => false,
                    'message' => $qe->getMessage(),
                ];
            } catch (Exception $e) {
                Log::error('Exception:', ['message' => $e->getMessage()]);
                $response = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            } catch (\Throwable $th) {
                Log::error('Throwable:', ['message' => $th->getMessage()]);
                $response = [
                    'success' => false,
                    'message' => $th->getMessage(),
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'User not found',
            ];
        }

        return response()->json($response, 201);
    }


    public function getAllNotification(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user_id)->where('is_read', 0)->get();
        $data = $notifications;


        if ($notifications->isNotEmpty()) {
            $response = [
                'success' => true,
                'notifications' => $data->toArray(),
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "No notifications found for the user",
            ];
        }
        return response()->json($response, 200);
    }

    public function readNotification(Request $request)
    {
        $request->validate([
            'notification_id' => 'required',
        ]);
        $notification = Notification::find($request->notification_id);

        if ($notification) {
            $notification->is_read = 1;
            $notification->save();

            $response = [
                'success' => true,
                'notification' => $notification,
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Notification not found",
            ];
        }
        return response()->json($response, 200);
    }

    public function getWallet(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $transactions = $user->transactions->reverse();
            $transactionsArr = [];
            foreach ($transactions as $transaction) {
                $a = [
                    "type" => $transaction->type,
                    "amount" => $transaction->amount,
                    "description" => $transaction->meta['description'],
                    "date" => $transaction->created_at->format('d-m-Y')
                ];
                $transactionsArr[] = $a;
            }

            $balance = $user->balance;

            $response = [
                'success' => true,
                'data' => [
                    'balance' => $balance,
                    'transactions' => $transactionsArr
                ],
            ];
        } else {
            $response = [
                'success' => false,
            ];
        }
        return response()->json($response);
    }

    public function addToFavorite(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            if ($request->type === 'item') {
                $item = Item::where('id', $request->item_id)->first();
                $item->addFavorite();
            } else {
                $store = Store::where('id', $request->store_id)->first();
                $store->addFavorite();
            }
            $response = [
                'success' => true,
                'data' =>  'Favorite Added Succefully',
            ];
        } else {
            $response = [
                'success' => false,
                'data' =>  'User not found',
            ];
        }
        return response()->json($response);
    }
    public function removeFromFavorite(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            if ($request->type === 'item') {
                $item = Item::where('id', $request->item_id)->first();
                $item->removeFavorite();
            } else {
                $store = Store::where('id', $request->store_id)->first();
                $store->removeFavorite();
            }
            $response = [
                'success' => true,
                'data' =>  'Favorite Removed Succefully',
            ];
        } else {
            $response = [
                'success' => false,
                'data' =>  'User not found',
            ];
        }
        return response()->json($response);
    }
    public function toggleFavorite(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            Log::info($request->all());
            if ($request->type === 'item') {
                $item = Item::where('id', $request->item_id)->first();
                $item->toggleFavorite();
            } else {
                $restaurant = Restaurant::where('id', $request->restaurant_id)->first();
                $restaurant->toggleFavorite();
            }
            $response = [
                'success' => true,
                'data' => 'Favorite Toggled Succefully',
            ];
        } else {
            $response = [
                'success' => false,
                'data' =>  'User not found',
            ];
        }
        return response()->json($response);
    }

    public function getAllFavoriteItems(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $ids =  $user->favorite(restaurant::class)->values()->pluck('id')->toArray();
            $items = Restaurant::whereIn('id', $ids)->get();

            $response = [
                'success' => true,
                'data' => $items

            ];
        } else {
            $response = [
                'success' => false,
                'data' =>  'User not found',
            ];
        }
        return response()->json($response);
    }

    public function getSingleOrder(Request $request)
    {
        $order = Order::where('id', $request->order_id)->with(
            'restaurant',
            'user',
            'orderItems.item',
            'orderStatus'
        )
            ->with(['orderItems' => function ($query) {
                $query->with(
                    'order',
                    'item',
                    'orderitemaddons'
                )->orderBy('created_at', 'desc');
            }])->first();

        if ($order) {
            $response = [
                'success' => true,
                'data' => $order,
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'Order Not Found',
            ];
        }

        return response()->json($response);
    }


    public function updateFeedback(Request $request)
    {
        $auth = Auth::user();
        if ($auth) {
            $feedback = new Review();
            $feedback->feedback = $request->feedback;
            $order = Order::where('id', $request->order_id)->first();
            $feedback->user_id = $request->user_id;
            $feedback->order_id = $request->order_id;
            $feedback->restaurant_id = $order->restaurant_id;

            $feedback->rating = $request->rating;
            $feedback->save();

            $order->is_rated = 1;
            $order->save();

            $response = [
                'success' => true,
                'data' => 'one review saved succesfully'
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'User Not Found'
            ];
        }
        return response()->json($response);
    }

    public function getReviewSingleOrder(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $order = Order::where('user_id', $user->id)
                ->where('id', $request->order_id)->with(
                    'restaurant',
                    'city',
                    'orderstatus'
                )->orderBy('created_at', 'desc')->first();
            $response = [
                'success' => true,
                'order' => $order,
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'User not found',
            ];
        }
        return response()->json($response, 201);
    }
    public function getReOrderItems(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $order = Order::where('id', $request->order_id)->with('orderItems.orderItemAddons')->first();
            $itemIds = collect([]);
            $addonIds = collect([]);
            foreach ($order->orderItems as $item) {
                if ($item) {
                    $itemIds->push($item->item_id);
                }
                if ($item->orderItemAddons) {
                    foreach ($item->orderItemAddons as $addon) {
                        $ogAddon = Addon::where('id', $addon->addon_id)->first();
                        $addonCategory = AddonCategory::where('id', $ogAddon->addon_category_id)->first();
                        $newObject = (object)['item_id' => $item->item_id, 'addon_id' => $ogAddon->id, 'addon_category_id' => $addonCategory->id];
                        $addonIds->push($newObject);
                    }
                }
            }

            $items = Item::whereIn('id', $itemIds)->where('is_active', 1)->with('store', 'addonCategories', 'addonCategories.addons')->with('store', function ($query) {
                $query->orderBy('is_active', 'desc')->where('is_a ctive', 1);
            })->get();

            $itemWithAddon = collect([]);
            foreach ($items as $item) {
                foreach ($addonIds as $addonId) {
                    if ($addonId->item_id == $item->id) {
                        $itemObject = (object)['addon_category_id' => $addonId->addon_category_id, 'addon_id' => $addonId->addon_id]; // Create the new object
                        $itemWithAddon->push($itemObject);
                        $item->addedAddons = $itemWithAddon;
                    }
                }
            }

            $response = [
                'success' => true,
                'data' => [
                    'items' => $items,
                ]
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }


    public function getUserSCratchCards()
    {
        $user = Auth::user();
        if ($user) {
            $cards = ScratchCardWinner::where('user_id', $user->id)->with('scratchCard', 'order', 'user')->orderBy('is_collected', 'ASC')->get();
            $response = [
                'success' => true,
                'cards' => $cards,
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }

    public function scratchCardCollected(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $card = ScratchCardWinner::where('user_id', $user->id)->where('id', $request->id)->first();
            if ($card) {
                if (!$card->is_collected) {
                    $card->is_collected = 1;
                    $card->save();

                    $user->deposit($card->amount, ['description' => 'Scratch Card Reward']);

                    $response = [
                        'success' => true,
                        'card' => $card,
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'data' => 'Scratch Card is redeemed',
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'data' => 'No Scratch Card Winning Found',
                ];
            }
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }

    public function updateDeviceToken(Request $request)
    {
        $user = auth()->user();
        $checkDeviceToken = DeviceToken::where('token', $request->device_token)->first();
        if ($user && !$checkDeviceToken && $request->device_token) {
            $newDevice = new DeviceToken;
            $newDevice->user_id = $user->id;
            $newDevice->token = $request->device_token;
            $newDevice->save();

            return response(true);
        } else {
            return response(false);
        }
        return response()->json($response);
    }
}
