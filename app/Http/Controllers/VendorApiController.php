<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuthException;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\completedOrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\AddonCategory;
use App\Models\Category;
use App\Models\OrderItem;
use App\Jobs\sendToFleet;
use App\Models\OrderItemAddon;
use App\Models\Address;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\StoreRating;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Notification;
use GuzzleHttp\Client;
use App\Jobs\SendInteraktMessage;
use Barryvdh\Snappy\Facades\SnappyImage;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Spatie\PdfToImage\Pdf as PdfToImagePdf;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class VendorApiController extends Controller
{
    private function getToken($user)
    {
        $token = null;

        try {
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'User is invalid..',
                    'token' => $token,
                ]);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Token creation failed',
            ]);
        }
        return $token;
    }

    public function loginVendor(Request $request)
    {
        $user = User::where('email', $request->email)->with('restaurants')->first();
        if ($user &&  \Hash::check($request->password, $user->password)) {
            if ($user->hasRole('Restaurant Owner')) {
                $authStoreIds = $user->restaurants->pluck('id')->first();
                $store = Restaurant::where('id', $authStoreIds)->first();
                $defaultAddress = Address::where('id', $user->default_address_id)->first();
                $token = self::getToken($user);
                $user->auth_token = $token;
                $user->save();
                $response = [
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'auth_token' => $user->auth_token,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'store_id' => $store->id ?? null,
                        'default_address_id' => $user->default_address_id,
                        'defaultAddress' => $defaultAddress,
                    ],
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'These credentials do not match our records'
                ];
            }
        } else {
            $response = ['success' => false,  'message' => 'These credentials do not match our records'];
        }
        return response()->json($response, 201);
    }

    public function RegisterVendorapp(Request $request)
    {
        $user = user::where('email', $request->email)->first();
        if ($user) {
            $token = self::getToken($user);
            $user->assignRole('Vendor');
            $user->password = Hash::make($request->password);
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->auth_token = $token;
            $user->store_name = $request->store_name;
            $user->store_owner_name = $request->store_owner_name;
            $user->image = $request->image;
            $user->license_document = $request->license_document;
            $user->gst = $request->gst;
            $user->license_number = $request->license_number;
            $user->save();
            $response = [
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'auth_token' => $user->auth_token,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ];
        } else {
            $user = new User();
            $user->assignRole('Vendor');
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->store_name = $request->store_name;
            $user->password = Hash::make($request->password);
            $user->image = $request->image;
            $user->license_document = $request->license_document;
            $user->gst = $request->gst;
            $user->license_number = $request->license_number;
            $user->save();
            $token = self::getToken($user);
            $user->auth_token = $token;
            $user->save();

            $response = [
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'auth_token' => $user->auth_token,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ];
        }

        return response()->json($response, 201);
    }



    public function getVendorHomeData(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $authStoreIds = Auth::user()->restaurants->pluck('id')->first();
            if ($authStoreIds) {
                $store = Restaurant::with("city")->where('id', $authStoreIds)->first();
                $todaySales = Order::where('restaurant_id', $store->id)->whereDate('created_at', Carbon::today())->where('order_status_id', 7)->sum('total');
                $totalSales = Order::where('restaurant_id', $store->id)->where('order_status_id', 7)->sum('total');

                $totalOrders = Order::where('restaurant_id', $store->id)->where('order_status_id', 7)->count();
                $currentMonthOrders = Order::where('restaurant_id', $store->id)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->where('order_status_id', 7)
                    ->count();
                $lastMonth = Carbon::now()->subMonth();
                $lastMonthOrders = Order::where('restaurant_id', $store->id)
                    ->whereYear('created_at', $lastMonth->year)
                    ->whereMonth('created_at', $lastMonth->month)
                    ->where('order_status_id', 7)
                    ->count();

                if ($lastMonthOrders == 0) {
                    $monthlyGrowthPerc = $currentMonthOrders > 0 ? 100 : 0;
                } else {
                    $monthlyGrowthPerc = (($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100;
                }

                // $todaysOrder =  Order::where('restaurant_id', $authStoreIds)->get();
                // $todaysOrderCount =  Order::where('restaurant_id', $authStoreIds)->whereDate('created_at', Carbon::today())->where('order_status_id', 5)->count();
                // $turn_over = $orders->sum('restaurant_total');
                // $newOrders = $orders->where('order_status_id', 3)->count();
                // $acceptedOrders = $orders->whereIn('order_status_id', [2, 3, 7])->count();

                $dateRange = Carbon::today()->subDays(7);
                $weeklyOrders = Order::where('restaurant_id', $authStoreIds)
                    ->where('created_at', '>=', $dateRange)
                    ->where('order_status_id', '7')
                    ->select('restaurant_total', 'created_at')
                    ->get();
                for ($i = 0; $i <= 6; $i++) {
                    $amount[] = $weeklyOrders->where('created_at', '>=', Carbon::today()->subDays($i)->startOfDay())->where('created_at', '<=', Carbon::today()->subDays($i)->endOfDay())->count();
                    if ($i == 0) {
                        $days[] = "Today";
                    } else  if ($i == 1) {
                        $days[] = "Yesterday";
                    } else {
                        $days[] = Carbon::today()->subDays($i)->format('D');
                    }
                }
                foreach ($amount as $amt) {
                    $amtArr[] = $amt;
                }

                $amtArr = array_reverse($amtArr);
                foreach ($days as $key => $day) {
                    $dayArr[] = $day;
                }

                $dayArr = array_reverse($dayArr);
                $chartData = [];
                $weekData = new  Collection();
                for ($i = 0; $i <= 6; $i++) {
                    $chartData[] = [
                        $dayArr[$i] => $amtArr[$i],
                    ];
                }
                $weekData->push($chartData[0] + $chartData[1] + $chartData[2] + $chartData[3] + $chartData[4] + $chartData[5] + $chartData[6]);
                $response = [
                    'success' => true,
                    'data' => [
                        'store' => $store,
                        'todaySales' => $todaySales,
                        'totalSales' => $totalSales,
                        'currentMonthOrders' => $currentMonthOrders,
                        'lastMonthOrders' => $lastMonthOrders,
                        'totalOrders' => $totalOrders,
                        'monthlyGrowthPerc' => $monthlyGrowthPerc,
                        // 'todaysOrder' => $todaysOrder,

                        'weekData' => $weekData[0],
                    ],
                ];
            } else {
                $response = [
                    'success' => false,
                    'data' => 'No Restaurant Found',
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

    public function getNewOrder(Request $request)
    {
        $authStoreIds = Auth::user()->restaurants->pluck('id')->first();
        if ($authStoreIds) {
            $order = Order::whereIn('order_status_id', [1, 2])->where('restaurant_id', $authStoreIds)->orderBy('id', 'DESC')->with(
                'orderItems.item',
                'orderItems.orderItemAddons'
            )->get();
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
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }

    public function getOngoingOrder(Request $request)
    {
        $authStoreIds = Auth::user()->restaurants->pluck('id')->first();
        if ($authStoreIds) {
            $order = Order::whereIn('order_status_id', [2, 3, 4, 5])->orderBy('id', 'DESC')->where('restaurant_id', $authStoreIds)->with('restaurant', 'user', 'orderItems', 'orderItems.orderItemAddons', 'orderStatus')->get();
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
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }

    public function getSingleVendorOrder(Request $request)
    {
        $authStoreIds = Auth::user()->restaurants->pluck('id')->first();
        if ($authStoreIds) {
            $order = Order::where('id', $request->order_id)->where('restaurant_id', $authStoreIds)->with('restaurant', 'user', 'orderItems', 'orderItems.orderItemAddons', 'orderStatus')->first();
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
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }



    public function acceptCancelOrder(Request $request)
    {

        $user = auth()->user();
        if ($user) {
            $order = Order::where('id', $request->order_id)->first();
            if ($request->type == "accept" && $order->order_status_id == 1) {
                $order->order_status_id = 3;
                $order->order_accepted_at = Carbon::now();
                $order->save();


                $restaurant = Restaurant::find($order->restaurant_id);

                $heading = 'Order Confirmed';
                $message =  $restaurant->name . ' has started preparing your order. Our delivery executive will pick it up soon.';
                \App\Jobs\pushNotification::dispatch($order->user_id, $message, $heading, 'customer');

                $vendors = $order->restaurant->users;
                $vHeading = '⏰ Delivery Reminder
';
                $vMessage = 'This is a friendly reminder that your delivery for order #' . $order->unique_order_id .
                    '. is approaching. Please ensure everything is ready by [delivery time]. If you need more time, simply tap "Need More Time" to adjust the schedule.';
                foreach ($vendors as $vendor) {
                    \App\Jobs\pushNotification::dispatch($vendor->id, $vMessage, $vHeading, 'vendor');
                }


                $notification = new Notification();
                $notification->user_id = $order->user_id;
                $notification->title = $heading;
                $notification->body = $message;
                $notification->save();

                $order->order_prepairing_time = isset($request->order_prepairing_time) ? $request->order_prepairing_time : 5;
                $order->eta = (isset($order->eta) && $order->eta !== null) ? $order->eta + $order->order_prepairing_time : 0;
                $order->save();
                $response = [
                    'success' => true,
                    'message' => "Order Accepted Successfully",
                ];
            } else if ($request->type == "cancel" && $order->order_status_id != 7) {
                $order = Order::where('id', $request->order_id)->first();
                $order->order_status_id = 8;
                $order->order_cancelled_at = Carbon::now();
                $order->cancellation_reason = $request->cancellation_reason;
                $order->save();
                $vendorHeading = 'Order Cancellation Successful!';
                $vendorMessage = 'The customer has been notified, and the order #' . $order->unique_order_id . ' has been cancelled. ';

                \App\Jobs\pushNotification::dispatch($order->restaurant_id, $vendorMessage, $vendorHeading, 'vendor');


                $heading = 'Order Cancelled';
                $message = 'We regret to inform you that your order' . $order->unique_order_id . 'from restaurant ' . $order->restaurant->name . ' has been cancelled. Please check the "My Orders" section for the reason. If paid, your refund will be processed shortly. For any assistance, contact customer support.';
                \App\Jobs\pushNotification::dispatch($user->id, $message, $heading, 'customer');

                $response = [
                    'success' => true,
                    'message' => "Order Cancelled Successfully",
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => "Order Status Not Accepted",
                ];
            }
        } else {
            $response = [
                'success' => false,
                'data' => "User Not Found"
            ];
        }

        return response()->json($response, 201);
    }

    private function formatRemainingTime($remainingSeconds)
    {
        $hours = floor($remainingSeconds / 3600);
        $minutes = floor(($remainingSeconds % 3600) / 60);
        $seconds = $remainingSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }



    public function getExtraTime(Request $request)
    {
        $user = auth()->user();
        if ($user) {

            $order = Order::where('id', $request->order_id)->first();
            $order->extra_time_accepted_at = Carbon::now();
            $order->order_prepairing_extra_time = $request->extra_prepairing_time;
            $format = 'H:i:s'; // Assuming the format is hours:minutes:seconds
            $preparingTime = $request->extra_prepairing_time;
            $order->total_exta_time = $order->extra_time_accepted_at->copy()->addMinutes($preparingTime);
            $remainingTime = $order->total_exta_time->diffInSeconds(Carbon::now());

            // Format remaining time
            $formattedRemainingTime = $this->formatRemainingTime($remainingTime);

            // Save the formatted remaining time to the database
            $order->formatted_remaining_extra_time = $formattedRemainingTime;

            $order->save();

            $response = [
                'success' => true,
                'data' => [
                    'message' => "Order Accepted Successfully",
                    'remaining_time' => $formattedRemainingTime,
                ],
            ];
        } else {
            $response = [
                'success' => false,
                'data' => "User Not Found"
            ];
        }

        return response()->json($response, 201);
    }

    public function addExtraTime(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $order = Order::where('id', $request->order_id)->first();
            $order->need_more_time_at = Carbon::now();
            $order->order_prepairing_extra_time = $request->extra_prepairing_time;
            $order->eta = $order->eta + $request->extra_prepairing_time;
            $order->save();
            $response = [
                'success' => true,
                'message' => "Extra Time Added Successfully",
            ];
        } else {
            $response = [
                'success' => false,
                'data' => "User Not Found"
            ];
        }

        return response()->json($response, 201);
    }


    public function addOutOfStockTime(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            // Find the category by ID
            $category = ItemCategory::where('id', $request->itemId)->first();
            if ($category) {
                // Set the out-of-stock times for the category
                $outOfStockTime = $request->out_of_stock_time; // Use the same out_of_stock_time
                $category->out_stock_time_at = Carbon::now();
                $category->out_of_stock_time = $outOfStockTime;
                $category->save();

                // Deactivate all related items using the relationship
                foreach ($category->items as $item) {
                    $item->out_stock_time_at = Carbon::now();
                    $item->out_of_stock_time = $outOfStockTime;
                    $item->save();
                }

                $response = [
                    'success' => true,
                    'message' => "Out of Stock Time for Category and Items Added Successfully",
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => "Category Not Found",
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => "User Not Found",
            ];
        }

        return response()->json($response, 201);
    }
    public function addOutOfStockTimeForItems(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $category = Item::where('id', $request->itemId)->first();

            $category->out_stock_time_at = Carbon::now();
            $category->out_of_stock_time = $request->out_of_stock_time;
            $category->save();
            $response = [
                'success' => true,
                'message' => "Out of Stock Time Added Successfully",
            ];
        } else {
            $response = [
                'success' => false,
                'data' => "User Not Found"
            ];
        }

        return response()->json($response, 201);
    }

    public function addOutOfStockTimeForAddonCategory(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $category = AddonCategory::where('id', $request->itemId)->first();

            $category->out_stock_time_at = Carbon::now();
            $category->out_of_stock_time = $request->out_of_stock_time;
            $category->save();

            $response = [
                'success' => true,
                'message' => "Out of Stock Time Added Successfully",
            ];
        } else {
            $response = [
                'success' => false,
                'data' => "User Not Found"
            ];
        }

        return response()->json($response, 201);
    }

    public function addOutOfStockTimeForAddAddon(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $category = Addon::where('id', $request->itemId)->first();

            $category->out_stock_time_at = Carbon::now();
            $category->out_of_stock_time = $request->out_of_stock_time;
            $category->save();
            $response = [
                'success' => true,
                'message' => "Out of Stock Time Added Successfully",
            ];
        } else {
            $response = [
                'success' => false,
                'data' => "User Not Found"
            ];
        }

        return response()->json($response, 201);
    }




    public function readyToPickupOrder(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $order = Order::where('id', $request->order_id)->first();
            $order->order_status_id = 4;
            $order->order_ready_to_pickup_at = Carbon::now();
            $order->save();
            $heading = 'Order Ready';
            $message = 'Your order  is ready and waiting for the delivery executive to pick it up. It’ll be on its way shortly! ';
            \App\Jobs\pushNotification::dispatch($user->id, $message, $heading, 'customer');


            sendToFleet::dispatch($order->id, 0);

            activity()->performedOn($order)
                ->causedBy(Auth::user())
                ->withProperties(['orderstatus' => '4'])
                ->log('Order marked as Ready to Pickup by Store owner');
            $response = [
                'success' => true,
                'data' => "Order Cooking Completed Successfully",
            ];
        } else {
            $response = [
                'success' => false,
                'data' =>  "User Not Found"
            ];
        }
        return response()->json($response, 201);
    }

    public function changeOrderStatusToPickedUp(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $order = Order::where('id', $request->order_id)->first();
            $order->order_status_id = 6;
            $order->order_ready_to_pickup_at = Carbon::now();
            $order->save();

            $heading = 'Order Picked Up';
            $message = 'Your order Picked Up';
            \App\Jobs\pushNotification::dispatch($user->id, $message, $heading, 'customer');

            activity()->performedOn($order)
                ->causedBy(Auth::user())
                ->withProperties(['orderstatus' => '6'])
                ->log('Order marked as Picked Up by Store owner');
            $response = [
                'success' => true,
                'data' => "Order Picked Up Successfully",
            ];
        } else {
            $response = [
                'success' => false,
                'data' =>  "User Not Found"
            ];
        }
        return response()->json($response, 201);
    }


    public function changeOrderStatusToDelivered(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $order = Order::where('id', $request->order_id)->first();
            $order->order_status_id = 7;
            $order->order_delivered_at = Carbon::now();
            $order->save();

            $heading = 'Order Completed';
            $message = 'Your order is Delivered';
            \App\Jobs\pushNotification::dispatch($user->id, $message, $heading, 'customer');

            activity()->performedOn($order)
                ->causedBy(Auth::user())
                ->withProperties(['orderstatus' => '7'])
                ->log('Order marked as Delivered by Store owner');
            $response = [
                'success' => true,
                'data' => "Order Delivered Successfully",
            ];
        } else {
            $response = [
                'success' => false,
                'data' =>  "User Not Found"
            ];
        }
        return response()->json($response, 201);
    }

    public function getCancelledOrder(Request $request)
    {
        $authStoreIds = Auth::user()->restaurants->pluck('id')->first();
        if ($authStoreIds) {
            $order = Order::where('restaurant_id', $authStoreIds)->where('order_status_id', 8)->with('restaurant', 'user', 'orderItems', 'orderItems.orderItemAddons', 'orderStatus')->get();
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
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }
    public function getCompletedOrder(Request $request)
    {
        $authStoreIds = Auth::user()->restaurants->pluck('id')->first();
        if ($authStoreIds) {
            $order = Order::where('restaurant_id', $authStoreIds)->where('order_status_id', 7)->with('restaurant', 'user', 'orderItems', 'orderItems.orderItemAddons', 'orderStatus')->get();
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
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }


    public function getAllItemCategories()
    {
        $user = Auth::user();
        if ($user) {
            $item_categries = ItemCategory::where('is_active', 1)->get();
            $response = [
                'success' => true,
                'data' => $item_categries,
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }

    function getAllAddonCategories()
    {
        $user = Auth::user();
        if ($user) {
            $categories = AddonCategory::where('is_active', 1)->get();
            $response = [
                'success' => true,
                'data' => $categories,
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }


    public function addNewItem(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $item = new Item();
            $item->name = $request->name;
            $item->restaurant_id = $request->store_id;
            $item->item_category_id = $request->item_category_id;
            $item->description = $request->description;
            $item->selling_price = $request->selling_price;
            $item->market_price = $request->market_price;
            $item->min_quantity = $request->min_quantity;
            $item->max_quantity = $request->max_quantity;
            if ($request->is_popular) {
                $item->is_popular = 1;
            } else {
                $item->is_popular = 0;
            }

            if ($request->is_recommended) {
                $item->is_recommended = 1;
            } else {
                $item->is_recommended = 0;
            }

            if ($request->is_veg) {
                $item->is_veg = 1;
            } else {
                $item->is_veg = 0;
            }
            if ($request->file('image')) {
                $image = $request->file('image');
                $imageName = time() . $image->getClientOriginalName();
                $image->move(public_path('/assets/images/items/'), $imageName);
                $item->image = 'https://zeato.howincloud.com/public/assets/images/items/' . $imageName;
            }
            $item->is_active = 1;
            $item->save();
            $response = [
                'success' => true,
                'data' => 'Item Created Successfully',
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }

    public function addNewCategory(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $restaurant = Restaurant::where('id', $request->store_id)->first();
            $category = new ItemCategory();
            $category->name = $request->name;
            $category->restaurant_id = $request->store_id;
            $category->restaurant_category_id = $restaurant->restaurant_category_id;
            $category->description = $request->description;
            if ($request->file('image')) {
                $image = $request->file('image');
                $imageName = time() . $image->getClientOriginalName();
                $image->move(public_path('/assets/images/itemcategory/'), $imageName);
                $category->image = 'https://zeato.howincloud.com/public/assets/images/itemcategory/' . $imageName;
            }
            $category->is_active = 1;
            $category->save();
            $response = [
                'success' => true,
                'data' => 'Item Category Created Successfully',
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }

    public function addNewAddon(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $addon = new Addon();
            $addon->name = $request->name;
            $addon->addon_category_id = $request->addon_category_id;
            $addon->price = $request->price;
            $addon->is_active = 0;
            $addon->save();
            $response = [
                'success' => true,
                'data' => 'Addon Created Successfully',
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }

    public function addNewAddonCategory(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $category = new AddonCategory();
            $category->name = $request->name;
            $category->restaurant_id = $request->store_id;
            $category->description = $request->description;
            $category->type = $request->type;
            $category->is_active = 0;
            $category->save();
            $response = [
                'success' => true,
                'data' => 'Addon Category Created Successfully',
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }
    public function getVendorUserProfile(Request $request)
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
                'defaultAddress' => $defaultAddress
            ],
        ];
        return response()->json($response, 201);
    }
    public function getUserdata(Request $request)
    {
        if ($request->filled('name')) {
            $users = User::where('name', 'like', '%' . $request->name . '%')
                ->limit(10)
                ->get();

            if ($users->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => $users->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'auth_token' => $user->auth_token,
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'default_address_id' => $user->default_address_id,
                            'location' => $user->location,
                            'building' => $user->building,
                            'room_no' => $user->room_no,
                        ];
                    }),
                ], 200);
            }
        }
        if ($request->filled('phone')) {
            $user = User::where('phone', $request->phone)->first();

            if ($user) {
                return response()->json([
                    'success' => true,
                    'data' => [[
                        'id' => $user->id,
                        'auth_token' => $user->auth_token,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'default_address_id' => $user->default_address_id,
                        'location' => $user->location,
                        'building' => $user->building,
                        'room_no' => $user->room_no,
                    ]],
                ], 200);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 404);
    }


    public function vendorOrderUserRegister(Request $request)
    {
        $existingUser = User::where('phone', $request->phone)->first();

        if ($existingUser) {
            $token = self::getToken($existingUser);
            $existingUser->name = $request->name;
            $existingUser->auth_token = $token;
            $existingUser->location = $request->location;
            $existingUser->building = $request->building;
            $existingUser->room_no = $request->room_no;
            $existingUser->save();

            $response = [
                'success' => true,
                'data' => [
                    'id' => $existingUser->id,
                    'auth_token' => $existingUser->auth_token,
                    'name' => $existingUser->name,
                    'phone' => $existingUser->phone,
                    'location' => $existingUser->location,
                    'building' => $existingUser->building,
                    'room_no' => $existingUser->room_no,
                ],
            ];
        } else {
            $user = new User();
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->location = $request->location;
            $user->building = $request->building;
            $user->room_no = $request->room_no;
            $user->save();

            $token = self::getToken($user);
            $user->auth_token = $token;
            $user->save();

            $response = [
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'auth_token' => $user->auth_token,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'location' => $user->location,
                    'building' => $user->building,
                    'room_no' => $user->room_no,
                    'default_address_id' => $user->default_address_id,
                ],
            ];
        }

        return response()->json($response, 201);
    }
    public function vendorStoreUpdate(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $store = Restaurant::where('id', $request->store_id)->first();
            $store->name = $request->name;
            $store->description = $request->description;
            $store->min_order_price = $request->min_order_price;
            if ($request->file('image')) {
                $file = $request->file('image');
                $imageName = time() . $file->getClientOriginalName();
                $file->move(public_path('/restaurant/'), $imageName);
                $store->image = 'restaurant/' . $imageName;
            }
            $store->save();
            $response = [
                'success' => true,
                'store' => $store,
            ];
        } else {
            $response = [
                'success' => true,
                'data' => "User not found",
            ];
        }
        return response()->json($response, 201);
    }
    public function getSingleStore(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $store = Restaurant::where('id', $request->store_id)->first();
            $response = [
                'success' => true,
                'store' => $store,
            ];
        } else {
            $response = [
                'success' => true,
                'data' => "User not found",
            ];
        }
        return response()->json($response, 201);
    }
    public function getStorePayoutData(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $pendingPayout = Restaurant::where('id', $request->store_id)->where('is_accepted', 1)
                ->whereHas('orders', function ($q) {
                    $q->where('order_status_id', 7)->where('is_payout_released', 0);
                })->with(['orders' => function ($q) {
                    $q->where('order_status_id', 7)->where('is_payout_released', 0);
                }])->first();

            $completedPayout = Restaurant::where('id', $request->store_id)->where('is_accepted', 1)
                ->whereHas('orders', function ($q) {
                    $q->where('order_status_id', 7)->where('is_payout_released', 1);
                })->with(['orders' => function ($q) {
                    $q->where('order_status_id', 7)->where('is_payout_released', 1);
                }])
                ->first();

            $dateRange = Carbon::today()->subDays(7);
            $weeklyOrders = Order::where('restaurant_id', $request->store_id)
                ->where('created_at', '>=', $dateRange)
                ->where('order_status_id', '7')->where('is_payout_released', 1)
                ->select('restaurant_total', 'created_at')
                ->get();
            for ($i = 0; $i <= 6; $i++) {
                $amount[] = $weeklyOrders->where('created_at', '>=', Carbon::today()->subDays($i)->startOfDay())->where('created_at', '<=', Carbon::today()->subDays($i)->endOfDay())->count();
                if ($i == 0) {
                    $days[] = "Today";
                } else  if ($i == 1) {
                    $days[] = "Yesterday";
                } else {
                    $days[] = Carbon::today()->subDays($i)->format('D');
                }
            }
            foreach ($amount as $amt) {
                $amtArr[] = $amt;
            }

            $amtArr = array_reverse($amtArr);
            foreach ($days as $key => $day) {
                $dayArr[] = $day;
            }

            $dayArr = array_reverse($dayArr);
            $chartData = [];
            $weekData = new  Collection();
            for ($i = 0; $i <= 6; $i++) {
                $chartData[] = [
                    $dayArr[$i] => $amtArr[$i],
                ];
            }
            $weekData->push($chartData[0] + $chartData[1] + $chartData[2] + $chartData[3] + $chartData[4] + $chartData[5] + $chartData[6]);
            $response = [
                'success' => true,
                'data' => [
                    'pendingPayout' => $pendingPayout,
                    'completedPayout' => $completedPayout,
                    'weekData' => $weekData[0],
                ]
            ];
        } else {
            $response = ['success' => false, 'data' => 'No User Found'];
        }
        return response()->json($response);
    }

    public function getCompletedOrderWithDate(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $from_date = Carbon::parse($request->from_date);
            $to_date = Carbon::parse($request->to_date);
            $orders = Order::where('restaurant_id', $request->store_id)->with('restaurant', 'user', 'orderItems', 'orderItems.orderItemAddons', 'orderStatus')
                ->where('order_status_id', '7')
                ->whereDate('created_at', '>=', $from_date)
                ->whereDate('created_at', '<=', $to_date)->get();
            $total_amount = Order::where('restaurant_id', $request->store_id)->with('restaurant', 'user', 'orderItems', 'orderItems.orderItemAddons', 'orderStatus')
                ->where('order_status_id', '7')
                ->whereDate('created_at', '>=', $from_date)
                ->whereDate('created_at', '<=', $to_date)->sum('total');
            $response = [
                'success' => true,
                'data' => [
                    'orders' => $orders,
                    'total_amount' => $total_amount,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
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

    public function getCancelledOrderWithDate(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $from_date = Carbon::parse($request->from_date);
            $to_date = Carbon::parse($request->to_date);
            $orders = Order::where('restaurant_id', $request->store_id)->with('restaurant', 'user', 'orderItems', 'orderItems.orderItemAddons', 'orderStatus')
                ->where('order_status_id', '8')
                ->whereDate('created_at', '>=', $from_date)
                ->whereDate('created_at', '<=', $to_date)->get();
            $total_amount = Order::where('restaurant_id', $request->store_id)->with('restaurant', 'user', 'orderItems', 'orderItems.orderItemAddons', 'orderStatus')
                ->where('order_status_id', '8')
                ->whereDate('created_at', '>=', $from_date)
                ->whereDate('created_at', '<=', $to_date)->sum('total');
            $response = [
                'success' => true,
                'data' => [
                    'orders' => $orders,
                    'total_amount' => $total_amount,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
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

    public function exportCompletedOrders(Request  $request)
    {
        $user = Auth::user();
        if ($user) {
            $from = Carbon::parse($request->from);
            $to = Carbon::parse($request->to);
            $store_id = $request->store_id;
            $export = new completedOrdersExport();
            ob_end_clean();
            $export->recieveData($from, $to, $store_id);
            return Excel::download($export, 'completedOrders.xlsx');
        }
    }

    public function getAllStoreRatings(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $ratings = StoreRating::where('restaurant_id', $request->store_id)->with('user', 'store', 'order')->get();
            $response = [
                'success' => true,
                'data' => $ratings
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }



    public function getAllInventoryItems(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $categories = ItemCategory::where('restaurant_id', $request->store_id)->get();
            $items = Item::where('restaurant_id', $request->store_id)->get();
            $addoncategories = AddonCategory::where('restaurant_id', $request->store_id)->get();
            $addoncategoryIds = AddonCategory::where('restaurant_id', $request->store_id)->pluck('id')->toArray();
            $addons = Addon::whereIn('addon_category_id', $addoncategoryIds)->get();


            $response = [
                'success' => true,
                'categories' => $categories,
                'items' => $items,
                'addoncategories' => $addoncategories,
                'addons' => $addons
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }




    public function getAllCategory(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $category = ItemCategory::where('restaurant_id', $request->store_id)
                ->get();

            if ($category) {
                $response = [
                    'success' => true,
                    'data' => $category,
                ];
            } else {
                $response = [
                    'success' => false,
                    'data' => 'category Not Found',
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

    public function getAllItems(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $item = Item::where('restaurant_id', $request->store_id)
                ->get();

            if ($item) {
                $response = [
                    'success' => true,
                    'data' => $item,
                ];
            } else {
                $response = [
                    'success' => false,
                    'data' => 'category Not Found',
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

    public function getAllAddoncategory(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $addoncategory = AddonCategory::where('restaurant_id', $request->store_id)
                ->get();

            if ($addoncategory) {
                $response = [
                    'success' => true,
                    'data' => $addoncategory,
                ];
            } else {
                $response = [
                    'success' => false,
                    'data' => 'category Not Found',
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
    public function getAllAddon(Request $request)
    {
        $user = Auth::user();

        if ($user) {

            $addoncategoryIds = AddonCategory::where('restaurant_id', $request->store_id)->pluck('id')->toArray();
            $addon = Addon::whereIn('addon_category_id', $addoncategoryIds)
                ->get();
            if ($addon) {
                $response = [
                    'success' => true,
                    'data' => $addon,
                ];
            } else {
                $response = [
                    'success' => false,
                    'data' => 'category Not Found',
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


    public function toggleAddon(Request $request)
    {
        $addon = Addon::find($request->id);
        if ($addon) {
            $addon->toggleActive();
            $addon->save();
            $response = [
                'success' => true,
            ];
            return response()->json($response, 201);
        } else {
            return (['error' => 'Something Went Wrong']);
        }
    }

    public function toggleItem(Request $request)
    {
        $addon = Item::find($request->id);
        if ($addon) {
            $addon->toggleActive();
            $addon->save();
            $response = [
                'success' => true,
            ];
            return response()->json($response, 201);
        } else {
            return (['error' => 'Something Went Wrong']);
        }
    }

    public function toggleCategory(Request $request)
    {
        // Find the category by ID
        $category = ItemCategory::find($request->id);
        if ($category) {
            // Check if the category is being reactivated (made active)
            $wasInactive = !$category->is_active; // Assuming 'is_active' is a boolean field

            // Toggle the active status
            $category->toggleActive();

            // If the category was inactive and is now being activated, set out_of_stock_time to null
            if ($wasInactive && $category->is_active) {
                // Set out_of_stock_time and out_stock_time_at to null for the category
                $category->out_of_stock_time = null;
                $category->out_stock_time_at = null;
                $category->save();

                // Set out_of_stock_time and out_stock_time_at to null for all related items
                foreach ($category->items as $item) {
                    $item->out_of_stock_time = null;
                    $item->out_stock_time_at = null;
                    $item->save();
                }
            } else {
                // Save the category without nullifying times if deactivating
                $category->save();
            }

            // Return success response
            $response = [
                'success' => true,
            ];
            return response()->json($response, 201);
        } else {
            return response()->json(['error' => 'Something Went Wrong'], 400);
        }
    }



    public function toggleAddonCategory(Request $request)
    {
        $addon = AddonCategory::find($request->id);
        if ($addon) {
            $addon->toggleActive();
            $addon->save();
            $response = [
                'success' => true,
            ];
            return response()->json($response, 201);
        } else {
            return (['error' => 'Something Went Wrong']);
        }
    }
    // UPDATES ENDS

    // public function printOrderBillInstructions($order_id)
    // {
    //     // $user = Auth::user();
    //     $user = User::where('id', 2)->first();
    //     if ($user) {
    //         $order = Order::where('id', $order_id)->with('restaurant', 'orderitems')->first();
    //         $newLineString = '▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃▃' . PHP_EOL;
    //         $instructions = [];

    //         array_push($instructions, ['type' => 'newline']);
    //         array_push($instructions, ['type' => 'align', 'alignment' => 'CENTER']);
    //         array_push($instructions, ['type' => 'bold', 'value' => true]);

    //         array_push($instructions, [
    //             'type' => 'text',
    //             'text' => $order->restaurant->name,
    //             'options' => [
    //                 'widthtimes' => 3,
    //                 'heigthtimes' => 3,
    //                 'fonttype' => 1,
    //             ]
    //         ]);

    //         array_push($instructions, ['type' => 'bold', 'value' => false]);
    //         array_push($instructions, ['type' => 'newline']);

    //         array_push($instructions, [
    //             'type' => 'text',
    //             'text' =>  $order->created_at->format('D, M d, Y • h:i:s A'),
    //             'options' => [
    //                 'widthtimes' => 0.1,
    //                 'heigthtimes' => 0.1,
    //                 'fonttype' => 1,
    //             ]
    //         ]);

    //         array_push($instructions, ['type' => 'text', 'text' => $newLineString]);
    //         array_push($instructions, ['type' => 'bold', 'value' => true]);

    //         array_push($instructions, [
    //             'type' => 'text',
    //             'text' => 'Order# ' . $order->unique_order_id,
    //             'options' => [
    //                 'widthtimes' => 0,
    //                 'heigthtimes' => 1,
    //                 'fonttype' => 5,
    //             ]
    //         ]);

    //         array_push($instructions, ['type' => 'bold', 'value' => false]);
    //         array_push($instructions, ['type' => 'text', 'text' => $newLineString]);

    //         if ($order->customer_phone != null) {
    //             array_push($instructions, [
    //                 'type' => 'column',
    //                 'columnWidths' => [16, 16],
    //                 'alignments' => ['LEFT', 'RIGHT'],
    //                 'contents' => ['Customer phone:', '' . $order->customer_phone],
    //                 'options' => [
    //                     'widthtimes' => 0,
    //                     'heigthtimes' => 1,
    //                     'fonttype' => 5,
    //                 ]
    //             ]);
    //         }


    //         if ($order->address != null) {
    //             array_push($instructions, [
    //                 'type' => 'text',
    //                 'text' => 'Address: ' . $order->address,
    //                 'options' => [
    //                     'widthtimes' => 0.05,
    //                     'heigthtimes' => 0.05,
    //                     'fonttype' => 5,
    //                 ]
    //             ]);
    //         }



    //         array_push($instructions, [
    //             'type' => 'column',
    //             'columnWidths' => [16, 18],
    //             'alignments' => ['LEFT', 'RIGHT'],
    //             'contents' => ['Payment Mode:', '' . $order->payment_mode],
    //             'options' => [
    //                 'widthtimes' => 0,
    //                 'heigthtimes' => 1,
    //                 'fonttype' => 5,
    //             ]
    //         ]);

    //         // array_push($instructions, [
    //         //     'type' => 'column',
    //         //     'columnWidths' => [16, 16],
    //         //     'alignments' => ['LEFT', 'RIGHT'],
    //         //     'contents' => ['Delivery', 'Delivery']
    //         // ]);

    //         array_push($instructions, ['type' => 'text', 'text' => $newLineString]);

    //         foreach ($order->orderitems as $orderitem) {
    //             // Prepare the item name and quantity
    //             $itemName = $orderitem->name . ' x ' . $orderitem->quantity;

    //             // If the order item has addons, append them to the name
    //             if (isset($orderitem->orderitemaddons) && $orderitem->orderitemaddons->isNotEmpty()) {
    //                 $addons = '(' . implode(', ', array_column($orderitem->orderitemaddons->toArray(), 'name')) . ')';
    //                 $itemName .= ' ' . $addons;
    //             }

    //             // Calculate the total price including the addons
    //             $totalPrice = $orderitem->price * $orderitem->quantity;
    //             if (isset($orderitem->orderitemaddons) && !empty($orderitem->orderitemaddons)) {
    //                 $addonPrices = array_sum(array_column($orderitem->orderitemaddons->toArray(), 'price'));
    //                 $totalPrice += $addonPrices;
    //             }

    //             // Add the order item column to the instructions
    //             array_push($instructions, [
    //                 'type' => 'column',
    //                 'columnWidths' => [20, 12],
    //                 'alignments' => ['LEFT', 'RIGHT'],
    //                 'contents' => [$itemName, 'AED ' . number_format($totalPrice, 2)],
    //                 'options' => [
    //                     "heigthtimes" => 1,
    //                     "widthtimes" => 0,
    //                 ]
    //             ]);
    //         }


    //         array_push($instructions, ['type' => 'bold', 'value' => true]);
    //         array_push($instructions, ['type' => 'text', 'text' => $newLineString]);

    //         // Subtotal
    //         array_push($instructions, [
    //             'type' => 'column',
    //             'columnWidths' => [16, 16],
    //             'alignments' => ['LEFT', 'RIGHT'],
    //             'contents' => [
    //                 'Sub Total',
    //                 'AED ' . number_format((float) $order->sub_total, 2, '.', '')
    //             ],
    //             'options' => [
    //                 "heigthtimes" => 1,
    //                 "widthtimes" => 0,
    //             ]
    //         ]);

    //         // Delivery Fee
    //         array_push($instructions, [
    //             'type' => 'column',
    //             'columnWidths' => [16, 16],
    //             'alignments' => ['LEFT', 'RIGHT'],
    //             'contents' => [
    //                 'Delivery Fee',
    //                 $order->delivery_charge != 0 ? 'AED ' . number_format((float) $order->delivery_charge, 2, '.', '') : 'FREE'
    //             ],
    //             'options' => [
    //                 "heigthtimes" => 1,
    //                 "widthtimes" => 0,
    //             ]
    //         ]);

    //         // Service Fee
    //         array_push($instructions, [
    //             'type' => 'column',
    //             'columnWidths' => [16, 16],
    //             'alignments' => ['LEFT', 'RIGHT'],
    //             'contents' => [
    //                 'Service Fee',
    //                 'AED ' . number_format((float) $order->platform_fee, 2, '.', '')
    //             ],
    //             'options' => [
    //                 "heigthtimes" => 1,
    //                 "widthtimes" => 0,
    //             ]
    //         ]);

    //         // Total
    //         array_push($instructions, [
    //             'type' => 'column',
    //             'columnWidths' => [16, 16],
    //             'alignments' => ['LEFT', 'RIGHT'],
    //             'contents' => [
    //                 'TOTAL',
    //                 'AED ' . number_format((float) $order->total, 2, '.', '')
    //             ],
    //             'options' => [
    //                 "heigthtimes" => 1,
    //                 "widthtimes" => 0,
    //             ]
    //         ]);


    //         // array_push($instructions, ['type' => 'bold', 'value' => false]);
    //         array_push($instructions, ['type' => 'text', 'text' => $newLineString]);
    //         array_push($instructions, ['type' => 'bold', 'value' => true]);

    //         array_push($instructions, [
    //             'type' => 'text',
    //             'text' => 'Vat (Incl),Thank you for ordering with us! We look forward to serving you again. Have a great day!',
    //             'options' => [
    //                 'widthtimes' => 0.1,
    //                 'heigthtimes' => 0.2,
    //                 'fonttype' => 3,
    //             ]
    //         ]);

    //         // array_push($instructions, ['type' => 'bold', 'value' => false]);
    //         // array_push($instructions, ['type' => 'logo']);
    //         // array_push($instructions, ['type' => 'newline']);

    //         return $instructions;
    //     }
    // }

    public function printOrderBillInstructions($order_id)
    {
        $user = User::where('id', 2)->first();
        if (!$user) return [];

        $order = Order::where('id', $order_id)->with('restaurant', 'orderitems')->first();
        if (!$order) return [];

        $singleLine = str_repeat('-', 32) . PHP_EOL;
        $instructions = [];

        // Header
        $instructions[] = ['type' => 'newline'];
        $instructions[] = ['type' => 'align', 'alignment' => 'CENTER'];
        $instructions[] = ['type' => 'bold', 'value' => true];
        $instructions[] = [
            'type' => 'text',
            'text' => $order->restaurant->name,
            'options' => ['widthtimes' => 2, 'heigthtimes' => 2, 'fonttype' => 1],
        ];
        $instructions[] = ['type' => 'bold', 'value' => false];
        $instructions[] = ['type' => 'newline'];

        // Date &Order ID
        $instructions[] = [
            'type' => 'text',
            'text' => $order->created_at->format('D, M d, Y  h:i A'),
            'options' => ['widthtimes' => 0, 'heigthtimes' => 0, 'fonttype' => 0],
        ];
        $instructions[] = ['type' => 'text', 'text' => $singleLine];
        $instructions[] = ['type' => 'bold', 'value' => true];
        $instructions[] = [
            'type' => 'text',
            'text' => 'Order# ' . $order->unique_order_id,
            'options' => ['widthtimes' => 0, 'heigthtimes' => 0, 'fonttype' => 0],
        ];
        $instructions[] = ['type' => 'bold', 'value' => false];
        $instructions[] = ['type' => 'text', 'text' => $singleLine];

        // Payment Mode
        $instructions[] = [
            'type' => 'column',
            'columnWidths' => [16, 16],
            'alignments' => ['LEFT', 'RIGHT'],
            'contents' => ['Payment Mode:', $order->payment_mode],
        ];

        $instructions[] = ['type' => 'text', 'text' => $singleLine];

        // Items Header
        $instructions[] = ['type' => 'bold', 'value' => true];
        $instructions[] = [
            'type' => 'column',
            'columnWidths' => [14, 6, 12],
            'alignments' => ['LEFT', 'CENTER', 'RIGHT'],
            'contents' => ['Item', 'Qty', 'Price'],
        ];
        $instructions[] = ['type' => 'bold', 'value' => false];

        // Items
        foreach ($order->orderitems as $orderitem) {
            $itemName = $orderitem->name;
            if ($orderitem->orderitemaddons && $orderitem->orderitemaddons->isNotEmpty()) {
                $addonNames = implode(', ', array_column($orderitem->orderitemaddons->toArray(), 'name'));
                $itemName .= " ($addonNames)";
            }

            $qty = $orderitem->quantity;
            $totalPrice = $orderitem->price * $qty;
            if ($orderitem->orderitemaddons && $orderitem->orderitemaddons->isNotEmpty()) {
                $addonPrices = array_sum(array_column($orderitem->orderitemaddons->toArray(), 'price'));
                $totalPrice += $addonPrices;
            }

            $instructions[] = [
                'type' => 'column',
                'columnWidths' => [14, 6, 12],
                'alignments' => ['LEFT', 'CENTER', 'RIGHT'],
                'contents' => [$itemName, 'x' . $qty, 'AED ' . number_format($totalPrice, 2)],
            ];
        }

        $instructions[] = ['type' => 'text', 'text' => $singleLine];

        // Totals
        $instructions[] = ['type' => 'bold', 'value' => true];
        $instructions[] = [
            'type' => 'column',
            'columnWidths' => [16, 16],
            'alignments' => ['LEFT', 'RIGHT'],
            'contents' => ['Sub Total', 'AED ' . number_format($order->sub_total, 2)],
        ];
        $instructions[] = [
            'type' => 'column',
            'columnWidths' => [16, 16],
            'alignments' => ['LEFT', 'RIGHT'],
            'contents' => ['Delivery Fee', $order->delivery_charge != 0 ? 'AED ' . number_format($order->delivery_charge, 2) : 'FREE'],
        ];
        $instructions[] = [
            'type' => 'column',
            'columnWidths' => [16, 16],
            'alignments' => ['LEFT', 'RIGHT'],
            'contents' => ['Service Fee', 'AED ' . number_format($order->platform_fee, 2)],
        ];
        $instructions[] = [
            'type' => 'column',
            'columnWidths' => [16, 16],
            'alignments' => ['LEFT', 'RIGHT'],
            'contents' => ['TOTAL', 'AED ' . number_format($order->total, 2)],
        ];
        $instructions[] = ['type' => 'bold', 'value' => false];
        $instructions[] = ['type' => 'text', 'text' => $singleLine];

        // Customer details
        $instructions[] = ['type' => 'bold', 'value' => true];
        $instructions[] = ['type' => 'text', 'text' => 'Customer Details'];
        $instructions[] = ['type' => 'bold', 'value' => false];

        if ($order->user->name) {
            $instructions[] = [
                'type' => 'column',
                'columnWidths' => [16, 16],
                'alignments' => ['LEFT', 'RIGHT'],
                'contents' => ['Name:', $order->user->name],
            ];
        }

        if ($order->user->phone) {
            $instructions[] = [
                'type' => 'column',
                'columnWidths' => [16, 16],
                'alignments' => ['LEFT', 'RIGHT'],
                'contents' => ['Phone:', $order->user->phone],
            ];
        }

        if ($order->address) {
            $instructions[] = [
                'type' => 'text',
                'text' => 'Address: ' . $order->address,
            ];
        }

        $instructions[] = ['type' => 'text', 'text' => $singleLine];

        // Footer
        $instructions[] = ['type' => 'align', 'alignment' => 'CENTER'];
        $instructions[] = [
            'type' => 'text',
            'text' => 'Vat (Incl), Thank you for ordering with us!',
            'options' => ['widthtimes' => 0, 'heigthtimes' => 0, 'fonttype' => 2],
        ];
        $instructions[] = [
            'type' => 'text',
            'text' => 'We look forward to serving you again.',
            'options' => ['widthtimes' => 0, 'heigthtimes' => 0, 'fonttype' => 2],
        ];

        return $instructions;
    }




    public function printOrderKOTBillInstructions($order_id)
    {
        $user = User::where('id', 2)->first();
        if (!$user) return [];

        $order = Order::where('id', $order_id)->with('restaurant', 'orderitems')->first();
        if (!$order) return [];

        $lineSeparator = str_repeat('-', 32) . PHP_EOL;
        $instructions = [];

        // KOT Heading
        $instructions[] = ['type' => 'newline'];
        $instructions[] = ['type' => 'align', 'alignment' => 'CENTER'];
        $instructions[] = ['type' => 'bold', 'value' => true];
        $instructions[] = [
            'type' => 'text',
            'text' => 'KOT',
            'options' => ['widthtimes' => 2, 'heigthtimes' => 2, 'fonttype' => 1],
        ];
        $instructions[] = ['type' => 'bold', 'value' => false];

        $instructions[] = ['type' => 'newline'];
        $instructions[] = [
            'type' => 'text',
            'text' => $order->created_at->format('D, M d, Y  h:i A'),
        ];

        $instructions[] = ['type' => 'text', 'text' => $lineSeparator];

        // Order ID (slightly bigger)
        $instructions[] = ['type' => 'bold', 'value' => true];
        $instructions[] = [
            'type' => 'text',
            'text' => 'Order# ' . $order->unique_order_id,
            'options' => ['widthtimes' => 0, 'heigthtimes' => 0, 'fonttype' => 0],
        ];
        $instructions[] = ['type' => 'bold', 'value' => false];
        $instructions[] = ['type' => 'text', 'text' => $lineSeparator];

        // Items Header
        $instructions[] = ['type' => 'bold', 'value' => true];
        $instructions[] = [
            'type' => 'column',
            'columnWidths' => [24, 8],
            'alignments' => ['LEFT', 'RIGHT'],
            'contents' => ['Item', 'Qty'],
        ];
        $instructions[] = ['type' => 'bold', 'value' => false];

        // Order Items
        foreach ($order->orderitems as $orderitem) {
            $itemName = $orderitem->name;

            if ($orderitem->orderitemaddons && $orderitem->orderitemaddons->isNotEmpty()) {
                $addonNames = implode(', ', array_column($orderitem->orderitemaddons->toArray(), 'name'));
                $itemName .= " ($addonNames)";
            }

            $instructions[] = [
                'type' => 'column',
                'columnWidths' => [24, 8],
                'alignments' => ['LEFT', 'RIGHT'],
                'contents' => [$itemName, 'x' . $orderitem->quantity],
            ];
        }

        $instructions[] = ['type' => 'text', 'text' => $lineSeparator];

        // Total Price
        $instructions[] = ['type' => 'bold', 'value' => true];
        $instructions[] = [
            'type' => 'column',
            'columnWidths' => [16, 16],
            'alignments' => ['LEFT', 'RIGHT'],
            'contents' => ['TOTAL', 'AED ' . number_format($order->total, 2)],
        ];
        $instructions[] = ['type' => 'bold', 'value' => false];

        $instructions[] = ['type' => 'text', 'text' => $lineSeparator];

        // Footer Message
        $instructions[] = ['type' => 'align', 'alignment' => 'CENTER'];
        // $instructions[] = [
        //     'type' => 'text',
        //     'text' => 'Prepare freshly – Bon Appétit!',
        //     'options' => ['widthtimes' => 0, 'heigthtimes' => 0, 'fonttype' => 2],
        // ];

        // Padding / Paper Spacing
        // $instructions[] = ['type' => 'newline'];
        // $instructions[] = ['type' => 'newline'];
        $instructions[] = ['type' => 'newline'];

        return $instructions;
    }




    public function printOrderBillImage($order_id)
    {
        // $user = Auth::user();
        $user = User::where('id', 2)->first();
        if ($user) {
            // return response()->json($order_id);
            $order = Order::where('id', $order_id)->with('restaurant', 'orderitems')->first();
            info($order);
            $html =  view('admin.orders.orderBillApp', compact('order'))->render();
            $image = SnappyImage::loadHTML($html)
                ->setOption('format', 'jpg') // or 'png'
                ->setOption('width', 210)
                ->setOption('quality', 100)

                ->output(); // This generates the image in binary form

            // Create an image instance from the binary data
            $manager = new ImageManager(new Driver());

            // read image from file system

            $img = $manager->read($image);

            // Convert to grayscale
            $img = $img->greyscale();
            $img = $img->contrast(-20);

            // Sharpen the image
            $img = $img->sharpen(50); // You can adjust the value (1-100) based on your preference

            // Get the modified image in binary form
            $modifiedImage = (string) $img->toJpeg(
                100
            );

            // Convert the modified image binary to Base64
            $base64Image = base64_encode($modifiedImage);
            // Return the Base64 encoded image string without any prefix
            return $base64Image;
        } else {

            $response = ['success' => false, 'data' => 'Something Went Wrong'];
        }

        return response()->json($response);
    }


    public function printOrderBillImagee($order_id)
    {
        // $user = Auth::user();
        $user = User::where('id', 2)->first();
        if ($user) {
            // return response()->json($order_id);
            $order = Order::where('id', $order_id)->with('restaurant', 'orderitems')->first();
            info($order);
            $html = view('admin.orders.orderBillApp', compact('order'))->render();

            // Generate the PDF from the HTML
            $pdf = SnappyPdf::loadHTML($html)
                ->setOption('page-width', '50mm')
                ->setOption('page-height', '150mm')
                ->setOption('dpi', 300) // High DPI for better quality
                ->setOption('image-dpi', 300) // High DPI for better quality
                ->setOption('image-quality', 100) // High quality
                ->setOption('margin-left', '0mm')
                ->setOption('margin-right', '1mm')
                ->setOption('margin-top', '0mm')
                ->setOption('margin-bottom', '0mm')
                ->setOption('no-outline', true)
                ->output();

            // return response($pdf, 200, [
            //     'Content-Type' => 'application/pdf',
            // ]);

            $pdfPath = public_path("orders/{$order_id}.pdf");

            // Ensure the directory exists
            if (!file_exists(public_path('orders'))) {
                mkdir(public_path('orders'), 0755, true);
            }

            // Save the PDF to the public folder
            file_put_contents($pdfPath, $pdf);

            // Convert the PDF to an image using spatie/pdf-to-image
            $pdf = new PdfToImagePdf($pdfPath);
            $imagePath = public_path("orders/{$order_id}.png");

            // Save the image to the public folder
            $pdf->saveImage($imagePath);

            // Get the image as base64
            $imageData = file_get_contents($imagePath);
            $imageBase64 = base64_encode($imageData);


            return $imageBase64;

            // Convert the image binary to Base64
            // $base64Image = base64_encode($image);

            // Return the Base64 encoded image string without any prefix

        } else {

            $response = ['success' => false, 'data' => 'Something Went Wrong'];
        }

        return response()->json($response);
    }
    public function printOrderBill($order_id)
    {
        // $user = Auth::user();
        $user = User::where('id', 2)->first();
        if ($user) {
            // return response()->json($order_id);
            $order = Order::where('id', $order_id)->with('restaurant', 'orderitems')->first();
            info($order);
            return view('admin.orders.orderBillApp', compact('order'))->render();
            $order = Order::where('id', $order_id)->with('restaurant', 'orderitems')->first();
            info($order);
            $html = view('admin.orders.orderBillApp', compact('order'))->render();
            $pdf = SnappyPdf::loadHTML($html)
                ->setOption('page-width', '58mm') // Set dimensions for better clarity
                ->setOption('page-height', '150mm')
                ->setOption('dpi', 300) // Set high DPI for better quality
                ->output();

            // Convert the image binary to Base64
            // $base64Image = base64_encode($image);

            // Return the Base64 encoded image string without any prefix
            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
            ]);
        } else {

            $response = ['success' => false, 'data' => 'Something Went Wrong'];
        }

        return response()->json($response);
    }

    public function printOrderBillView($order_id)
    {
        $user = User::where('id', 2)->first();
        if ($user) {
            $order = Order::where('id', $order_id)->with('restaurant', 'orderitems')->first();
            info($order);
            return view('admin.orders.orderBillApp', compact('order'))->render();
        } else {

            return ['error' => ''];
        }
    }

    //KOT
    public function printKOTOrderBill($order_id)
    {
        $user = User::where('id', 2)->first();
        if ($user) {
            $order = Order::where('id', $order_id)->with('restaurant', 'orderitems')->first();
            info($order);
            $render = view('admin.orders.orderKOTBillApp', compact('order'))->render();
            $pdf = PDF::loadHTML($render, 'UTF-8')->setPaper('a4', 'portrait');
            return $pdf->download('order-kot-bill' . '.pdf');
        } else {

            $response = ['success' => false, 'data' => 'Something Went Wrong'];
        }

        return response()->json($response);
    }

    public function getAllOrders(Request $request)
    {
        $authStoreIds = Auth::user()->restaurants->pluck('id')->first();
        if ($authStoreIds) {
            $newOrders = Order::where('restaurant_id', $authStoreIds)
                ->where('is_schedule', 0)
                ->whereIn('order_status_id', [1, 2])
                ->with('orderItems.item', 'orderItems.orderItemAddons', 'user')
                ->orderBy('id', 'DESC')

                ->get();

            $scheduledOrders = Order::where('restaurant_id', $authStoreIds)
                ->where('is_schedule', 1)
                ->whereIn('order_status_id', [1, 2])
                ->with('orderItems.item', 'orderItems.orderItemAddons', 'user')
                ->orderBy('id', 'DESC')
                ->get();

            $readyOrders = Order::where('restaurant_id', $authStoreIds)->where('order_status_id', 4)->with('orderItems.item', 'orderItems.orderItemAddons', 'user')->orderBy('id', 'DESC')->get();
            $pickedOrders = Order::where('restaurant_id', $authStoreIds)->whereIn('order_status_id', [5, 6])->with('orderItems.item', 'orderItems.orderItemAddons', 'user')->orderBy('id', 'DESC')->get();
            $ongoingOrderNotNull = Order::where('restaurant_id', $authStoreIds)->whereIn('order_status_id', [3])->with('orderItems.item', 'orderItems.orderItemAddons', 'user')->whereNotNull('need_more_time_at')->whereNotNull('order_prepairing_extra_time')->orderBy('need_more_time_at', 'desc')->get();
            $ongoingOrderNull = Order::where('restaurant_id', $authStoreIds)->whereIn('order_status_id', [3])->with('orderItems.item', 'orderItems.orderItemAddons', 'user')->whereNull('need_more_time_at')->orderBy('id', 'DESC')->whereNull('order_prepairing_extra_time')->get();
            $ongoingOrders = $ongoingOrderNotNull->concat($ongoingOrderNull);
            $notNullOrders = Order::where('restaurant_id', $authStoreIds)->whereIn('order_status_id', [3])->with('restaurant', 'user', 'orderItems', 'orderItems.orderItemAddons', 'orderStatus')->whereNotNull('need_more_time_at')->get();
            $pendingOrders = [];
            foreach ($notNullOrders as $notNullOrder) {
                $now = Carbon::now();
                $newDate = Carbon::parse($notNullOrder->need_more_time_at)->addMinutes($notNullOrder->order_prepairing_extra_time);
                if ($newDate <= $now) {
                    $pendingOrders[] = $notNullOrder;
                }
            }
            $response = [
                'success' => true,
                'newOrders' => $newOrders,
                'scheduledOrders' => $scheduledOrders,
                'pickedOrders' => $pickedOrders,
                'readyOrders' => $readyOrders,
                'ongoingOrders' => $ongoingOrders,
                'pendingOrders' => $pendingOrders,
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }

    public function getAllPendingOrders(Request $request)
    {
        $authStoreIds = Auth::user()->restaurants->pluck('id')->first();
        if ($authStoreIds) {
            $notNullOrders = Order::where('restaurant_id', $authStoreIds)->whereIn('order_status_id', [3])->with('restaurant', 'user', 'orderItems', 'orderItems.orderItemAddons', 'orderStatus')->whereNotNull('need_more_time_at')->get();
            $pendingOrders = [];
            foreach ($notNullOrders as $notNullOrder) {
                $now = Carbon::now();
                $newDate = Carbon::parse($notNullOrder->need_more_time_at)->addMinutes($notNullOrder->order_prepairing_extra_time);
                if ($newDate <= $now) {
                    $pendingOrders[] = $notNullOrder;
                }
            }
            $response = [
                'success' => true,
                'pendingOrders' => $pendingOrders,
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'No User Found',
            ];
        }
        return response()->json($response);
    }

    public function getAllStoreItems(Request $request)
    {
        $restaurant_id = $request->restaurant_id;
        $restaurant = Restaurant::where('id', $request->restaurant_id)->with('favorites', 'city')->first();
        $itemCategories = ItemCategory::where('restaurant_id', $request->restaurant_id)
            ->where('is_active', 1)
            ->with(['items' => function ($query) use ($restaurant_id) {
                $query->with(
                    'addonCategories',
                    'restaurant.city',
                    'addonCategories.addons'
                )->where('restaurant_id', $restaurant_id)->where('is_active', 1);
            }])->whereHas('items', function ($query) use ($restaurant_id) {
                $query->with(
                    'addonCategories',
                    'restaurant.city',
                    'addonCategories.addons'
                )->where('restaurant_id', $restaurant_id)->where('is_active', 1);
            })->get();
        $itemCategoryIds = ItemCategory::where('restaurant_id', $request->restaurant_id)->where('is_active', 1)->pluck('id')->toArray();
        $items = Item::whereIn('item_category_id', $itemCategoryIds)->where('restaurant_id', $request->restaurant_id)
            ->where('is_active', 1)->where('is_recommended', 1)->with('addonCategories', 'restaurant.city', 'addonCategories.addons')->get();
        $vegItems = collect();
        $nonVegItems = collect();
        foreach ($items as $item) {
            if ($item->is_veg) {
                $vegItems->push($item);
            } else {
                $nonVegItems->push($item);
            }
        }
        foreach ($itemCategories as $itemCategory) {
            $vegItem = collect();
            $noneVegItem = collect();
            foreach ($itemCategory->items as $item) {
                if ($item->is_veg) {
                    $vegItem->push($item);
                } else {
                    $noneVegItem->push($item);
                }
            }
            $itemCategory->vegItems = $vegItem;
            $itemCategory->nonVegItems = $noneVegItem;
        }
        $response = [
            'success' => true,
            'restaurant' => $restaurant,
            'itemCategories' => $itemCategories,
            'items' => $items,
            'vegItems' => $vegItems,
            'nonVegItems' => $nonVegItems,
        ];
        return response()->json($response);
    }

    //ORDERFUCTIONS

    public function placeOrder(Request $request)
    {
        $user = auth()->user();
        if ($user) {

            $user = User::where('id', $request->customer_id)->first();
            $newOrder = new Order();
            $unique_order_id = 'OD' . '-' . date('m-d') . '-' . rand(1111, 9999) . '-' . rand(1111, 9999);
            $newOrder->unique_order_id = $unique_order_id;
            $newOrder->platform_fee =  (float) ((float)$request->platform_fee);
            // $newOrder->order_prepairing_time = 1;
            // $newOrder->customer_id = $request->customer_id;
            // $newOrder->customer_phone = $request->customer_phone;
            info($request->all());
            $newOrder->user_id = $request->customer_id;


            $restaurant_id = $request->restaurant_id;
            $restaurant = Restaurant::where('id', $restaurant_id)->first();
            if ($request['paymentMode'] == 'ONLINE') {
                $newOrder->order_status_id = '9';
            } else {
                $newOrder->order_status_id = '1';
            }
            $landmark = $request->landmark ?? '';
            $newOrder->Address = $landmark . ', ' . $request->address;
            $newOrder->latitude = $request->latitude;
            $newOrder->longitude = $request->longitude;


            // $Address = json_decode($request->address);
            // $newOrder->address = $Address->address;
            // $newOrder->landmark = $Address->landmark;
            // $newOrder->latitude = $Address->latitude;
            // $newOrder->longitude = $Address->longitude;

            // if ($request->is_schedule == "true") {
            //     $newOrder->is_schedule = 1;
            //     $newOrder->schedule_date = Carbon::parse($request->schedule_date)->format('Y-m-d');
            //     $newOrder->schedule_time = $request->schedule_time;
            // } else {
            //     $newOrder->is_schedule = 0;
            // }

            // if ($request->is_express == 1) {
            //     $newOrder->is_express = 1;
            // } else {
            //     $newOrder->is_express = 0;
            // }

            $newOrder->restaurant_charges = (float) ((float)$restaurant->restaurant_charges);
            if ($restaurant->city->is_surge == 1) {
                $newOrder->surge_fee = (float) ((float)$restaurant->city->surge_fee);
            }
            $newOrder->order_placed_at = Carbon::now();
            $newOrder->order_accepted_at = Carbon::now();
            $newOrder->city_id = $restaurant->city_id;

            $orderItemCommission = 0;
            $orderTotal = 0;
            $cartItems = json_decode($request['cartProducts']);
            foreach ($cartItems as $oI) {
                $originalItem = Item::where('id', $oI->id)->first();
                if (isset($oI->quantity)) {
                    $orderTotal += ($originalItem->selling_price * $oI->quantity);
                    if (!is_null($originalItem->commision_rate)) {
                        $orderItemCommission += (float) ((float)$originalItem->commision_rate / 100 * ($originalItem->selling_price * $oI->quantity));
                    } else {
                        $orderItemCommission +=  (float) ((float)$originalItem->restaurant->commission_rate / 100 * ($originalItem->selling_price * $oI->quantity));
                    }
                }

                if (isset($oI->selectedaddons)) {
                    foreach ($oI->selectedaddons as $selectedaddon) {
                        $addon = Addon::where('id', $selectedaddon->addon_id)->first();
                        if ($addon) {
                            $orderTotal += $addon->price * $selectedaddon->quantity + $originalItem->selling_price * $selectedaddon->quantity;
                        }
                    }
                }
            }


            $newOrder->sub_total = $orderTotal;



            if ($restaurant->tax && $restaurant->tax > 0) {
                $taxAmount = (float) (((float) $restaurant->tax / 100) * $newOrder->sub_total);
            } else {
                $taxAmount = 0;
            }

            $newOrder->tax = $taxAmount;

            $orderTotal = $orderTotal + $taxAmount;

            $orderTotal = $orderTotal + (float) ((float)$restaurant->restaurant_charges);
            $orderTotal = $orderTotal  +  (float) $request->platform_fee;

            $newOrder->restaurant_total = $orderTotal;

            if ($restaurant->city->is_surge == 1) {
                $orderTotal += (float) ((float)$restaurant->city->surge_fee);
            }
            $newOrder->total_commission = (float) ((float)$orderItemCommission);

            if ($restaurant->city->delivery_charge_type == 'DYNAMIC' && $restaurant->city->base_delivery_distance && $restaurant->city->extra_delivery_distance && $restaurant->city->extra_delivery_charge && $restaurant->city->base_delivery_charge) {
                $distance =  (float)$request->distance;
                if ($distance > $restaurant->city->base_delivery_distance) {
                    $extraDistance = $distance - $restaurant->city->base_delivery_distance;
                    $extraCharge = ($extraDistance / $restaurant->city->extra_delivery_distance) * $restaurant->city->extra_delivery_charge;
                    $dynamicDeliveryCharge = $restaurant->city->base_delivery_charge + $extraCharge;
                    $newOrder->delivery_charge = ceil($dynamicDeliveryCharge);
                    $orderTotal = $orderTotal + ceil($dynamicDeliveryCharge);
                } else {
                    $newOrder->delivery_charge = $restaurant->city->base_delivery_charge;
                    $orderTotal = $orderTotal + $restaurant->city->base_delivery_charge;
                }
            } else if ($restaurant->city->delivery_charge_type == 'FIXED' && ($restaurant->city->delivery_charge) > 0) {
                $newOrder->delivery_charge = $restaurant->city->delivery_charge;
                $orderTotal = $orderTotal + $restaurant->city->delivery_charge;
            }

            if ($request['paymentMode'] == 'COD') {
                if ($request->partial_wallet == true) {
                    $newOrder->payable = $orderTotal;
                }
                if ($request->partial_wallet == false) {
                    $newOrder->payable = $orderTotal;
                }
            }

            $newOrder->total = (float) ((float)$orderTotal);

            $newOrder->order_comment = $request['deliveryNote'];

            $newOrder->payment_mode = $request['paymentMode'];
            $newOrder->payment_status = "PENDING";
            $newOrder->restaurant_id = $request->restaurant_id;


            $newOrder->payout_amount = ($newOrder->sub_total + $newOrder->restaurant_charges) - $newOrder->total_commission;

            $newOrder->order_status_id = 3;
            $newOrder->save();

            //process online payment
            if ($request['paymentMode'] == 'ONLINE') {
                $newOrder->save();
                if ($request->partial_wallet == 'true') {
                    $userWalletBalance = (float)floatval($user->balance);
                    $newOrder->walletamount = $userWalletBalance;
                    $newOrder->payable = (float) $orderTotal - (float)$userWalletBalance;
                    $newOrder->save();
                    //deduct all user amount and add
                    $user->withdraw($userWalletBalance * 100, ['description' => 'Partial amount for Order  ' . $newOrder->unique_order_id]);
                }
                $cartItems = json_decode($request['cartProducts']);
                foreach ($cartItems as $orderItem) {
                    $item = new OrderItem();
                    $item->order_id = $newOrder->id;
                    $item->item_id = $orderItem->id;
                    $item->name = $orderItem->name;
                    $item->quantity = $orderItem->quantity;
                    $item->price = $orderItem->selling_price;
                    $item->save();


                    if (isset($orderItem->selectedaddons)) {
                        foreach ($orderItem->selectedaddons as $selectedaddon) {
                            $addon = Addon::where('id', $selectedaddon->addon_id)->first();
                            $addonCategory = AddonCategory::where('id', $addon->addon_category_id)->first();
                            if ($addon) {

                                $addonItem = new OrderItemAddon();
                                $addonItem->order_item_id = $item->id;
                                $addonItem->addon_id = $addon->id;
                                $addonItem->category_name = $addonCategory->name;
                                $addonItem->name = $addon->name;
                                $addonItem->price = $addon->price;
                                $addonItem->quantity = $selectedaddon->quantity;
                                $addonItem->save();
                                $item->price += $addon->price;
                                $item->quantity += $selectedaddon->quantity;
                                $item->save();
                                $addon->stock = $addon->stock - 1;
                            }
                        }
                    }
                }

                $response = [
                    'success' => true,
                    'data' => $newOrder,
                ];

                $data = [
                    "countryCode" => "+971",
                    "phoneNumber" => $user->phone,
                    "type" => "Template",
                    "template" => [
                        "name" => "order_placed",
                        "languageCode" => "en",
                        "bodyValues" => [
                            $user->name,
                            $newOrder->total,
                            $restaurant->name,
                            $restaurant->address,
                            "ZEATO05"
                        ],
                        "buttonValues" => [
                            "1" => [
                                $newOrder->id
                            ]
                        ]
                    ],
                ];

                SendInteraktMessage::dispatch($data);

                return response()->json($response);
            } else {
                $newOrder->save();
                if ($request['paymentMode'] == 'COD') {
                    if ($request->partial_wallet == 'true') {
                        $userWalletBalance = (float) floatval($user->balance);
                        $newOrder->walletamount = $userWalletBalance;
                        $newOrder->payable = (float) $orderTotal - (float)$userWalletBalance;
                        $newOrder->save();
                    }
                }

                if ($request['paymentMode'] == 'WALLET') {
                    $userWalletBalance = (float) floatval($user->balance);
                    $newOrder->walletamount = $orderTotal;
                    $newOrder->save();
                    $user->withdraw($orderTotal, ['description' => "Order Amount for the Order: " . $newOrder->unique_order_id]);
                    if ($newOrder->walletamount == $newOrder->total) {
                        $newOrder->payment_status = "SUCCESS";
                        $newOrder->save();
                    }
                }
                $cartItems = json_decode($request['cartProducts']);

                foreach ($cartItems as $orderItem) {
                    $item = new OrderItem();
                    $item->order_id = $newOrder->id;
                    $item->item_id = $orderItem->id;
                    $item->name = $orderItem->name;
                    if (isset($orderItem->quantity)) {
                        $item->quantity = $orderItem->quantity;
                    }
                    if (isset($orderItem->selling_price)) {
                        $item->price = $orderItem->selling_price;
                    }
                    $item->save();

                    if (isset($orderItem->selectedaddons)) {
                        foreach ($orderItem->selectedaddons as $selectedaddon) {
                            $addon = new OrderItemAddon();
                            $realAddon = Addon::where('id', $selectedaddon->addon_id)->first();
                            $AddonCategory = AddonCategory::where('id', $realAddon->addon_category_id)->first();
                            $addon->addon_id = $realAddon->id;
                            $addon->order_item_id = $item->id;
                            $addon->category_name = $AddonCategory->name;
                            $addon->name = $realAddon->name;
                            $addon->price = $realAddon->price;
                            $addon->quantity = $selectedaddon->quantity;

                            $addon->save();
                            $item->price += $realAddon->price;
                            $item->save();
                        }
                    }
                    if (isset($orderItem->removals)) {
                        $item->removals = json_encode($orderItem->removals);
                        $item->save();
                    }
                }


                if ($request['paymentMode'] == 'COD' || $request['paymentMode'] == 'WALLET') {
                }

                $vHeading = 'Order Created!';
                $userName = User::where('id', $newOrder->user_id)->value('name');
                $vMessage = 'An order for ' .    $userName  . ' has been created. Please begin preparation and ensure timely delivery.';
                $vendors = $restaurant->users;
                foreach ($vendors as $vendor) {
                    \App\Jobs\pushNotification::dispatch($vendor->id, $vMessage, $vHeading, 'vendor');
                }
                $response = [
                    'success' => true,
                    'data' => $newOrder,
                ];

                $data = [
                    "countryCode" => "+971",
                    "phoneNumber" => $user->phone,
                    "type" => "Template",
                    "template" => [
                        "name" => "order_placed",
                        "languageCode" => "en",
                        "bodyValues" => [
                            $user->name,
                            $newOrder->total,
                            $restaurant->name,
                            $restaurant->address,
                            "ZEATO05"
                        ],
                        "buttonValues" => [
                            "1" => [
                                $newOrder->id
                            ]
                        ]
                    ],
                ];

                SendInteraktMessage::dispatch($data);

                return response()->json($response);
            }
        }
    }

    public function getCartRestaurant(Request $request)
    {
        $restaurant = Restaurant::where('id', $request->restaurant_id)->with('city')->first();
        $response = [
            'success' => true,
            'data' => [
                'restaurant' => $restaurant,
            ],
        ];
        return response()->json($response, 201);
    }

    private function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * 6371;
    }

    private function getDistanceGoogle($originLat, $originLng, $destLat, $destLng)
    {
        $client = new Client();
        $apiKey = 'AIzaSyD8gyy7vdYe-ybpheXUPQT6XX4SfLrkyl4';

        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';

        $params = [
            'origins' => "{$originLat},{$originLng}",
            'destinations' => "{$destLat},{$destLng}",
            'key' => $apiKey,
        ];

        try {
            $response = $client->get($url, ['query' => $params]);
            $data = json_decode($response->getBody(), true);

            if (!empty($data['rows'][0]['elements'][0]['distance'])) {
                $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];

                return $distanceInMeters / 1000;
            }

            $restaurant_distance = $this->getDistance($originLat, $originLng, $destLat, $destLng);

            return $restaurant_distance;
        } catch (\Exception $e) {
            $restaurant_distance = $this->getDistance($originLat, $originLng, $destLat, $destLng);

            return $restaurant_distance;
        }
    }

    public function calculateRestaurantMaxDistance(Request $request)
    {
        $products = json_decode($request->products);
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $distance = collect(['distance' => 0, 'restaurant_id' => null]);
        if ($products && $latitude && $longitude) {
            $restaurant = Restaurant::where('id', $products[0]->restaurant->id)->with('city')->first();
            $restaurant_distance = $this->getDistanceGoogle($restaurant->latitude, $restaurant->longitude, $latitude, $longitude);
            if ($distance['distance'] <= $restaurant_distance) {
                $distance['distance'] = $restaurant_distance;
                $distance['restaurant_id'] = $restaurant;
            }
        }

        return response($distance);
    }

    public function calculateRestaurantChargeTax(Request $request)
    {
        $products = json_decode($request->products);
        $charges = collect(['restaurant_charges' => 0, 'tax' => 0]);
        $restaurant_charges = 0;
        $tax = 0;
        $restaurantIds = collect([]);
        if ($products) {
            foreach ($products as $product) {
                $restaurantIds->push($product->restaurant->id);
            }

            $filteredrestaurantIds = $restaurantIds->unique();
            foreach ($filteredrestaurantIds as $restaurant_id) {
                $restaurant = Restaurant::where('id', $restaurant_id)->where('is_active', 1)->where('is_accepted', 1)->first();
                if ($restaurant) {
                    $restaurant_charges +=  $restaurant->restaurant_charge;
                    $tax += $restaurant->tax;
                }
            }

            $charges['restaurant_charges'] = (float)((float)$restaurant_charges);
            $charges['tax'] = (float)((float)$tax);
            $response = [
                'success' => true,
                'restaurant_charge' => (float)((float)$charges['restaurant_charges']),
                'tax' => (float)((float)$charges['tax']),
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'CART PRODUCTS NOT FOUND',
            ];
        }
        return response($response);
    }


    public function vendorcoordinatesToAddress(Request $request)
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
            $address->save();
        } else {
            $address = new Address();
            $address->user_id = $request->user_id;
            $address->address = $request->address;
            $address->landmark = $request->landmark;
            $address->house = $request->house;
            $address->latitude = $request->latitude;
            $address->longitude = $request->longitude;
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



    public function toggleVendor()
    {
        $user = Auth::user();
        if ($user) {
            $authStoreIds = Auth::user()->restaurants->pluck('id')->first();
            if ($authStoreIds) {
                $restaurant = Restaurant::with("city")->where('id', $authStoreIds)->first();
                $status = ($restaurant->is_active == 0) ? 1 : 0;
                $restaurant->is_active = $status;
                $restaurant->save();

                $response = [
                    'success' => true,
                    'data' => 'Store Updated',
                    'is_active' => $restaurant->is_active
                ];
            } else {
                $response = [
                    'success' => false,
                    'data' => 'No Store Found',
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
}
