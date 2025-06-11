<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'date',
        'description',
        'category',
        'type',
        'amount',
        'user_id',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getDailyReport($startDate, $endDate = null)
    {
        if (is_null($endDate)) {
            $endDate = Carbon::now()->format('Y-m-d');
        }

        return self::selectRaw('date, inventory_item_id,
                SUM(CASE WHEN type = "purchase" THEN amount ELSE 0 END) as purchased,
                SUM(CASE WHEN type = "used" THEN amount ELSE 0 END) as used,
                SUM(CASE WHEN type = "wasted" THEN amount ELSE 0 END) as wasted,
                SUM(CASE WHEN type = "returned" THEN amount ELSE 0 END) as returned')
            ->with('inventoryItem')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date', 'inventory_item_id')
            ->orderBy('date')
            ->get();
    }

    public static function getMonthlyReport($year = null)
    {
        if (is_null($year)) {
            $year = Carbon::now()->year;
        }

        return self::selectRaw('MONTH(date) as month, inventory_item_id,
                SUM(CASE WHEN type = "purchase" THEN amount ELSE 0 END) as purchased,
                SUM(CASE WHEN type = "used" THEN amount ELSE 0 END) as used,
                SUM(CASE WHEN type = "wasted" THEN amount ELSE 0 END) as wasted,
                SUM(CASE WHEN type = "returned" THEN amount ELSE 0 END) as returned')
            ->with('inventoryItem')
            ->whereYear('date', $year)
            ->groupBy('month', 'inventory_item_id')
            ->orderBy('month')
            ->get();
    }
}
