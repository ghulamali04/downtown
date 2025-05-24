<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItemVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'menu_item_id',
        'current_price'
    ];

    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id', 'id');
    }
}
