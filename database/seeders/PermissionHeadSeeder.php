<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $status = [
            'Dashboard',
            'Live Orders',
            'Restaurant & Category',
            'Inventory Management',
            'Users Management',
            'Promotions Management',
            'Report Management',
            'Others Management',
        ];
        foreach ($status as $data) {
            DB::table('permission_heads')->insert([
                'name' => $data
            ]);
        }
    }
}
