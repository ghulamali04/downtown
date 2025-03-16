<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'unit'
    ];
    public function trackings(): HasMany
    {
        return $this->hasMany(InventoryTracking::class);
    }

    public function getCurrentStock()
    {
        $purchased = $this->trackings()->where('type', 'purchase')->sum('amount');
        $used = $this->trackings()->whereIn('type', ['used', 'wasted', 'returned'])->sum('amount');

        return $purchased - $used;
    }

    public function getDailyReport($startDate, $endDate = null)
    {
        if (is_null($endDate)) {
            $endDate = Carbon::now()->format('Y-m-d');
        }

        return $this->trackings()
            ->selectRaw('date,
                SUM(CASE WHEN type = "purchase" THEN amount ELSE 0 END) as purchased,
                SUM(CASE WHEN type = "used" THEN amount ELSE 0 END) as used,
                SUM(CASE WHEN type = "wasted" THEN amount ELSE 0 END) as wasted,
                SUM(CASE WHEN type = "returned" THEN amount ELSE 0 END) as returned')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getMonthlyReport($year = null)
    {
        if (is_null($year)) {
            $year = Carbon::now()->year;
        }

        return $this->trackings()
            ->selectRaw('MONTH(date) as month,
                SUM(CASE WHEN type = "purchase" THEN amount ELSE 0 END) as purchased,
                SUM(CASE WHEN type = "used" THEN amount ELSE 0 END) as used,
                SUM(CASE WHEN type = "wasted" THEN amount ELSE 0 END) as wasted,
                SUM(CASE WHEN type = "returned" THEN amount ELSE 0 END) as returned')
            ->whereYear('date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
