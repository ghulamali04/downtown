<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'variant',
        'current_price',
        'menu_category_id'
    ];

    public function variants()
    {
        return $this->hasMany(MenuItemVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id', 'id');
    }
}
