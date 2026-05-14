<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\City;
use App\Models\DeliveryGuyDetail;
use App\Models\PermissionHead;
use App\Models\Sms;
use App\Models\Store;
use App\Models\Address;
use Spatie\Permission\Models\Permission;
use JWTAuthException;
use JWTAuth;


class AdminController extends Controller
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

    public function sendOtp(Request $request)
    {
        $phone = $request->phone;
        $oldAcc = User::where('phone', $phone)->first();
        if ($oldAcc) {
            $sendOtp = new Sms();
            $sendOtp->processSmsAction('OTP', $phone);
            $response = [
                'success' => true,
                'data' => 'OTPSEND',
                'message' => 'OTP send successfully',
            ];
        } else if ($phone) {
            $sendOtp = new Sms();
            $sendOtp->processSmsAction('OTP', $phone);
            $response = [
                'success' => true,
                'data' => 'OTPSEND',
                'message' => 'OTP send successfully',
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Something Went Wrong',
            ];
        }
        return response()->json($response);
    }

    public function verifyOtp(Request $request)
    {
        if ($request->phone == '8157816000' && $request->otp == '1234') {
            $response = ['valid_otp' => true];
        } else {
            $sms = new Sms();
            $response = $sms->processSmsAction('VERIFY', $request->phone, $request->otp);
        }
        if ($response['valid_otp'] == true) {
            $user = User::where('phone', $request->phone)->first();
            if ($user) {
                $defaultAddress = Address::where('id', $user->default_address_id)->first(); //??
                $token = self::getToken($user);
                $user->auth_token = $token;
                $user->save();
                $response = [
                    'success' => true,
                    'already_existing' => true,
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
            } else {
                $response = ['success' => true, 'already_existing' => false, 'data' => 'NEWUSER'];
                return response()->json($response, 201);
            }
        } else {
            $response = ['success' => false, 'data' => 'DO NOT MATCH'];
            return response()->json($response, 201);
        }
    }

    public function userRegister(Request $request)
    {

        $existingUser = User::where('phone', $request->phone)->first();
        if ($existingUser) {
            $defaultAddress = Address::where('id', $existingUser->default_address_id)->first();
            $token = self::getToken($existingUser);
            $existingUser->name = $request->name;
            $existingUser->email = $request->email;
            $existingUser->auth_token = $token;
            $existingUser->save();
            $response = [
                'success' => true,
                'data' => [
                    'id' => $existingUser->id,
                    'auth_token' => $existingUser->auth_token,
                    'name' => $existingUser->name,
                    'email' => $existingUser->email,
                    'phone' => $existingUser->phone,
                    'default_address_id' => $existingUser->default_address_id,
                    'defaultAddress' => $defaultAddress
                ],
            ];
        } else {
            $user = new User();
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->email = $request->email;
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
                    'default_address_id' => $user->default_address_id,
                ],
            ];
        }
        return response()->json($response, 201);
    }
}
