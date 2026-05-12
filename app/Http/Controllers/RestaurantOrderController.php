<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\Order;
use App\Models\Restaurant;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Jobs\sendToFleet;
use App\Models\Notification;
use App\Models\Review;
use App\Models\User;
use Log;
use Illuminate\Support\Facades\DB;
class RestaurantOrderController extends Controller
{
    public function orderView($id)
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $order = Order::where('unique_order_id', $id)->where('restaurant_id', $restaurant->id)->with('orderstatus', 'orderitems.orderitemaddons', 'user')->with(array('restaurant' => function ($q) {
            $q->with(array('items' => function ($q) {
                $q->where('is_active', 1);
            }));
        }))->first();
        $logs = Activity::where('subject_id', $order->id)->get();
        if ($order) {
            return view('restaurantOwner.orders.orderView', array(
                'order' => $order,
                'logs' => $logs,
            ));
        } else {
            return redirect()->route('restaurantOwner.orders');
        }
    }

    public function viewLiveOrders($city_id)
    {
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $cities = Auth::user()->cities;
        if ($city_id == 'All') {
            $city_ids = $cities->pluck('id')->toArray();
            $orders = Order::where('restaurant_id', $restaurant->id)->with('restaurant')->whereNotIn('order_status_id', [5, 6])->orderBy('created_at', 'desc')->whereIn('city_id', $city_ids)->get();
            $today = Order::where('restaurant_id', $restaurant->id)->with('restaurant')->whereNotIn('order_status_id', [5, 6])->orderBy('schedule_date', 'asc')->orderBy('schedule_time', 'asc')->whereIn('city_id', $city_ids)->get();
        } else {
            $city_ids = $cities->pluck('id')->toArray();
            $orders = Order::where('restaurant_id', $restaurant->id)->with('restaurant')->whereNotIn('order_status_id', [5, 6])->orderBy('schedule_date', 'desc')->where('city_id', $city_id)->get();
            $today = Order::where('restaurant_id', $restaurant->id)->with('restaurant')->whereNotIn('order_status_id', [5, 6])->orderBy('schedule_date', 'desc')->orderBy('schedule_time', 'asc')->whereIn('city_id', $city_ids)->get();
        }

        $newOrders = $orders->where('order_status_id', 1)->where('is_schedule', 0);
        $acceptedOrders = $orders->where('order_status_id', 2)->where('is_schedule', 0);
        $ongoingOrders = $orders->whereIn('order_status_id', [3, 4, 7, 8, 9, 10, 11])->where('is_schedule', 0);
        $completedOrders = Order::where('restaurant_id', $restaurant->id)->where('order_status_id', 5)->whereDate('created_at', '>=', Carbon::now()->subHours(24))->where('is_schedule', 0)->get();
        $cancelledOrders = Order::where('restaurant_id', $restaurant->id)->where('order_status_id', 6)->whereDate('created_at', '>=', Carbon::now()->subHours(24))->where('is_schedule', 0)->get();
        $newScheduledOrders = $orders->where('order_status_id', 1)->where('is_schedule', 1);
        $acceptedScheduledOrders = $orders->where('order_status_id', 2)->where('is_schedule', 1);
        $ongoingScheduledOrders = $today->whereIn('order_status_id', [3, 4, 7, 8, 9, 10, 11])->where('is_schedule', 1);
        $completedScheduledOrders = Order::where('restaurant_id', $restaurant->id)->where('order_status_id', 5)->whereDate('created_at', '>=', Carbon::now()->subHours(24))->where('is_schedule', 1)->get();
        $cancelledScheduledOrders = Order::where('restaurant_id', $restaurant->id)->where('order_status_id', 6)->whereDate('created_at', '>=', Carbon::now()->subHours(24))->where('is_schedule', 1)->get();
        $instant_count = $orders->where('is_schedule', 0)->whereIn('order_status_id', [1, 2, 3, 4, 7, 8, 9, 10, 11])->count();
        $schedule_count = $orders->where('is_schedule', 1)->whereIn('order_status_id', [1, 2, 3, 4, 7, 8, 9, 10, 11])->count();

        return view('restaurantOwner.liveOrders.liveOrders', array(
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
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $cityIds = Auth::user()->cities->pluck('id')->toArray();

        $orders = Order::where('restaurant_id', $restaurant->id)->whereIn('city_id', $cityIds)->whereDate('created_at', Carbon::now()->subHours(24))->with('restaurant')->get();

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
        $restaurantIds = Auth::User()->restaurants->where('is_deleted', 0)->pluck('id')->toArray();
        $restaurant = Restaurant::whereIn('id', $restaurantIds)->with('restaurantCategory')->first();
        $searchorders = Order::where('restaurant_id', $restaurant->id)->where('unique_order_id', 'like', '%' . $request->search . '%')
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
        $order = Order::where('id', $request->id)->first();
        if ($order->order_status_id == 1) {
            $order->order_status_id = 2;
            $order->order_accepted_at = Carbon::now();
            $order->save();

            $restaurant = Restaurant::find($order->restaurant_id);


            $heading = 'Order Confirmed';
            $message =  $restaurant->name . ' has started preparing your order. Our delivery executive will pick it up soon.';
            \App\Jobs\pushNotification::dispatch($order->user_id, $message, $heading, 'customer');

            $notification = new Notification();
            $notification->user_id = $order->user_id;
            $notification->title = $heading;
            $notification->body = $message;
            $notification->save();

            activity()->performedOn($order)
                ->causedBy(Auth::user())
                ->withProperties(['orderstatus' => '2'])
                ->log('Order Accepted by Admin' . Auth::user()->name);

            sendToFleet::dispatch($order->id, 0);

            return redirect()->back()->with(array('success' => 'Order Accepted by Admin'));
        } else {
            return redirect()->back()->with(array('error' => 'Something went wrong.'));
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
        // Fetch the order by ID
        $order = Order::find($id);
        if (!$order) {
            return redirect()->back()->with(['error' => 'Order not found']);
        }

        // Get the admin user (assuming Auth::user() returns the admin user)
        $admin = Auth::user();
        // Fetch the user associated with the order
        $user = User::find($order->user_id);

        if (!$user) {
            return redirect()->back()->with(['error' => 'User not found']);
        }

        // Default values for adjustment and message
        $adjustment = 'deposit'; // Default to deposit
        $message = 'Order rejection by admin';

        if ($order->order_status_id != 5 && $order->order_status_id != 6 && $order->order_status_id != 7) {
            $order->order_status_id = 8; // Set status to cancelled
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

            $heading = 'Order Cancelled !!!';
            \App\Jobs\pushNotification::dispatch($order->user_id, $message, $heading, 'customer');

            $notification = new Notification();
            $notification->user_id = $order->user_id;
            $notification->title = $heading;
            $notification->body = $message;
            $notification->save();


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
public function usersWithOrders()
{
    $user = Auth::user();


    $restaurantIds = DB::table('restaurant_user')
        ->where('user_id', $user->id)
        ->pluck('restaurant_id');

    if ($restaurantIds->isEmpty()) {
        return back()->with('error', 'No restaurant is assigned to your account.');
    }

    return view('restaurantOwner.userswithOrders.usersWithOrders');
}


public function detailedReview()
{
    return view('restaurantOwner.reviews.detailedReview');
}



}
