<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTracking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InventoryTrackingController extends Controller
{

    public function index(Request $request)
    {
        $query = InventoryTracking::with('inventoryItem', 'user');

        // Filter by item if provided
        if ($request->has('item_id')) {
            $query->where('inventory_item_id', $request->item_id);
        }

        // Filter by type if provided
        if ($request->has('type') && in_array($request->type, ['purchase', 'used', 'wasted', 'returned'])) {
            $query->where('type', $request->type);
        }

        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $trackings = $query->orderBy('date', 'desc')->paginate(15);
        $items = InventoryItem::orderBy('name')->get();

        return view('inventory.trackings.index', compact('trackings', 'items'));
    }

    public function create()
    {
        $items = InventoryItem::orderBy('name')->get();

        return view('inventory.trackings.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'type' => 'required|in:purchase,used,wasted,returned',
            'amount' => 'required|numeric|min:0',
        ]);

        // Add the logged in user ID
        $validated['user_id'] = Auth::user()->id;

        InventoryTracking::create($validated);

        return redirect()->route('inventory.trackings.index')
            ->with('success', 'Inventory tracking created successfully.');
    }

    public function show(InventoryTracking $tracking)
    {
        return view('inventory.trackings.show', compact('tracking'));
    }

    public function edit(InventoryTracking $tracking)
    {
        $items = InventoryItem::orderBy('name')->get();

        return view('inventory.trackings.edit', compact('tracking', 'items'));
    }

    public function update(Request $request, InventoryTracking $tracking)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'type' => 'required|in:purchase,used,wasted,returned',
            'amount' => 'required|numeric|min:0',
        ]);

        $tracking->update($validated);

        return redirect()->route('inventory.trackings.index')
            ->with('success', 'Inventory tracking updated successfully.');
    }

    public function destroy(InventoryTracking $tracking)
    {
        $tracking->delete();

        return redirect()->route('inventory.trackings.index')
            ->with('success', 'Inventory tracking deleted successfully.');
    }

    public function dailyReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(7)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $report = InventoryTracking::getDailyReport($startDate, $endDate);
        $items = InventoryItem::orderBy('name')->get();

        return view('inventory.reports.all-daily', compact('report', 'items', 'startDate', 'endDate'));
    }

    public function monthlyReport(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $report = InventoryTracking::getMonthlyReport($year);
        $items = InventoryItem::orderBy('name')->get();

        return view('inventory.reports.all-monthly', compact('report', 'items', 'year'));
    }
}
