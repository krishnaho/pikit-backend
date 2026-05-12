<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback', 'image', 'rating'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
