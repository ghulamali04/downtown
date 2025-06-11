<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;

class MenuItemVariantController extends Controller
{
    public function get_data(DataTables $dataTables)
    {
        $items = MenuItemVariant::with(['item', 'item.category']);
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
            'name' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:1'],
            'menu_item_id' => ['required', 'exists:menu_items,id']
        ])->validate();
    }
    public function index()
    {
        return view("menu.variant.index");
    }
    public function create()
    {
        $items = MenuItem::all();
        return view("menu.variant.create", compact('items'));
    }
    public function edit($item)
    {
        $item = MenuItemVariant::findOrFail($item);
        $items = MenuItem::all();
        return view("menu.variant.edit", compact('item', 'items'));
    }
    public function store(Request $request)
    {
        $this->menuItemValidation($request->except('_token'));
        MenuItemVariant::create([
            "name" => $request->input('name'),
            "menu_item_id" => $request->input('menu_item_id'),
            "current_price" => $request->input('price')
        ]);
        return redirect()->back()->with('success', 'Menu Item Variant successfully created');
    }
    public function update(Request $request, $menuItem)
    {
        $this->menuItemValidation($request->except('_token'));
        $menuItem = MenuItemVariant::findOrFail($menuItem);
        $menuItem->update([
            "name" => $request->input('name'),
            "menu_item_id" => $request->input('menu_item_id'),
            "current_price" => $request->input('price')
        ]);
        return redirect()->back()->with('success', 'Menu Item Variant successfully updated');
    }
    public function destroy($menuItem)
    {
        $menuItem = MenuItemVariant::findOrFail($menuItem);
        $menuItem->delete();
        return new JsonResponse([
            "status" => "success",
            "message" => "Menu Item successfully deleted"
        ], 200);
    }
}
