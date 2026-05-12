<?php

namespace App\Http\Controllers;

use App\Models\User;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Notification;

use Auth;
use Log;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use Exception;
use App\Jobs\sendToFleet;
use App\Jobs\getFleetOrderDetails;
use App\Models\Orderitem;
use App\Models\Settings;
use App\Models\Review;

class OrderController extends Controller
{
    public function orderView($id)
    {
        $order = Order::where('unique_order_id', $id)->with('orderstatus', 'orderitems.orderitemaddons', 'user')->with(array('restaurant' => function ($q) {
            $q->with(array('items' => function ($q) {
                $q->where('is_active', 1);
            }));
        }))->first();
        $logs = Activity::where('subject_id', $order->id)->get();
        if ($order) {
            return view('admin.orders.orderView', array(
                'order' => $order,
                'logs' => $logs,
            ));
        } else {
            return redirect()->route('admin.orders');
        }
    }

    public function reviews()
    {
        $reviews = Review::all();
        return view('admin.reviews.reviews', array(
            'reviews' => $reviews,
        ));
    }
    public function viewLiveOrders($city_id)
    {
        $cities = Auth::user()->cities;
        if ($city_id == 'All') {
            $city_ids = $cities->pluck('id')->toArray();
            $orders = Order::with('restaurant')->whereNotIn('order_status_id', [5, 6])->whereDate('created_at', '>=', Carbon::now()->subHours(24))->orderBy('created_at', 'desc')->whereIn('city_id', $city_ids)->get();
            $today = Order::with('restaurant')->whereNotIn('order_status_id', [5, 6])->orderBy('schedule_date', 'asc')->orderBy('schedule_time', 'asc')->whereIn('city_id', $city_ids)->get();
        } else {
            $city_ids = $cities->pluck('id')->toArray();
            $orders = Order::with('restaurant')->whereNotIn('order_status_id', [5, 6])->whereDate('created_at', '>=', Carbon::now()->subHours(24))->orderBy('schedule_date', 'desc')->where('city_id', $city_id)->get();
            $today = Order::with('restaurant')->whereNotIn('order_status_id', [5, 6])->orderBy('schedule_date', 'desc')->orderBy('schedule_time', 'asc')->whereIn('city_id', $city_ids)->get();
        }

        $newOrders = $orders->where('order_status_id', 1)->where('is_schedule', 0);
        $acceptedOrders = $orders->where('order_status_id', 2)->where('is_schedule', 0);
        $ongoingOrders = $orders->whereIn('order_status_id', [3, 4, 5, 6, 11])->where('is_schedule', 0);
        $completedOrders = Order::where('order_status_id', 5)->whereDate('created_at', '>=', Carbon::now()->subHours(24))->where('is_schedule', 0)->get();
        $cancelledOrders = Order::where('order_status_id', 6)->whereDate('created_at', '>=', Carbon::now()->subHours(24))->where('is_schedule', 0)->get();
        $newScheduledOrders = $orders->where('order_status_id', 1)->where('is_schedule', 1);
        $acceptedScheduledOrders = $orders->where('order_status_id', 2)->where('is_schedule', 1);
        $ongoingScheduledOrders = $today->whereIn('order_status_id', [3, 4, 5, 6, 11])->where('is_schedule', 1);
        $completedScheduledOrders = Order::where('order_status_id', 5)->whereDate('created_at', '>=', Carbon::now()->subHours(24))->where('is_schedule', 1)->get();
        $cancelledScheduledOrders = Order::where('order_status_id', 6)->whereDate('created_at', '>=', Carbon::now()->subHours(24))->where('is_schedule', 1)->get();
        $instant_count = $orders->where('is_schedule', 0)->whereIn('order_status_id', [1, 2, 3, 4, 5, 6, 11])->count();
        $schedule_count = $orders->where('is_schedule', 1)->whereIn('order_status_id', [1, 2, 3, 4, 5, 6, 11])->count();

        return view('admin.liveOrders.liveOrders', array(
            'newOrders' => $newOrders,
            'acceptedOrders' => $acceptedOrders,
            'ongoingOrders' => $ongoingOrders,
            'newScheduledOrders' => $newScheduledOrders,
            'acceptedScheduledOrders' => $acceptedScheduledOrders,
            'ongoingScheduledOrders' => $ongoingScheduledOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
            'completedScheduledOrders' => $completedScheduledOrders,
            'cancelledScheduledOrders' => $cancelledScheduledOrders,
            'instant_count' => $instant_count,
            'schedule_count' => $schedule_count,
            'cities' => $cities,
            'city_id' => $city_id,
        ));
    }
    public function ajaxLiveOrders()
    {
        $cityIds = Auth::user()->cities->pluck('id')->toArray();

        $orders = Order::whereIn('city_id', $cityIds)->whereDate('created_at', Carbon::now()->subHours(24))->with('restaurant')->get();

        $newOrders = $orders->where('order_status_id', 1)->where('is_schedule', 0);
        $acceptedOrders = $orders->where('order_status_id', 2)->where('is_schedule', 0);
        $ongoingOrders = $orders->whereIn('order_status_id', [3, 4, 7, 8, 9, 10, 11])->where('is_schedule', 0);
        $completedOrders = $orders->where('order_status_id', 5)->where('is_schedule', 0);
        $cancelledOrders = $orders->where('order_status_id', 6)->where('is_schedule', 0);
        $newScheduledOrders = $orders->where('order_status_id', 1)->where('is_schedule', 1);
        $acceptedScheduledOrders = $orders->where('order_status_id', 2)->where('is_schedule', 1);
        $ongoingScheduledOrders = $orders->whereIn('order_status_id', [3, 4, 7, 8, 9, 10, 11])->where('is_schedule', 1);
        $completedScheduledOrders = $orders->where('order_status_id', 5)->where('is_schedule', 1);
        $cancelledScheduledOrders = $orders->where('order_status_id', 6)->where('is_schedule', 1);
        $instant_count = $orders->where('is_schedule', 0)->where('order_status_id', 1)->count();
        $schedule_count = $orders->where('is_schedule', 1)->where('order_status_id', 1)->count();
        return response()->json(
            [
                'newOrders' => $newOrders,
                'acceptedOrders' => $acceptedOrders,
                'ongoingOrders' => $ongoingOrders,
                'newScheduledOrders' => $newScheduledOrders,
                'acceptedScheduledOrders' => $acceptedScheduledOrders,
                'ongoingScheduledOrders' => $ongoingScheduledOrders,
                'completedOrders' => $completedOrders,
                'cancelledOrders' => $cancelledOrders,
                'completedScheduledOrders' => $completedScheduledOrders,
                'cancelledScheduledOrders' => $cancelledScheduledOrders,
                'instant_count' => $instant_count,
                'schedule_count' => $schedule_count
            ]
        );
    }

