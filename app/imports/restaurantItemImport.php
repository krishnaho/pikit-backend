<?php

namespace App\Imports;

use App\Models\Item;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Carbon\Carbon;

class restaurantItemImport implements ToModel, WithHeadingRow
{
    private $rows = 0;

    public function model(array $row)
    {


        if (empty($row['id']) && empty($row['name']) && empty($row['restaurant_id'])) {
            return null;
        }


        ++$this->rows;

        $id = $row['id'] === 'NULL' ? null : $row['id'];
        $restaurant_id = $row['restaurant_id'] === 'NULL' ? null : $row['restaurant_id'];
        $item_category_id = $row['item_category_id'] === 'NULL' ? null : $row['item_category_id'];
        $name = $row['name'] === 'NULL' ? null : $row['name'];
        $description = $row['description'] === 'NULL' ? null : $row['description'];
        $market_price = $row['market_price'] === 'NULL' ? null : $row['market_price'];
        $selling_price = $row['selling_price'] === 'NULL' ? null : $row['selling_price'];
        $commision_rate = $row['commision_rate'] === 'NULL' ? null : $row['commision_rate'];
        $min_quantity = $row['min_quantity'] === 'NULL' ? null : $row['min_quantity'];
        $max_quantity = $row['max_quantity'] === 'NULL' ? '0' : $row['max_quantity'];
        $is_schedule = $row['is_schedule'] === 'NULL' ? '0' : $row['is_schedule'];
        $is_popular = $row['is_popular'] === 'NULL' ? '0' : $row['is_popular'];
        $is_top_item = $row['is_top_item'] === 'NULL' ? '0' : $row['is_top_item'];
        $is_recommended = $row['is_recommended'] === 'NULL' ? '0' : $row['is_recommended'];
        $is_veg = $row['is_veg'] === 'NULL' ? '0' : $row['is_veg'];
        $is_active = $row['is_active'] === 'NULL' ? '0' : $row['is_active'];


        $item = Item::where('id', $id)->first();

        info($item);

        if ($item) {
            $item->update([
                'restaurant_id' => $restaurant_id,
                'item_category_id' => $item_category_id,
                'name' => $name,
                'description' => $description,
                'market_price' => $market_price,
                'selling_price' => $selling_price,
                'commision_rate' => $commision_rate,
                'min_quantity' => $min_quantity,
                'max_quantity' => $max_quantity,
                'is_schedule' => $is_schedule,
                'is_popular' => $is_popular,
                'is_top_item' => $is_top_item,
                'is_recommended' => $is_recommended,
                'is_veg' => $is_veg,
                'is_active' => $is_active,
                // 'image' => $image,
                'updated_at' => Carbon::now(),
            ]);
            return $item;
        } else {
            return new Item([
                'restaurant_id' => $restaurant_id,
                'item_category_id' => $item_category_id,
                'name' => $name,
                'description' => $description,
                'market_price' => $market_price,
                'selling_price' => $selling_price,
                'commision_rate' => $commision_rate,
                'min_quantity' => $min_quantity,
                'max_quantity' => $max_quantity,
                'is_schedule' => $is_schedule,
                'is_popular' => $is_popular,
                'is_recommended' => $is_recommended,
                'is_veg' => $is_veg,
                'is_active' => $is_active,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
