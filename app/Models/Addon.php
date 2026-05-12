<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use HasFactory;

    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        return $this;
    }
    public function addonCategory()
    {
        return $this->belongsTo(AddonCategory::class);
    }
}