    public function ajaxLiveSearchOrders(Request $request)
    {

        $searchorders = Order::where('unique_order_id', 'like', '%' . $request->search . '%')
            ->with('restaurant', 'user', 'orderstatus')
            ->limit(20)
            ->get();
        if ($searchorders->count() > 0) {
            $searchorders = $searchorders->map(function ($order) {
                $order->formatted_created_at = $order->created_at->diffForHumans();
                return $order;
            });
        }
        return response($searchorders);
    }

    public function acceptOrderByAdmin(Request $request)
    {
        $order = Order::where('id', $request->id)->with('restaurant.users')->first();
        $restaurant = Restaurant::find($order->restaurant_id);

        if ($order->order_status_id == 1) {
            $order->order_status_id = 2;
            $order->order_accepted_at = Carbon::now();
            $order->save();


            activity()->performedOn($order)
                ->causedBy(Auth::user())
                ->withProperties(['orderstatus' => '2'])
                ->log('Order Accepted by Admin' . Auth::user()->name);

            sendToFleet::dispatch($order->id, 0);

            $heading = 'Order Confirmed';
            $message =  $restaurant->name . ' has started preparing your order. Our delivery executive will pick it up soon.';
            \App\Jobs\pushNotification::dispatch($order->user_id, $message, $heading, 'customer');

            $notification = new Notification();
            $notification->user_id = $order->user_id;
            $notification->title = $heading;
            $notification->body = $message;
            $notification->save();

            $vHeading = 'New Order Recieved';
            $vMessage = 'You have new order at Zeato! Please check the Order';
            $vendors = $restaurant->users;
            foreach ($vendors as $vendor) {
                \App\Jobs\pushNotification::dispatch($vendor->id, $vMessage, $vHeading, 'vendor');
            }


            return redirect()->back()->with(array('success' => 'Order Accepted by Admin'));
        } else {
            return redirect()->back()->with(array('error' => 'Something went wrong.'));
        }
    }

