<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventoryItemController extends Controller
{
    public function index()
    {
        $items = InventoryItem::orderBy('name')->get();

        return view('inventory.items.index', compact('items'));
    }

    public function create()
    {
        return view('inventory.items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:255',
        ]);

        InventoryItem::create($validated);

        return redirect()->route('inventory.items.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function show(InventoryItem $item)
    {
        $currentStock = $item->getCurrentStock();

        return view('inventory.items.show', compact('item', 'currentStock'));
    }

    public function edit(InventoryItem $item)
    {
        return view('inventory.items.edit', compact('item'));
    }

    public function update(Request $request, InventoryItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:255',
        ]);

        $item->update($validated);

        return redirect()->route('inventory.items.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $item)
    {
        $item->delete();

        return redirect()->route('inventory.items.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    public function dailyReport(Request $request, InventoryItem $item)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $report = $item->getDailyReport($startDate, $endDate);

        return view('inventory.reports.daily', compact('item', 'report', 'startDate', 'endDate'));
    }

    public function monthlyReport(Request $request, InventoryItem $item)
    {
        $year = $request->input('year', Carbon::now()->year);

        $report = $item->getMonthlyReport($year);

        return view('inventory.reports.monthly', compact('item', 'report', 'year'));
    }
}
