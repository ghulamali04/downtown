<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class MenuItemVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'menu_item_id',
        'current_price',
        'item_type'
    ];

    protected static function boot()
{
    parent::boot();

    static::saved(function ($variant) {
        if ($variant->item) {
            Cache::forget("menu_variants_category_{$variant->item->menu_category_id}");
        }
    });

    static::deleted(function ($variant) {
        if ($variant->item) {
            Cache::forget("menu_variants_category_{$variant->item->menu_category_id}");
        }
    });
}

    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id', 'id');
    }
}
