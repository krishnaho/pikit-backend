<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = [
            'Order Placed',
            'Order Accepted',
            'Order Preparing',
            'Ready to Pick Up',
            'Delivery Agent Assigned',
            'Picked Up',
            'Completed',
            'Cancelled',
            'Transaction Failed',
            'Transaction Pending',
            'Self Pick Up',
        ];
        foreach ($status as $data) {
            DB::table('order_statuses')->insert([
                'name' => $data
            ]);
        }
    }
}
