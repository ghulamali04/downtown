<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTracking;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InventoryTrackingController extends Controller
{
    public function get_data(DataTables $dataTables, Request $request)
    {
        $query = InventoryTracking::with('inventoryItem', 'user');

        if (!empty($request->item_id)) {
            $query->where('inventory_item_id', $request->item_id);
        }

        if ($request->has('type') && in_array($request->type, ['purchase', 'used', 'wasted', 'returned'])) {
            $query->where('type', $request->type);
        }

        if(!empty($request->date)) {
            $query->where('date', '<=', date("Y-m-d", strtotime($request->date)));
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $trackings = $query->orderBy('date', 'desc');
        return $dataTables->eloquent($trackings)
            ->addColumn('timestamp', function ($tracking) {
                return date("d/m/Y H:iA", strtotime($tracking->created_at));
            })
            ->addColumn('action', function ($tracking) {
                return $tracking->id;
            })
            ->toJson();
    }
    public function index()
    {
        return view('inventory.trackings.index');
    }

    public function create()
    {
        $items = InventoryItem::orderBy('name')->get();

        return view('inventory.trackings.create', compact('items'));
    }

    public function update(Request $request, InventoryTracking $tracking)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'type' => 'required|in:purchase,used,wasted,returned',
            'category' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $originalType = $tracking->type;
        $originalAmount = $tracking->amount;
        $originalItemId = $tracking->inventory_item_id;

        $balanceAffectingChanges =
            $originalType !== $validated['type'] ||
            $originalAmount !== $validated['amount'] ||
            $originalItemId !== $validated['inventory_item_id'];

        if ($balanceAffectingChanges) {
            $balanceImpactDelta = $this->calculateBalanceImpactDelta(
                $originalType,
                $originalAmount,
                $validated['type'],
                $validated['amount']
            );

            if ($balanceImpactDelta < 0) {
                $wouldCauseNegative = $this->wouldCauseNegativeBalance(
                    $validated['inventory_item_id'],
                    $balanceImpactDelta,
                    $tracking->date,
                    $tracking->id
                );

                if ($wouldCauseNegative) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['amount' => 'This change would cause negative inventory in subsequent transactions.']);
                }
            }
        }

        $tracking->update($validated);

        return redirect()->route('tracking.index')
            ->with('success', 'Inventory tracking updated successfully.');
    }

    private function calculateBalanceImpactDelta($oldType, $oldAmount, $newType, $newAmount)
    {
        $oldImpact = ($oldType === 'purchase') ? $oldAmount : -$oldAmount;

        $newImpact = ($newType === 'purchase') ? $newAmount : -$newAmount;

        return $newImpact - $oldImpact;
    }

    private function wouldCauseNegativeBalance($inventoryItemId, $balanceChange, $date, $excludeTrackingId)
    {
        $futureTransactions = InventoryTracking::where('inventory_item_id', $inventoryItemId)
            ->where('id', '!=', $excludeTrackingId)
            ->where('date', '>=', $date)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $runningBalance = $this->getBalanceAtDate($inventoryItemId, $date, $excludeTrackingId);

        $runningBalance += $balanceChange;

        if ($runningBalance < 0) {
            return true;
        }

        foreach ($futureTransactions as $transaction) {
            $transactionImpact = ($transaction->type === 'purchase') ? $transaction->amount : -$transaction->amount;
            $runningBalance += $transactionImpact;

            if ($runningBalance < 0) {
                return true;
            }
        }

        return false;
    }

    public function getCurrentBalance(Request $request, $date = null)
    {
        $date = $date ?? date("Y-m-d");

        $transactions = InventoryTracking::where('date', '<=', date("Y-m-d", strtotime($date)))
        ->where(function ($qry) use ($request) {
            if (!empty($request->item_id)) {
                $qry->where('inventory_item_id', $request->item_id);
            }
        })
        ->get();

        $balance = 0;
        foreach ($transactions as $transaction) {
            if($transaction->type === 'purchase') {
                $balance += $transaction->amount;
            } else {
                $balance -= $transaction->amount;
            }
        }
        return $balance;
    }
    private function getBalanceAtDate($inventoryItemId, $date, $excludeTrackingId = null)
    {
        $query = InventoryTracking::where('inventory_item_id', $inventoryItemId)
            ->where('date', '<=', $date);

        if ($excludeTrackingId) {
            $query->where('id', '!=', $excludeTrackingId);
        }

        $transactions = $query->get();

        $balance = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->type === 'purchase') {
                $balance += $transaction->amount;
            } else {
                $balance -= $transaction->amount;
            }
        }

        return $balance;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'type' => 'required|in:purchase,used,wasted,returned',
            'category' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::user()->id;

        if (in_array($validated['type'], ['used', 'wasted', 'returned'])) {
            $currentBalance = $this->getBalanceAtDate(
                $validated['inventory_item_id'],
                $validated['date']
            );

            if ($currentBalance < $validated['amount']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['amount' => 'Insufficient inventory. Current balance at this date: ' . $currentBalance]);
            }
        }

        InventoryTracking::create($validated);

        return redirect()->route('tracking.index')
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

    public function destroy(InventoryTracking $tracking)
    {
        $tracking->delete();

        return new JsonResponse([
            "status" => "success",
            "message" => "Inventory tracking successfully deleted"
        ], 200);
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
