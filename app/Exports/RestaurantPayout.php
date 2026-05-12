<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Auth;
use Carbon\Carbon;

use Illuminate\Contracts\Queue\ShouldQueue;

class RestaurantPayout implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, ShouldQueue, WithCustomChunkSize
{
    use Exportable;
    private $year;
    /**
     * @return \Illuminate\Support\Collection
     */


    public function chunkSize(): int
    {
        return 5000;
    }

    public function recieveData($restaurants, $status)
    {
        $this->restaurants = $restaurants;
        $this->status = $status;
    }

    public function collection()
    {
        return $this->restaurants;
    }
    public function map($restaurant): array
    {
        $last = $restaurant->orders->last();
        $date = $last->created_at->format('d/m/Y h:i A');
        $item_amount = $restaurant->orders->sum('sub_total');
        $item_tax = $restaurant->orders->sum('tax');
        $restaurant_charges = $restaurant->orders->sum('restaurant_charges');
        $commssion = $restaurant->orders->sum('total_commission');
        $payout = $restaurant->orders->sum('payout_amount');
        $status = $this->status;
        return [
            $date,
            $restaurant->name,
            $item_amount,
            $item_tax,
            $restaurant_charges,
            $commssion,
            $payout,
            $status,
        ];
    }
    public function headings(): array
    {
        return [
            'Date',
            'Name',
            'Item Amount',
            'Item Tax',
            'Restaurant Charges',
            'Commission',
            'Payout Amount',
            'Status'
        ];
    }
}
