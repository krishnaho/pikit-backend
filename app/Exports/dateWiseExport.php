<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Auth;
use Carbon\Carbon;

use Illuminate\Contracts\Queue\ShouldQueue;

class dateWiseExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, ShouldQueue, WithCustomChunkSize
{
    use Exportable;
    private $year;
    /**
     * @return \Illuminate\Support\Collection
     */


    public function chunkSize(): int
    {
        return 7000;
    }

    public function recieveData($from, $to, $restaurant_id, $city_id)
    {
        // dd($from);
        $this->from = $from;
        $this->to = $to;
        $this->restaurant_id = $restaurant_id;
        $this->city_id = $city_id;
    }

    public function collection()
    {
        $authUser = Auth::user();
        $cityIds = $authUser->cities->pluck('id')->toArray();
        if ($this->city_id == 'All' && $this->restaurant_id == 'All') {
            $orders = Order::whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to)->with('restaurant')
            ->whereIn('city_id', $cityIds)
            ->where('order_status_id', '7')
            ->get();
            // dd($orders->created_at);
        } elseif ($this->city_id != 'All' && $this->restaurant_id == 'All') {
            $orders = Order::whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to)->with('restaurant')
                ->where('city_id', $this->city_id)
                ->where('order_status_id', '7')
                ->get();
            } elseif ($this->city_id == 'All' && $this->restaurant_id != 'All') {
            $orders = Order::whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to)->with('restaurant')
            ->where('restaurant_id', $this->restaurant_id)
            ->where('order_status_id', '7')
                ->get();
        } else {
            $orders = Order::whereBetween('created_at', [$this->from, $this->to])->with('restaurant')->where('restaurant_id', $this->restaurant_id)->where('order_status_id', 7)->get();
        }

        $order_data = $orders;
        return $order_data;
    }
    public function map($order): array
    {
        if ($order->restaurant_charges) {
            $restaurantCharge = $order->restaurant_charges;
        } else {
            $restaurantCharge = '0';
        }

        // Coupon Discount Calculation
        if ($order->coupon_code) {
            $couponName = $order->coupon_code;
            $couponDiscount =  $order->coupon_discount;
        } else {
            $couponDiscount = '0';
            $couponName = 'Coupon Not Applied';
        }

        // // Wallet Amount
        if ($order->walletamount > 0) {
            $walletamount = $order->walletamount;
        } else {
            $walletamount = '0';
        }

        return [
            $order->created_at->format('d/m/Y h:i A'),
            $order->id,
            $order->unique_order_id,
            $order->restaurant ? $order->restaurant->name : "Any Restaurant",
            $restaurantCharge,
            $order->tax,
            $order->total_commission,
            $order->surge_fee,
            $order->sub_total,
            $order->delivery_charge,
            $couponName,
            $couponDiscount,
            $order->sub_total,
            $order->payment_mode,
            $walletamount,
            $order->payout_amount,
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Order ID',
            'ORDER UNIQUE ID',
            'Restaurant Name',
            'Restaurant Charge',
            'Tax',
            'Commission Amount',
            'Surge Fee',
            'Item Total',
            'Delivery Charge',
            'Coupon Name',
            'Coupon Discount',
            'Order Total',
            'Payment Mode',
            'Wallet Amount Used',
            'Restaurant Payout Amount',
        ];
    }
}
