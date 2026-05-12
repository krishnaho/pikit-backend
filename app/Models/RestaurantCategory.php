<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantCategory extends Model
{
    use HasFactory;

    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        return $this;
    }
    public function itemCategories()
    {
        return $this->hasMany(ItemCategory::class);
    }

    public function itemGroups()
    {
        return $this->hasMany(ItemGroup::class);
    }
}
