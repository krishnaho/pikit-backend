<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRelease extends Model
{
    use HasFactory;


  
    public function user()
    {
        return $this->belongsTo(User::class, 'payout_released_by');
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
