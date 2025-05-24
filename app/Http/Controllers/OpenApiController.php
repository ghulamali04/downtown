<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use Illuminate\Http\Request;

class OpenApiController extends Controller
{
    public function getMenuCategories(Request $request)
    {
        $categories = MenuCategory::all();
        return response()->json($categories);
    }

    public function getMenuItems(Request $request)
    {
        $items = MenuItem::where('menu_category_id', $request->menu_category_id)->get();
        return response()->json($items);
    }

    public function getMenuItemVariants(Request $request)
    {
        $variants = MenuItemVariant::where('menu_item_id', $request->menu_item_id)->get();
        return response()->json($variants);
    }
}
