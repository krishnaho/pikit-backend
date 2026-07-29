<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\AddonCategory;
use App\Models\Coupon;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendInteraktMessage;
use App\Jobs\sendToFleet;

class OrderApiController extends Controller
{
    public function placeOrder(Request $request)
    {
        // info($request->all());
        //exit;
        // dd(1);
        $user = auth()->user();
        if ($user) {
            // Prevent users from placing another order within 5 minutes
            $lastOrder = Order::where('user_id', $user->id)
                ->latest('created_at')
                ->first();

            if ($lastOrder && $lastOrder->created_at->diffInMinutes(now()) < 5) {
                $remainingSeconds = 300 - $lastOrder->created_at->diffInSeconds(now());

                return response()->json([
                    'success' => false,
                    'message' => 'Please wait ' . ceil($remainingSeconds / 60) . ' minute(s) before placing another order.'
                ], 429);
            }

            $user = User::where('id', $user->id)->first();
            $newOrder = new Order();
            $unique_order_id = 'OD' . '-' . date('m-d') . '-' . rand(1111, 9999) . '-' . rand(1111, 9999);
            $newOrder->unique_order_id = $unique_order_id;
            $newOrder->platform_fee = (float) $request->platform_fee;
            $newOrder->order_prepairing_time = 1;
            $newOrder->user_id = $request->user_id;
            $restaurant_id = $request->restaurant_id;
            $restaurant = Restaurant::where('id', $restaurant_id)->first();
            if ($request['paymentMode'] == 'ONLINE') {
                $newOrder->order_status_id = '9';
            } else {
                $newOrder->order_status_id = '1';
            }

            $Address = json_decode($request->address);
            $parts = [];

            if (!empty($Address->tag)) {
                $parts[] = $Address->tag;
            }

            if (!empty($Address->house)) {
                $parts[] = $Address->house;
            }

            if (!empty($Address->landmark)) {
                $parts[] = $Address->landmark;
            }
            $parts[] = $Address->address;
            $fullAddress = implode(", ", $parts);
            $newOrder->address = $fullAddress;
            $newOrder->landmark = $Address->landmark ?? "--";
            $newOrder->latitude = $Address->latitude;
            $newOrder->longitude = $Address->longitude;
            if ($request->is_schedule == "true") {
                $newOrder->is_schedule = 1;
                $newOrder->schedule_date = Carbon::parse($request->schedule_date)->format('Y-m-d');
                $newOrder->schedule_time = $request->schedule_time;
            } else {
                $newOrder->is_schedule = 0;
            }

            $newOrder->is_express = $request->is_express == 1 ? 1 : 0;
            $newOrder->restaurant_charges = (float) $restaurant->restaurant_charges;
            $newOrder->order_placed_at = Carbon::now();
            $newOrder->city_id = $restaurant->city_id;

            $orderItemCommission = 0;
            $orderTotal = 0;
            $cartItems = json_decode($request['cartProducts']);
            foreach ($cartItems as $oI) {

                $originalItem = Item::where('id', $oI->id)->first();

                $originalItemCheck = Item::where('id', $oI->id)
                    ->where('restaurant_id', $restaurant_id)
                    ->first();

                if (!$originalItemCheck) {
                    // Soft delete the order if it was already created
                    if (isset($newOrder) && $newOrder->exists) {
                        $newOrder->delete();
                    }

                    // Soft delete the user
                    if ($user && !$user->trashed()) {
                        $user->delete();
                    }

                    // Log the abuse attempt
                    \Log::warning('Cross-restaurant item injection detected.', [
                        'user_id' => $user->id,
                        'restaurant_id' => $restaurant_id,
                        'item_id' => $oI->id,
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Your account has been suspended due to suspicious activity.'
                    ], 403);
                }
                if (isset($oI->quantity)) {
                    $orderTotal += ($originalItem->selling_price * $oI->quantity);
                    if (!is_null($originalItem->commision_rate)) {
                        $orderItemCommission += ($originalItem->commision_rate / 100 * ($originalItem->selling_price * $oI->quantity));
                    } else {
                        $orderItemCommission += ($originalItem->restaurant->commission_rate / 100 * ($originalItem->selling_price * $oI->quantity));
                    }
                }

                if (isset($oI->selectedaddons)) {
                    foreach ($oI->selectedaddons as $selectedaddon) {
                        $addon = Addon::where('id', $selectedaddon->addon_id)->first();
                        if ($addon) {
                            $orderTotal += ($addon->price * $selectedaddon->quantity);
                        }
                    }
                }
            }
            $newOrder->save();
            foreach ($cartItems as $orderItem) {
                $originalItem = Item::where('id', $orderItem->id)
                    ->where('restaurant_id', $restaurant_id)
                    ->first();

                if (!$originalItem) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid item detected.'
                    ], 400);
                }


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


