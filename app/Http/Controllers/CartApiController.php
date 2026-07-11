<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Addon;
use App\Models\Item;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use GuzzleHttp\Client;

class CartApiController extends Controller
{
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
        \Log::info('getDistanceGoogle: Started distance calculation', [
            'originLat' => $originLat,
            'originLng' => $originLng,
            'destLat' => $destLat,
            'destLng' => $destLng
        ]);

        $client = new Client();
        $apiKey = 'AIzaSyCXJoxjl9n-5zU7unjCFzUZyTEK0kqsyHA';

        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';

        $params = [
            'origins' => "{$originLat},{$originLng}",
            'destinations' => "{$destLat},{$destLng}",
            'key' => $apiKey,
        ];

        try {
            \Log::info('getDistanceGoogle: Calling Google Distance Matrix API', ['params' => $params]);
            $response = $client->get($url, ['query' => $params]);
            $data = json_decode($response->getBody(), true);
            \Log::info('getDistanceGoogle: Google Distance Matrix API response received', ['data' => $data]);

            if (!empty($data['rows'][0]['elements'][0]['distance'])) {
                $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];
                $distanceInKm = $distanceInMeters / 1000;
                \Log::info('getDistanceGoogle: Using Google Maps driving distance', ['distance_km' => $distanceInKm]);

                return $distanceInKm;
            }

            \Log::warning('getDistanceGoogle: Google Maps response elements empty. Falling back to air distance.');
            $restaurant_distance = $this->getDistance($originLat, $originLng, $destLat, $destLng);
            \Log::info('getDistanceGoogle: Fallback air distance calculated', ['distance_km' => $restaurant_distance]);

