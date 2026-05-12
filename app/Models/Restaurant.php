<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ChristianKuri\LaravelFavorite\Traits\Favoriteable;


class Restaurant extends Model
{
    use HasFactory, Favoriteable;
    protected $guarded = [];

    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        return $this;
    }

    public function restaurantCategory()
    {
        return $this->belongsTo(RestaurantCategory::class, 'restaurant_category_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function addoncategories()
    {
        return $this->hasMany(AddonCategory::class);
    }
    public function itemCategories()
    {
        return $this->hasMany(ItemCategory::class);
    }


    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurant_user', 'restaurant_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
