<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        return $this;
    }

    public function toggleSurgeActive()
    {
        $this->is_surge = !$this->is_surge;
        return $this;
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
