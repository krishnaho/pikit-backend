<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\StoreEarning;
use App\OrderStoreEarning;
use Carbon\Carbon;
use App\Jobs\pushNotification;
use App\Orderitem;
use App\Models\Notification;


class FleetController extends Controller
{
    public function taskUpdate(Request $request)
    {
        $order = Order::where('id', $request->order_client_id)->first();
        if ($request->order_status_id == 5 && !$order->order_assigned_at) {
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
        if ($request->order_status_id == 6 && !$order->order_picked_up_at) {

            $order->order_picked_up_at = Carbon::now();
            $heading = 'Your Order is on the way! 🏍️';
            $message =  ' Our delivery executive has picked up your order and is heading to your location. Get ready to enjoy your meal soon!   ';

            pushNotification::dispatch($order->user_id, $message, $heading, 'customer');

            $name = 'Order Ready To Pickup !!';
            $data = $order->agent_name . ' will reach your doorstep soon....';

            $notification = new Notification();
            $notification->user_id = $order->user_id;
            $notification->title = $heading;
            $notification->body = $message;
            $notification->save();


            activity()->performedOn($order)
                ->withProperties(['orderstatus' => '6'])
                ->log('Order Picked by agent');
        }
        if ($request->order_status_id == 11) {
            $order->cancellation_reason = 'Logistics Cancellation';
            activity()->performedOn($order)
                ->withProperties(['orderstatus' => '6'])
                ->log('Order Cancelled by HowinFleet');
        }

        if ($request->order_status_id != 5) {
            $order->order_status_id = $request->order_status_id;
        }
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
            $order_count = $order->user->orders->count();
            $order->order_status_id = 7;
            $order->order_delivered_at = Carbon::now();
            activity()->performedOn($order)
                ->withProperties(['orderstatus' => '7'])
                ->log('Delivery Completed By HowinFleet');
            $order->save();

            $heading = 'Order Delivered';
            $message = 'Your order (#' . $order->unique_order_id . ') has been delivered! Enjoy your meal and please rate your experience. Thank you for choosing us!';

            pushNotification::dispatch($order->user_id, $message, $heading, 'customer');

            $notification = new Notification();
            $notification->user_id = $order->user_id;
            $notification->title = $heading;
            $notification->body = $message;
            $notification->save();



            return response()->json('Success', 200);
        } else {
            return response()->json('Task Not Found Or Already Completed', 404);
        }
    }
}