    public function taskUpdate(Request $request)
    {
        $order = Order::where('id', $request->order_client_id)->first();
        if ($request->order_status_id == 3 && !$order->order_assigned_at) {
            $order->order_assigned_at = Carbon::now();
            $order->agent_name = $request->agent_name;
            $order->agent_phone = $request->agent_contact;
            $order->agent_image = $request->agent_image;
            $order->save();
            if ($order->agent_name && $order->agent_phone) {

                $heading = 'Order Assigned to Delivery Guy';
                $message = $order->agent_name . ' is your delivery partner';

                $name = 'Order Assigned to Delivery Guy !!';
                $data = 'Heyy..' . $order->agent_name . ' is your delivery partner..Your order is on the way..!!';
            }

            activity()->performedOn($order)
                ->withProperties(["orderstatus" => "3"])
                ->log('Order Assigned to Delivery Guy');
        }
        if ($request->order_status_id == 4 && !$order->order_picked_up_at) {

            $order->order_picked_up_at = Carbon::now();
            $heading = 'Order Ready To Pickup';
            $message = $order->agent_name . ' will reach your doorstep soon';

            $name = 'Order Ready To Pickup !!';
            $data = $order->agent_name . ' will reach your doorstep soon....';

            activity()->performedOn($order)
                ->withProperties(['orderstatus' => '4'])
                ->log('Order Picked by agent');
        }
        if ($request->order_status_id == 11) {
            $order->cancellation_reason = 'Logistics Cancellation';
            activity()->performedOn($order)
                ->withProperties(['orderstatus' => '6'])
                ->log('Order Cancelled by HowinFleet');
        }

        $order->order_status_id = $request->order_status_id;
        $order->save();
        if ($order->payment_mode == 'COD' && $order->order_status_id == 4) {
            if ($request->store_payment_mode == 'CASH') {
                $store_payout = $order->store_total;
                $order->store->forceWithdraw($store_payout, ['description' => 'Delivery Boy Paid store total by CASH' . $order->unique_order_id, 'order_id' => $order->unique_order_id, 'payment_mode' => $request->store_payment_mode]);
                $order->store_payment_mode = $request->store_payment_mode;
            }
            if ($request->store_payment_mode == 'WALLET') {
                $store_payout = $order->store_total;
                $order->store->deposit($store_payout, ['description' => 'Delivery Boy Paid Store Total using wallet' . $order->unique_order_id, 'order_id' => $order->unique_order_id, 'payment_mode' => $request->store_payment_mode]);
                $order->store_payment_mode = $request->store_payment_mode;
            }
            $order->save();
        }
        if ($order->payment_mode == 'ONLINE' && $order->order_status_id == 4) {
            $storePayout = $order->store_total;
            $order->store->deposit($storePayout, ['description' => 'Deposit order amount for ' . $order->unique_order_id, 'order_id' => $order->unique_order_id, 'payment_mode' => “ONLINE”]);
        }
        return response()->json('Success', 200);
    }

