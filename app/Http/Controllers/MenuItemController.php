<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class MenuItemController extends Controller
{
    public function get_data(DataTables $dataTables)
    {
        $items = MenuItem::query();
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
            'price' => ['required', 'numeric', 'min:1']
        ])->validate();
    }
    public function index()
    {
        return view("menu.index");
    }
    public function create()
    {
        return view("menu.create");
    }
    public function edit($item)
    {
        $item = MenuItem::findOrFail($item);
        return view("menu.edit", compact('item'));
    }
    public function store(Request $request)
    {
        $this->menuItemValidation($request->except('_token'));
        MenuItem::create([
            "name" => $request->input('name'),
            "variant" => $request->input('variant'),
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
            "variant" => $request->input('variant'),
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
