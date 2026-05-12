<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ChristianKuri\LaravelFavorite\Traits\Favoriteable;



class Item extends Model
{
    use HasFactory,Favoriteable;
    protected $guarded = [];

    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        return $this;
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class);
    }
    public function addonCategories()
    {
        return $this->belongsToMany(AddonCategory::class);
    }


    public function itemGroups()
    {
        return $this->belongsToMany(ItemGroup::class);
    }
}