            return $restaurant_distance;
        } catch (\Exception $e) {
            \Log::error('getDistanceGoogle: Google Maps API exception occurred. Falling back to air distance.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $restaurant_distance = $this->getDistance($originLat, $originLng, $destLat, $destLng);
            \Log::info('getDistanceGoogle: Fallback air distance calculated', ['distance_km' => $restaurant_distance]);

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
        $subTotal = 0;
        $restaurantIds = collect([]);
        if ($products) {

            foreach ($products as $oI) {
                $originalItem = Item::where('id', $oI->id)->first();
                $restaurantIds->push($oI->restaurant->id);

                if (isset($oI->quantity)) {
                    $subTotal += ($originalItem->selling_price * $oI->quantity);
                }

                if (isset($oI->selectedaddons)) {
                    foreach ($oI->selectedaddons as $selectedaddon) {
                        $addon = Addon::where('id', $selectedaddon->addon_id)->first();
                        if ($addon) {
                            $subTotal += (($addon->price + $originalItem->selling_price) * $selectedaddon->quantity);
                        }
                    }
                }
            }

            $filteredrestaurantIds = $restaurantIds->unique();
            foreach ($filteredrestaurantIds as $restaurant_id) {
                $restaurant = Restaurant::where('id', $restaurant_id)->where('is_active', 1)->where('is_accepted', 1)->first();
                if ($restaurant) {
                    $restaurant_charges += $restaurant->restaurant_charge;
                    $taxAmount = $restaurant->tax > 0 ? (($restaurant->tax / 100) * $subTotal) : 0;

                    $tax += $taxAmount;
                }
            }

            $charges['restaurant_charges'] = (float) ((float) $restaurant_charges);
            $charges['tax'] = (float) ((float) $tax);
            $response = [
                'success' => true,
                'restaurant_charge' => (float) ((float) $charges['restaurant_charges']),
                'tax' => (float) ((float) $charges['tax']),
                'restaurant' => $subTotal,
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'CART PRODUCTS NOT FOUND',
            ];
        }
        return response($response);
    }

    public function applyCoupon(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            $response = [
                'success' => false,
                'type' => 'NOTLOGGEDIN',
            ];
            return response()->json($response);
        }
        $coupon = Coupon::where('coupon_code', $request->coupon)->where('is_active', 1)->with("restaurants", 'items')->first();
        $item_restaurant_ids = $coupon->items->pluck('restaurant_id')->toArray();
        if ($coupon) {
            if (in_array($request->restaurant_id, $coupon->restaurants()->pluck('restaurant_id')->toArray()) || in_array($request->restaurant_id, $item_restaurant_ids)) {
                if (Carbon::parse($coupon->end_date)->gt(Carbon::now()) && $coupon->count < $coupon->max_count) {
                    if ($request->subtotal >= $coupon->min_sub_total) {
                        $userOrderCount = count($user->orders);

                        if ($coupon->coupon_type == "ITEM") {
                            $coupon->item_ids = $coupon->items->pluck('id')->toArray();
                        } else {
                            $coupon->item_ids = ["All"];
                        }
                        if ($coupon->user_type == 'ONCE') {
                            $orderAlreadyPlacedWithCoupon = Order::where('user_id', $user->id)->where('coupon_code', $coupon->code)->first();
                            if ($orderAlreadyPlacedWithCoupon) {
                                $response = [
                                    'success' => false,
                                    'type' => 'ALREADYUSEDONCE',
                                    'message' => 'This coupon can only be used once per one user',
                                ];
                                return response()->json($response);
                            }
                        }
                        if ($coupon->user_type == 'ONCENEW') {
                            if ($userOrderCount != 0) {
                                $response = [
                                    'success' => false,
                                    'type' => 'FORNEWUSER',
                                    'message' => 'This coupon can only be used for first order',
                                ];
                                return response()->json($response);
                            }
                        }
                        if ($coupon->user_type == 'CUSTOM') {
                            $orderAlreadyPlacedWithCoupon = Order::where('user_id', $user->id)->where('coupon_code', $coupon->code)->get()->count();
                            if ($orderAlreadyPlacedWithCoupon >= $coupon->max_count_per_user) {
                                $response = [
                                    'success' => false,
                                    'type' => 'MAXLIMITREACHEDPERUSER',
                                    'message' => 'Max limit reached for this coupon',
                                ];
                                return response()->json($response);
                            }
                        }
                        $coupon->success = true;
                        return response()->json($coupon);
                    } else {
                        $response = [
                            'success' => false,
                            'type' => 'MINSUBTOTAL',
                            'message' => $coupon->sub_total_message,
                        ];
                        return response()->json($response);
                    }
                } else {
                    $response = [
                        'success' => false,
                        'message' => "coupon is expired",
                    ];
                    return response()->json($response);
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => "restaurant not matching",
                ];
                return response()->json($response);
            }
        } else {
            $response = [
                'success' => false,
                'message' => "coupon not found",
            ];
            return response()->json($response);
        }
    }

    public function getAllCartCoupons(Request $request)
    {
        $authUser = Auth::user();
        $item_ids = collect([]);
        $cartProducts = json_decode($request->cartProducts);
        foreach ($cartProducts as $cartProduct) {
            $item_ids->push($cartProduct->id);
        }
        $restaurant_id = $request->restaurant_id;
        if ($authUser) {
            $restaurantCoupons = Coupon::where('is_active', 1)
                ->where('coupon_type', 'restaurant')
                ->with('restaurants', 'items')
                ->whereHas('restaurants', function ($q) use ($restaurant_id) {
                    $q->where("restaurant_id", $restaurant_id);
                })
                ->get();

            $itemCoupons = Coupon::where('is_active', 1)
                ->where('coupon_type', 'ITEM')
                ->with('restaurants', 'items')
                ->whereHas('items', function ($q) use ($item_ids) {
                    $q->whereIn("item_id", $item_ids);
                })
                ->get();

            $coupons = collect();
            if ($restaurantCoupons->isNotEmpty()) {
                $coupons = $restaurantCoupons;
            }

            if ($itemCoupons->isNotEmpty()) {
                $coupons = $coupons->merge($itemCoupons);
            }

            if ($coupons->count() > 0) {

                $filterdCoupons = new Collection();
                foreach ($coupons as $coupon) {
                    if (
                        Carbon::parse($coupon->end_date)->isFuture() &&
                        $coupon->count < $coupon->max_count &&
                        ($coupon->restaurants->count() > 0 || $coupon->items->count() > 0)
                    ) {
                        $userOrderCount = count($authUser->orders);
                        if ($coupon->user_type == 'ONCE') {
                            $orderAlreadyPlacedWithCoupon = Order::where('user_id', $authUser->id)->where('coupon_code', $coupon->code)->first();
                            if ($orderAlreadyPlacedWithCoupon) {
                                continue;
                            }
                        }
                        if ($coupon->user_type == 'ONCENEW') {
                            if ($userOrderCount != 0) {
                                continue;
                            }
                        }
                        if ($coupon->user_type == 'CUSTOM') {
                            $orderAlreadyPlacedWithCoupon = Order::where('user_id', $authUser->id)->where('coupon_code', $coupon->code)->get()->count();
                            if ($orderAlreadyPlacedWithCoupon >= $coupon->max_count_per_user) {
                                continue;
                            }
                        }
                        $filterdCoupons->push($coupon);
                    } else {
                        continue;
                    }
                }
                $response = [
                    'success' => true,
                    'coupons' => $filterdCoupons,
                ];
            } else {
                $response = [
                    'success' => false,
                    'coupons' => "no counpon",
                ];
            }
        } else {
            $response = [
                'success' => true,
                'data' => '401 USER UNAUTHORIZED'
            ];
        }
        return response()->json($response, 200);
    }
}
