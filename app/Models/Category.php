<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'restaurant_category_id',
        'image',
        'description',
        'is_active',
        'created_at',
        'updated_at'
    ];

    public function item_categories()
    {
        return $this->hasMany(ItemCategory::class);
    }
}
