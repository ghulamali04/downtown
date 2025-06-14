<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'name',
        'price',
        'menu_item_id',
        'menu_category_id',
        'menu_item_variant_id',
        'qty',
        'to_be_processed'
    ];

    public function menu_item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id', 'id');
    }
    public function menu_category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id', 'id');
    }
    public function menu_item_variant()
    {
        return $this->belongsTo(MenuItemVariant::class, 'menu_item_variant_id', 'id');
    }
}
