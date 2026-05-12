<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;
    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        return $this;
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function restaurant_category()
    {
        return $this->belongsTo(RestaurantCategory::class, 'restaurant_category_id');
    }
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class);
    }
    public function items()
    {
        return $this->belongsToMany(Item::class);
    }
}
