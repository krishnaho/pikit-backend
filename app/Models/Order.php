<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public function orderstatus()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function review()
    {
        return $this->hasOne(Review::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payoutReleases()
    {
        return $this->belongsToMany(PayoutRelease::class);
    }
}
