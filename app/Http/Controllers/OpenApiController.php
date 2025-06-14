<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OpenApiController extends Controller
{
    public function getMenuCategories(Request $request)
    {
        $categories = MenuCategory::get();
        return response()->json($categories);
    }

    public function getMenuItems(Request $request)
    {
        $items = MenuItem::where('menu_category_id', $request->menu_category_id)->get();
        return response()->json($items);
    }

    // public function getMenuItemVariants(Request $request)
    // {
    //     $variants = MenuItemVariant::with(['item'])
    //     ->whereHas('item', function ($join) use ($request) {
    //         $join->where('menu_category_id', $request->menu_category_id);
    //     })
    //     ->get();
    //     return response()->json($variants);
    // }

    public function getMenuItemVariants(Request $request)
{
    // Validate the request
    $request->validate([
        'menu_category_id' => 'required|integer|exists:menu_categories,id'
    ]);

    $categoryId = $request->menu_category_id;
    $cacheKey = "menu_variants_category_{$categoryId}";

    // Try to get from cache first (24 hours TTL)
    $variants = Cache::remember($cacheKey, 86400, function () use ($categoryId) {
        return MenuItemVariant::select([
                'id',
                'menu_item_id',
                'name',
                'current_price',
            ])
            ->with(['item:id,name,menu_category_id,current_price'])
            ->whereHas('item', function ($query) use ($categoryId) {
                $query->where('menu_category_id', $categoryId);
            })
            ->orderBy('menu_item_id')
            ->orderBy('name')
            ->get();
    });

    return response()->json($variants);
}

// Add this method to clear cache when menu items are updated
public function clearMenuVariantsCache($categoryId = null)
{
    if ($categoryId) {
        Cache::forget("menu_variants_category_{$categoryId}");
    } else {
        // Clear all menu variant caches (use pattern matching if available)
        $keys = Cache::getRedis()->keys('*menu_variants_category_*');
        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }
    }
}

}
