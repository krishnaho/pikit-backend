<?php

namespace App\Http\Controllers;

use App\Exports\dateWiseExport;
use App\Exports\RestaurantPayout;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Order;
use App\Models\PayoutRelease;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function viewDatewiseReport(Request $request)
    {
        $cityIds = Auth::user()->cities->pluck('id')->toArray();
        $cities = City::whereIn('id', $cityIds)->get();
        $restaurants = Restaurant::whereIn('city_id', $cityIds)->get();
        return view('admin.report.dateWiseReport', array(
            'cities' => $cities,
            'restaurants' => $restaurants,
        ));
    }

    public function exportDateWise(Request $request)
    {
        $from = Carbon::parse($request->start_date);
        $to = Carbon::parse($request->end_date);
        $export = new dateWiseExport();
        ob_end_clean();
        $export->recieveData($from, $to, $request->restaurant_id, $request->city_id);
        return Excel::download($export, 'DateWiseSales.xlsx');
    }


    public function viewRestaurantPayoutReport(Request $request)
    {

        $city_Ids = Auth::user()->cities->pluck('id')->toArray();
        if ($request->start_date && $request->end_date) {
            $from = Carbon::parse($request->start_date);
            $to = Carbon::parse($request->end_date);
            $restaurants = Restaurant::where('is_deleted', 0)
                ->whereIn('city_id', $city_Ids)
                ->whereHas('orders', function ($q) use ($from, $to) {
                    $q->where('order_status_id', 7)
                        ->whereBetween('created_at', [$from, $to])
                        ->where('is_payout_released', 0);
                })
                ->with(['orders' => function ($q) use ($from, $to) {
                    $q->where('order_status_id', 7)
                        ->whereBetween('created_at', [$from, $to])
                        ->where('is_payout_released', 0);
                }])
                ->get();
        } else {
            $restaurants = Restaurant::where('is_deleted', 0)
                ->whereIn('city_id', $city_Ids)
                ->whereHas('orders', function ($q) {
                    $q->where('order_status_id', 7)
                        ->where('is_payout_released', 0);
                })
                ->with(['orders' => function ($q) {
                    $q->where('order_status_id', 7)
                        ->where('is_payout_released', 0);
                }])
                ->get();
        }
        return view('admin.report.viewRestaurantPayoutReport', array(
            'restaurants' => $restaurants,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ));
    }


    public function viewCompletedRestaurantPayouts()
    {
        $city_Ids = Auth::user()->cities->pluck('id')->toArray();
        $restaurants = Restaurant::where('is_deleted', 0)
            ->whereIn('city_id', $city_Ids)
            ->whereHas('orders', function ($q) {
                $q->where('order_status_id', 7);
            })
            ->with(['orders' => function ($q) {
                $q->where('order_status_id', 7);
            }])
            ->get();
        return view('admin.report.newRestaurantCompletedPayout', array(
            'restaurants' => $restaurants,
        ));
    }

    public function exportRestaurantPayout(Request $request)
    {
        $city_Ids = Auth::user()->cities->pluck('id')->toArray();
        if ($request->start_date && $request->end_date && $request->type == 'PENDING') {
            $from = Carbon::parse($request->start_date);
            $to = Carbon::parse($request->end_date);
            $restaurants = Restaurant::where('is_deleted', 0)
                ->whereIn('city_id', $city_Ids)
                ->whereHas('orders', function ($q) use ($from, $to) {
                    $q->where('order_status_id', 7)->whereBetween('created_at', [$from, $to])
                        ->where('is_payout_released', 0);
                })
                ->with(['orders' => function ($q) use ($from, $to) {
                    $q->where('order_status_id', 7)->whereBetween('created_at', [$from, $to])
                        ->where('is_payout_released', 0);
                }])
                ->get();
            $export = new RestaurantPayout();
            ob_end_clean();
            $status = "PENDING";
            $export->recieveData($restaurants, $status);
            return Excel::download($export, 'RestaurantPayoutReport.xlsx');
        } elseif ($request->type == 'COMPLETED') {
            $restaurants = Restaurant::where('is_deleted', 0)
                ->whereIn('city_id', $city_Ids)
                ->whereHas('orders', function ($q) {
                    $q->where('order_status_id', 7)
                        ->where('is_payout_released', 1);
                })
                ->with(['orders' => function ($q) {
                    $q->where('order_status_id', 7)
                        ->where('is_payout_released', 1);
                }])
                ->get();
            $export = new RestaurantPayout();
            ob_end_clean();
            $status = 'COMPLETED';
            $export->recieveData($restaurants, $status);
            return Excel::download($export, 'RestaurantPayoutReport.xlsx');
        }
    }

    public function releasePayouts(Request $request)
    {
        $request->validate([
            'order_ids' => 'required',
            'restaurant_id' => 'required',
            'payout_amount' => 'required',
            'message' => 'nullable',
        ]);

        $payout_release = new PayoutRelease();
        $payout_release->order_ids = $request->order_ids;
        $payout_release->restaurant_id = $request->restaurant_id;
        $payout_release->payout_amount = $request->payout_amount;
        $payout_release->payout_released_by = Auth::user()->id;
        $payout_release->message = $request->message;
        $payout_release->save();

        $orders = Order::whereIn('id', json_decode($request->order_ids, true))->get();
        foreach ($orders as $order) {
            $order->is_payout_released = 1;
            $order->save();
        }

        return redirect()->back()->with('success', 'Payout Updated successfully');
    }



    public function viewCompletedRestaurantPayoutReport(Request $request)
    {

        $city_Ids = Auth::user()->cities->pluck('id')->toArray();
        if ($request->start_date && $request->end_date) {
            $from = Carbon::parse($request->start_date);
            $to = Carbon::parse($request->end_date);
            $restaurants = Restaurant::where('is_deleted', 0)
                ->whereIn('city_id', $city_Ids)
                ->whereHas('orders', function ($q) use ($from, $to) {
                    $q->where('order_status_id', 7)
                        ->whereBetween('created_at', [$from, $to])
                        ->where('is_payout_released', 1);
                })
                ->with(['orders' => function ($q) use ($from, $to) {
                    $q->where('order_status_id', 7)
                        ->whereBetween('created_at', [$from, $to])
                        ->where('is_payout_released', 1);
                }])
                ->get();
        } else {
            $restaurants = Restaurant::where('is_deleted', 0)
                ->whereIn('city_id', $city_Ids)
                ->whereHas('orders', function ($q) {
                    $q->where('order_status_id', 7)
                        ->where('is_payout_released', 1);
                })
                ->with(['orders' => function ($q) {
                    $q->where('order_status_id', 7)
                        ->where('is_payout_released', 1);
                }])
                ->get();
        }
        return view('admin.report.viewCompletedRestaurantPayoutReport', array(
            'restaurants' => $restaurants,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ));
    }
}
