<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\MenuCategory;

class MenuCategoryController extends Controller
{
    public function get_data(DataTables $dataTables)
    {
        $items = MenuCategory::query();
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
            'name' => ['required', 'string', 'max:255']
        ])->validate();
    }
    public function index()
    {
        return view("menu.category.index");
    }
    public function create()
    {
        return view("menu.category.create");
    }
    public function edit($item)
    {
        $item = MenuCategory::findOrFail($item);
        return view("menu.category.edit", compact('item'));
    }
    public function store(Request $request)
    {
        $this->menuItemValidation($request->except('_token'));
        MenuCategory::create([
            "name" => $request->input('name'),
        ]);
        return redirect()->back()->with('success', 'Menu Category successfully created');
    }
    public function update(Request $request, $menuItem)
    {
        $this->menuItemValidation($request->except('_token'));
        $menuItem = MenuCategory::findOrFail($menuItem);
        $menuItem->update([
            "name" => $request->input('name'),
        ]);
        return redirect()->back()->with('success', 'Menu Category successfully updated');
    }
    public function destroy($menuItem)
    {
        $menuItem = MenuCategory::findOrFail($menuItem);
        $menuItem->delete();
        return new JsonResponse([
            "status" => "success",
            "message" => "Menu Category successfully deleted"
        ], 200);
    }
}
