<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonCategory extends Model
{
    use HasFactory;

    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        return $this;
    }
    public function addons()
    {
        return $this->hasMany(Addon::class);
    }
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
