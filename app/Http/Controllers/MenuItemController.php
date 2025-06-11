<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class MenuItemController extends Controller
{
    public function get_data(DataTables $dataTables)
    {
        $items = MenuItem::with('category');
        return $dataTables->eloquent($items)
        ->addColumn('timestamp', function ($item) {
            return date("d/m/Y H:iA", strtotime($item->created_at));
        })
            ->addColumn('action', function ($item) {
                return $item->id;
            })
            ->toJson();
    }
    private function menuItemValidation($input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            //'price' => ['required', 'numeric'],
            'menu_category_id' => ['required', 'exists:menu_categories,id']
        ])->validate();
    }
    public function index()
    {
        return view("menu.index");
    }
    public function create()
    {
        $categories = MenuCategory::all();
        return view("menu.create", compact('categories'));
    }
    public function edit($item)
    {
        $item = MenuItem::findOrFail($item);
        $categories = MenuCategory::all();
        return view("menu.edit", compact('item', 'categories'));
    }
    public function store(Request $request)
    {
        $this->menuItemValidation($request->except('_token'));
        MenuItem::create([
            "name" => $request->input('name'),
            "menu_category_id" => $request->input('menu_category_id'),
            "current_price" => $request->input('price')
        ]);
        return redirect()->back()->with('success', 'Menu Item successfully created');
    }
    public function update(Request $request, $menuItem)
    {
        $this->menuItemValidation($request->except('_token'));
        $menuItem = MenuItem::findOrFail($menuItem);
        $menuItem->update([
            "name" => $request->input('name'),
            "menu_category_id" => $request->input('menu_category_id'),
            "current_price" => $request->input('price')
        ]);
        return redirect()->back()->with('success', 'Menu Item successfully updated');
    }
    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();
        return new JsonResponse([
            "status" => "success",
            "message" => "Menu Item successfully deleted"
        ], 200);
    }
}
