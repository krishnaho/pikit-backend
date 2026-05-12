<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemGroup extends Model
{
    use HasFactory; 

    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        return $this;
    }
    public function restaurantCategory()
    {
        return $this->belongsTo(RestaurantCategory::class, 'restaurant_category_id');
    }


    public function items()
    {
        return $this->belongsToMany(Item::class);
    }
}
