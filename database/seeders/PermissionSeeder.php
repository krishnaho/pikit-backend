<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            ['dashboard', 1, 'Dashboard'],

            ['live_orders', 2, 'Live Orders'],

            ['restaurant_categories', 3, 'Restaurant Categories'],
            ['restaurant', 3, 'Restaurant'],
            ['categories', 3, 'Categories'],

            ['item_categories', 4, 'Item Categories'],
            ['item_groups', 4, 'Item Groups'],
            ['addon_categories', 4, 'Addon Categories'],
            ['addons', 4, 'Addons'],
            ['items', 4, 'Items'],


            ['all_users', 5, 'All Users'],
            ['restaurant_owner', 5, 'Restaurant Owners'],
            ['customers', 5, 'Customers'],

            ['banners', 6, 'Banners'],
            ['coupons', 6, 'Coupons'],

            ['date_wise_report', 7, 'Date Wise Report'],
            ['restaurant_payout_report', 7, 'Restaurant Payout Report'],
            ['delivery_collection_report', 7, 'Delivery Collection Report'],
            ['delivery_collection_log_report', 7, 'Delivery Collection Log Report'],

            ['permissions', 8, 'Permissions'],
            ['cities', 8, 'Cities'],
        ];
        foreach ($permissions as $data) {
            Permission::create([
                'name' => $data[0],
                'permission_head_id' => $data[1],
                'readable_name' => $data[2]
            ]);
        }
    }
}
