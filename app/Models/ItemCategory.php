<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;

    public function toggleActive()
    {
        // Toggle the is_active status of the category
        $this->is_active = !$this->is_active;
        $this->save(); // Save the changes to the category

        // Toggle the is_active status of all related items
        foreach ($this->items as $item) {
            $item->is_active = $this->is_active; // Set item's status to match the category's status
            $item->save(); // Save each item
        }

        return $this;
    }
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function restaurantCategory()
    {
        return $this->belongsTo(RestaurantCategory::class, 'restaurant_category_id');
    }
}