                // $singleItem = Item::find($orderItem->id);
                // $singleItem->stock = $singleItem->stock - $singleItem->quantity;
                // $singleItem->save();
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
                            $addon->stock = $addon->stock - 1;
                        }
                    }
                }
            }
            $newOrder->sub_total = $orderTotal;
            $newOrder->save();
            if ($request->code) {
                $coupon = Coupon::where('coupon_code', $request->code)->with('items')->first();
                if ($coupon) {
                    if ($coupon->coupon_type == "RESTAURANT") {
                        $newOrder->coupon_code = $request->code;
                        if ($coupon->discount_type == 'PERCENTAGE') {
                            $percentage_discount = (($coupon->coupon_discount / 100) * $orderTotal);
                            if ($coupon->max_discount && $percentage_discount >= $coupon->max_discount) {
                                $percentage_discount = $coupon->max_discount;
                            }
                            $newOrder->coupon_amount = $percentage_discount;
                            $orderTotal -= $percentage_discount;
                        } else {
                            $newOrder->coupon_amount = $coupon->coupon_discount;
                            $orderTotal -= $coupon->coupon_discount;
                        }
                        $coupon->save();
                    } else {
                        $item_ids = $coupon->items->pluck('id')->toArray();
                        $couponTotal = 0;
                        foreach ($cartItems as $oI) {
                            if (in_array($oI->id, $item_ids)) {
                                $originalItem = Item::where('id', $oI->id)->first();
                                if (isset($oI->quantity)) {
                                    $couponTotal += ($originalItem->selling_price * $oI->quantity);
                                }

                                if (isset($oI->selectedaddons)) {
                                    foreach ($oI->selectedaddons as $selectedaddon) {
                                        $addon = Addon::where('id', $selectedaddon->addon_id)->first();
                                        if ($addon) {
                                            $couponTotal += (($addon->price + $originalItem->selling_price) * $selectedaddon->quantity);
                                        }
                                    }
                                }
                            }
                        }
                        $newOrder->coupon_code = $request->code;
                        if ($coupon->discount_type == 'PERCENTAGE') {
                            $percentage_discount = (($coupon->coupon_discount / 100) * $couponTotal);
                            if ($coupon->max_discount && $percentage_discount >= $coupon->max_discount) {
                                $percentage_discount = $coupon->max_discount;
                            }
                            $newOrder->coupon_amount = $percentage_discount;
                            $orderTotal -= $percentage_discount;
                        } else {
                            $newOrder->coupon_amount = $coupon->coupon_discount;
                            $orderTotal -= $coupon->coupon_discount;
                        }
                        $coupon->save();
                    }
                }
            }

            $taxAmount = $restaurant->tax > 0 ? (($restaurant->tax / 100) * $orderTotal) : 0;
            $newOrder->tax = $taxAmount;

            $orderTotal += $taxAmount + $restaurant->restaurant_charges;

            $newOrder->restaurant_total = $orderTotal;

            if ($restaurant->city && $restaurant->city->is_surge == 1) {
                $orderTotal += $restaurant->city->surge_fee;
                $newOrder->surge_fee = $restaurant->city->surge_fee;
            }

            $newOrder->total_commission = $orderItemCommission;

            if ($restaurant->city->delivery_charge_type == 'DYNAMIC' && $restaurant->city->base_delivery_distance && $restaurant->city->extra_delivery_distance && $restaurant->city->extra_delivery_charge && $restaurant->city->base_delivery_charge) {
                // $distance = (float) $request->distance;
                $distance = $this->getDistanceGoogle(
                    $restaurant->latitude,
                    $restaurant->longitude,
                    $Address->latitude,
                    $Address->longitude
                );

                if ($distance > $restaurant->city->base_delivery_distance) {
                    $extraDistance = $distance - $restaurant->city->base_delivery_distance;
                    $extraCharge = ($extraDistance / $restaurant->city->extra_delivery_distance) * $restaurant->city->extra_delivery_charge;
                    $dynamicDeliveryCharge = $restaurant->city->base_delivery_charge + $extraCharge;
                    $newOrder->delivery_charge = ceil($dynamicDeliveryCharge);
                    $orderTotal += ceil($dynamicDeliveryCharge);
                } else {
                    $newOrder->delivery_charge = $restaurant->city->base_delivery_charge;
                    $orderTotal += $restaurant->city->base_delivery_charge;
                }
            } else if ($restaurant->city->delivery_charge_type == 'FIXED' && $restaurant->city->delivery_charge > 0) {
                $newOrder->delivery_charge = $restaurant->city->delivery_charge;
                $orderTotal += $restaurant->city->delivery_charge;
            }

            if ($newOrder->delivery_charge > 200) {

                // Delete all order items if any were created
                OrderItem::where('order_id', $newOrder->id)->delete();

                // Soft delete the order
                if ($newOrder->exists) {
                    $newOrder->delete();
                }

                // Soft delete the user
                if ($user && !$user->trashed()) {
                    $user->delete();
                }

                \Log::warning('Suspicious delivery charge detected.', [
                    'user_id' => $user->id,
                    'order_id' => $newOrder->id,
                    'delivery_charge' => $newOrder->delivery_charge,
                    'distance' => $distance ?? null,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $newOrder
                ], 403);
            }

            $total = $orderTotal + (float) $request->platform_fee;
            if ($request['paymentMode'] == 'ONLINE') {
                $total += round($total * 0.0269 + 0.50, 2);
            }
            $newOrder->total = $total;
            // $newOrder->order_comment = $request['deliveryNote'];
            $newOrder->order_comment = $request['order_comment'];

            $newOrder->payment_mode = $request['paymentMode'];
            $newOrder->payment_status = "PENDING";
            $newOrder->restaurant_id = $request->restaurant_id;

            $newOrder->payout_amount = ($newOrder->sub_total + $newOrder->restaurant_charges) - $newOrder->total_commission;

            $newOrder->eta = 20;
            $newOrder->save();

            // Determine the heading and message based on the payment mode
            // if ($newOrder->payment_mode == 'ONLINE') {
            //     $vHeading = '📩 Prepaid Order Received!';
            //     $vMessage = 'Your order  ' . $newOrder->unique_order_id . ' has been received and fully prepaid. Please prepare it for delivery and ensure it is ready. Thank you for your prompt action!';
            // } else { // Assume it's COD
            //     $vHeading = '📩 COD Order Received!';
            //     $vMessage = 'Your order ' . $newOrder->unique_order_id . ' has been received with payment to be collected upon delivery. Please ensure the payment is collected from the customer upon delivery and confirm once done.';
            // }

            // $vendors = $restaurant->users;
            // foreach ($vendors as $vendor) {
            //     \App\Jobs\pushNotification::dispatch($vendor->id, $vMessage, $vHeading, 'vendor');
            // }

            // if ($newOrder->is_schedule == '1') {
            //     $vHeading = 'Order Scheduled';

            //     // Format the date and time
            //     $scheduleDate = Carbon::parse($newOrder->schedule_date)->format('F j, Y');


            //     // Generate the message
            //     $vMessage = 'Your order ' . $newOrder->unique_order_id . ' has been scheduled for delivery on ' . $scheduleDate . ' at ' . $newOrder->schedule_time . '. Please prepare it in advance to ensure timely delivery. Thank you!';
            // } else {
            // }

            // $vendors = $restaurant->users;
            // foreach ($vendors as $vendor) {
            // \App\Jobs\pushNotification::dispatch($newOrder->restaurant_id, $vMessage, $vHeading, 'vendor');
            // }

            activity()->performedOn($newOrder)
                ->causedBy(Auth::user())
                ->withProperties(['orderstatus' => '1'])
                ->log('Order Placed');


            $response = [
                'success' => true,
                'data' => $newOrder,
            ];

            if ($request['paymentMode'] != 'ONLINE') {
                // customer push
                $heading = 'Order Received';
                $message = 'We have received your order ' . $newOrder->unique_order_id . '! Please wait for the restaurant to confirm.';
                \App\Jobs\pushNotification::dispatch($user->id, $message, $heading, 'customer');

                // vendor push
                $vHeading = '📩 COD Order Received!';
                $vMessage = 'Your order ' . $newOrder->unique_order_id . ' has been received with payment to be collected upon delivery. Please ensure the payment is collected from the customer upon delivery and confirm once done.';

                $vendors = $restaurant->users;
                foreach ($vendors as $vendor) {
                    \App\Jobs\pushNotification::dispatch($vendor->id, $vMessage, $vHeading, 'vendor');
                }

                // whatsapp message
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
                            "PIKIT05"
                        ],
                        "buttonValues" => [
                            "1" => [
                                $newOrder->id
                            ]
                        ]
                    ],
                ];

                // SendInteraktMessage::dispatch($data);
            }

            return response()->json($response);
        }
    }

    public function getTrackOrder(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $trackOrder = Order::where('user_id', $user->id)->where('id', $request->order_id)->first();
            $response = [
                'success' => true,
                'data' => [
                    'trackOrder' => $trackOrder,
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
        $client = new \GuzzleHttp\Client();
        $apiKey = config('services.google.api_key');

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
}
