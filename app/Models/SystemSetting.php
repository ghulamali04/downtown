<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    /** @use HasFactory<\Database\Factories\SystemSettingFactory> */
    use HasFactory;
    protected $fillable = [
        'type',
        'payload'
    ];

    public function scopeIpconfig($query)
    {
        return $query->where('type', 'ipconfig');
    }

    public function scopeServiceCharges($query)
    {
        return $query->where('type', 'service_charges');
    }

    public function scopePrintMode($query)
    {
        return $query->where('type', 'print_mode');
    }
}
