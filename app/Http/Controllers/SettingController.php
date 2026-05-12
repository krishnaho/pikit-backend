<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Jobs\SendInteraktMessage;

class SettingController extends Controller
{
    public function test() {}

    public function processPayment($id)
    {
        $order = Order::find($id);

        $array = explode(',', $order->address);
        $user = $order->user;
        $prefix = strstr($user->email, '@', true);

        $store_id = 30487;
        $auth_key = 'sgVBc~3cDwb#fTwv';

        $apiUrl = 'https://secure.telr.com/gateway/order.json';
        $postData = [
            "method" => "create",
            "store" => $store_id,
            "authkey" => $auth_key,
            "framed" => 3,
            "language" => "en",
            "ivp_applepay" => "0",
            "order" => [
                "cartid" => $order->id,
                "test" => "0",
                "amount" => $order->total,
                "currency" => "₹",
                "description" => $order->unique_order_id,
                "trantype" => "sale"
            ],
            "customer" => [
                "ref" => $user->id,
                "email" => $user->email ? $user->email : 'test@test.com',
                "name" => [
                    "forenames" => $user->name,
                    "surname" => $prefix && $prefix != $user->name ? $prefix : 'A'
                ],
                "address" => [
                    "line1" => $array[0] ?? $order->address,
                    "city" => $array[1] ?? $order->address,
                    "country" => "AE"
                ],
                "phone" => $user->phone
            ],
            "return" => [
                "authorised" => 'https://zeato.howincloud.com/public/handle-payment/success/' . $order->id,
                "declined" => 'https://zeato.howincloud.com/public/handle-payment/cancel/' . $order->id,
                "cancelled" => 'https://zeato.howincloud.com/public/handle-payment/cancel/' . $order->id
            ]
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);

        if ($response === false) {
            $errorMessage = curl_error($ch);
            curl_close($ch);

            echo 'Something went wrong!';
        }

        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpStatus !== 200) {
            $errorMessage = $responseData['error'] ?? 'An error occurred';
            echo 'Something went wrong!';
        }

        if (isset($responseData['order']['url'])) {
            $payUrl = $responseData['order']['url'];
        } else {
            $errorMessage = 'Invalid API response: Missing or incorrect "order" data.';
            echo 'Something went wrong!';
        }

        return redirect()->to($payUrl);
    }

    public function handleSuccess($id)
    {
        $store_id = 30487;
        $auth_key = 'sgVBc~3cDwb#fTwv';

        $order = Order::find($id);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
        ])->post('https://secure.telr.com/gateway/order.json', [
            'method' => 'check',
            'store' => $store_id,
            'authkey' => $auth_key,
            'order' => [
                'ref' => $order->cart_id
            ]
        ]);

        $response_data = json_decode($response->body(), true);
        $order->payment_status = $response_data['order']['status']['text'];
        $order->order_status_id = 1;
        $order->save();
        
        // Send push notification to customer
        $heading = '📩 Prepaid Order Recieved!';
        $message = 'Your order ' . $order->unique_order_id . 'has been received and fully prepaid. Please prepare it for delivery and ensure it is ready 
        by ' . Carbon::now()->addMinutes($order->restaurant->approx_time_delivery)->format('h:i A') . '. Thank you for your prompt action!';
        \App\Jobs\pushNotification::dispatch($order->user_id, $message, $heading, 'customer');

        // whatsapp message
        $data = [
            "countryCode" => "+971",
            "phoneNumber" => $order->user->phone,
            "type" => "Template",
            "template" => [
                "name" => "order_placed",
                "languageCode" => "en",
                "bodyValues" => [
                    $order->user->name,
                    $order->total,
                    $order->restaurant->name,
                    $order->restaurant->address,
                    "ZEATO05"
                ],
                "buttonValues" => [
                    "1" => [
                        $order->id
                    ]
                ]
            ],
        ];

        SendInteraktMessage::dispatch($data);

        // vendor push
        $vHeading = '📩 COD Order Received!';
        $vMessage = 'Your order ' . $order->unique_order_id . ' has been received with payment to be collected upon delivery. Please ensure the payment is collected from the customer upon delivery and confirm once done.';

        $vendors = $order->restaurant->users;
        foreach ($vendors as $vendor) {
            \App\Jobs\pushNotification::dispatch($vendor->id, $vMessage, $vHeading, 'vendor');
        }

        $redirectUrl = 'https://zeatoapp.com/payment-sucess/' . $order->id;
        return redirect()->away($redirectUrl);
    }

    public function handleCancel($id)
    {
        $store_id = 30487;
        $auth_key = 'sgVBc~3cDwb#fTwv';

        $order = Order::find($id);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
        ])->post('https://secure.telr.com/gateway/order.json', [
            'method' => 'check',
            'store' => $store_id,
            'authkey' => $auth_key,
            'order' => [
                'ref' => $order->cart_id
            ]
        ]);

        $response_data = json_decode($response->body(), true);
        if (isset($response_data['order'])) {
            $order->payment_status = $response_data['order']['status']['text'];
        } else {
            $order->payment_status = 'Failed';
        }

        $order->order_status_id = 9;
        $order->save();
        $heading = 'Payment Failed';
        $message = 'Your payment for order no:' . $order->unique_order_id . 'was not completed. If any amount debited from your account will get refunded within 5-7 days.';
        \App\Jobs\pushNotification::dispatch($order->user_id, $message, $heading, 'customer');

        $redirectUrl = 'https://zeatoapp.com/payment-failed/' . $order->id;
        return redirect()->away($redirectUrl);
    }

    public function viewsettings()
    {
        $settings = Settings::get();
        $platFormfee = $settings->where('key', 'platform_fee')->first();

        return view('admin.settings.viewSettings', [
            'platFormfee' => $platFormfee
        ]);
    }

    public function platformFee(Request $request)
    {
        $key = Settings::where('key', $request->key)->first();
        if (!$key) {
            $data = new Settings();
            $data->key = $request->key;
            $data->save();
        } else {
            $data = Settings::where('key', $request->key)->first();
        }
        try {
            if ($data) {
                $data->value = $request->platform_fee;
                $data->save();
                return redirect()->back()->with(['success' => 'Referrer Order Amount Updated']);
            } else {
                return redirect()->back()->with(['error' => 'Something Went Wrong.']);
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['error' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }
}