    public function taskComplete(Request $request)
    {

        $order = Order::where('id', $request->order_client_id)->first();
        if ($order && $order->order_status_id != 7) {
            $order->order_status_id = 7;
            $order->order_delivered_at = Carbon::now();


            activity()->performedOn($order)
                ->withProperties(['orderstatus' => '5'])
                ->log('Delivery Completed By HowinFleet');
            $order->save();


            return response()->json('Success', 200);
        } else {
            return response()->json('Task Not Found Or Already Completed', 404);
        }
    }




    public function readyToPickupByAdmin(Request $request)
    {
        $order = Order::where('id', $request->id)->first();
        try {
            if ($order->order_status_id == 2 || $order->order_status_id == 3) {
                $order->order_status_id = 7;
                $order->order_picked_up_at = Carbon::now();
                $order->save();

                activity()->performedOn($order)
                    ->causedBy(Auth::user())
                    ->withProperties(['orderstatus' => '7'])
                    ->log('Order Ready to Picked up by Admin' . Auth::user()->name);


                return redirect()->back()->with(array('success' => 'Order Ready Picked up by Admin'));
            } else {

                return redirect()->back()->with(array('error' => 'Something went wrong.'));
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }

    public function rejectOrderFromAdmin($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return redirect()->back()->with(['error' => 'Order not found']);
        }
        $restaurant = Restaurant::find($order->restaurant_id);
        $admin = Auth::user();
        $user = User::find($order->user_id);

        $heading = 'Order Cancelled';
        $notificationMessage = 'We regret to inform you that your order  (#' . $order->unique_order_id . ')  from ' . $restaurant->name . ' has been cancelled. Please contact support for more details.
';
        \App\Jobs\pushNotification::dispatch($user->id, $notificationMessage, $heading, 'customer');

        if (!$user) {
            return redirect()->back()->with(['error' => 'User not found']);
        }

        $adjustment = 'deposit'; // Default to deposit
        $message = 'Order rejection by admin';

        if ($order->order_status_id != 3 && $order->order_status_id != 4) {

            $order->order_status_id = 6; // Set status to cancelled
            $order->save();

            // Log the cancellation activity
            activity()->performedOn($order)
                ->causedBy($admin)
                ->withProperties(['orderstatus' => '6'])
                ->log('Order Cancelled by Admin For Store ' . $admin->name);

            // Determine the amount based on payment_mode and walletamount
            $amount = null;
            if ($order->payment_mode === 'ONLINE') {
                $amount = $order->total;
            } elseif ($order->payment_mode === 'COD' && $order->walletamount) {
                $amount = $order->payout_amount;
            }

            // Attempt to add money to the wallet based on the adjustment type
            if ($amount !== null) {
                try {
                    if ($adjustment == 'deposit') {
                        $user->deposit($amount, ['description' => $message]);
                    } else {
                        if ($user->balanceFloat >= $amount) {
                            $user->withdraw($amount, ['description' => $message]);
                        } else {
                            return redirect()->back()->with([
                                'success' => 'Order Cancelled',

                                'message' => 'Insufficient balance for withdrawal.'
                            ]);
                        }
                    }
                } catch (\Throwable $th) {
                    Log::error('Wallet adjustment error', ['message' => $th->getMessage()]);
                    return redirect()->back()->with([
                        'success' => 'Order Cancelled',
                        'message' => 'Error adjusting wallet balance: ' . $th->getMessage()
                    ]);
                }
            } else {
                return redirect()->back()->with([
                    'success' => 'Order Cancelled',
                    'message' => 'Invalid payment mode or missing wallet amount for COD.'
                ]);
            }

            return redirect()->back()->with(['success' => 'Order Cancelled']);
        } else {
            return redirect()->back()->with(['error' => 'Something went wrong.']);
        }
    }

    public function thermalPrint($id)
    {
        $order = Order::where('id', $id)->with('order_items')->first();
        return view('admin.orders.thermalPrint', compact('order'));
    }
}
