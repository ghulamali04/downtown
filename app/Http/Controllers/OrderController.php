<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function get_latest_pending_orders(DataTables $dataTables)
    {
        $items = Order::with(['customer', 'user'])
        ->where('status', 'pending')
        ->orderBy('id', 'desc');
        return $dataTables->eloquent($items)
            ->addColumn('timestamp', function ($item) {
                return date("d/m/Y H:iA", strtotime($item->created_at));
            })
            ->addColumn('action', function ($item) {
                return $item->id;
            })
            ->toJson();
    }
    public function get_data(DataTables $dataTables, Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $type = $request->get('type');
        $status = $request->get('status');

        $items = Order::with(['customer', 'user'])
        ->where(function ($qry) use ($start_date, $end_date, $type, $status) {
            if(!empty($start_date)) {
                $qry->where('created_at', '>=', date("Y-m-d", strtotime($start_date)));
            }
            if(!empty($end_date)) {
                $qry->where('created_at', '<=', date("Y-m-d", strtotime($end_date)));
            }
            if(!empty($type)) {
                $qry->whre('type', $type);
            }
            if(!empty($status)) {
                $qry->where('status', $status);
            }
        })
        ->orderBy('id', 'desc');
        return $dataTables->eloquent($items)
            ->addColumn('timestamp', function ($item) {
                return date("d/m/Y H:iA", strtotime($item->created_at));
            })
            ->addColumn('action', function ($item) {
                return $item->id;
            })
            ->toJson();
    }
    public function index()
    {
        return view("orders.index");
    }
    public function create()
    {
        $customers = Customer::get();
        $menuItems = MenuItem::get();
        return view("orders.create", compact("customers", "menuItems"));
    }
    public function edit($order)
    {
        $order = Order::with(['items', 'customer'])->findOrFail($order);
        $customers = Customer::get();
        $menuItems = MenuItem::get();
        return view("orders.edit", compact("customers", "menuItems", "order"));
    }
    public function store(Request $request)
    {
        Validator::make($request->except('_token'), [
            'type' => 'required',
            'menutItems.*' => 'required',
            'menuItems.*.item' => 'required'
        ])->validate();
        $order = Order::create([
            'customer_id' => $request->input('customer_id'),
            'user_id' => Auth::user()->id,
            'type' => $request->input('type'),
            'table_number' => $request->input('table_number'),
            'instructions' => $request->input('instructions')
        ]);

        foreach ($request->menuItems as $item) {
            $menuItem = MenuItem::where('id', $item['item'])->first();
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'qty' => $item['qty'],
                'price' => $menuItem->current_price,
            ]);
        }

        return redirect()->route('order.receipt', ['order' => $order->id])->with('success', 'Order successfully created');
    }
    public function update(Request $request, $order)
    {
        Validator::make($request->except('_token'), [
            'type' => 'required',
            'order_items.*' => 'required',
        ])->validate();

        $order = Order::findOrFail($order);

        $order->forceFill([
            'customer_id' => $request->input('customer_id'),
            'type' => $request->input('type'),
            'table_number' => $request->input('table_number'),
            'instructions' => $request->input('instructions')
        ]);

        OrderItem::where('order_id', $order->id)->delete();

        foreach ($request->order_items as $item) {
            $menuItem = MenuItem::where('id', $item['item'])->first();
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'qty' => $item['qty'],
                'price' => $menuItem->current_price,
            ]);
        }

        return redirect()->route('order.receipt', ['order' => $order->id])->with('success', 'Order successfully updated');
    }
    public function destroy(Order $order)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(404);
        }
        $order->delete();
        return redirect()->back()->with('success', 'Order successfully deleted');
    }
    public function receipt($order)
    {
        $order = Order::with('items', 'customer', 'user')->where('id', $order)->first();
        return view("orders.receipt", compact("order"));
    }
    public function update_status(Order $order, $status)
    {
        $return = redirect();
        if($status === "completed" || $status === "cancelled") {
            $order->update([
                "status" => $status
            ]);
            if($status === "completed") {
                $order->update([
                    "payment_status" => "paid"
                ]);
                $return->route('order.receipt', ['order' => $order->id]);
            } else {
                $return->back();
            }
        }
        return $return->with('success', 'Order status successfully updated');
    }
}
