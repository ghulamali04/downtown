<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Models\Customer;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Process;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\Printer;

class OrderController extends Controller
{

    public function getCurrentBalance($start_date = null, $end_date = null)
    {
        $totalPrice = Order::where('status', 'completed')
            ->where(function ($qry) use ($start_date, $end_date) {
                if (!empty($start_date)) {
                    $qry->where('created_at', '>=', date("Y-m-d", strtotime($start_date)) . " 00:00:00");
                }
                if (!empty($end_date)) {
                    $qry->where('created_at', '<=', date("Y-m-d", strtotime($end_date)) . " 23:59:59");
                }
            })
            ->join('order_items', 'orders.id', '=', 'order_items.order_id') // Join the related items
            ->sum('order_items.price');
        return $totalPrice ?? 0;
    }
    public function get_latest_pending_orders(DataTables $dataTables)
    {
        $items = Order::with(['customer', 'user', 'items'])
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
    public function exportOrders(Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $type = $request->get('type');
        $status = $request->get('status');

        $data = Order::with(['customer', 'user'])
            ->where(function ($qry) use ($start_date, $end_date, $type, $status) {
                if (!empty($start_date)) {
                    $qry->where('created_at', '>=', date("Y-m-d", strtotime($start_date)));
                }
                if (!empty($end_date)) {
                    $qry->where('created_at', '<=', date("Y-m-d", strtotime($end_date)));
                }
                if (!empty($type)) {
                    $qry->where('type', $type);
                }
                if (!empty($status)) {
                    $qry->where('status', $status);
                }
            })
            ->orderBy('id', 'desc')->get();
            $applied_filters = '';
            if(!empty($start_date)) {
                $applied_filters .= "Start Date:".date("Y-m-d", strtotime($start_date));
            }
            if(!empty($end_date)) {
                $applied_filters .= "End Date:". date("Y-m-d", strtotime($end_date));
            }
            if(!empty($type)) {
                $applied_filters .= "Type:".ucfirst($type);
            }
            if(!empty($status)) {
                $applied_filters .= "Status:".ucfirst($status);
            }
        return Excel::download(new OrderExport($data, $applied_filters), 'orders.xlsx');
    }
    public function get_data(DataTables $dataTables, Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $type = $request->get('type');
        $status = $request->get('status');

        $items = Order::with(['customer', 'user'])
            ->where(function ($qry) use ($start_date, $end_date, $type, $status) {
                if (!empty($start_date)) {
                    $qry->where('created_at', '>=', date("Y-m-d", strtotime($start_date)));
                }
                if (!empty($end_date)) {
                    $qry->where('created_at', '<=', date("Y-m-d", strtotime($end_date)));
                }
                if (!empty($type)) {
                    $qry->whre('type', $type);
                }
                if (!empty($status)) {
                    $qry->where('status', $status);
                }
            })
            ->orderBy('id', 'desc');
        return $dataTables->eloquent($items)
        ->addColumn('total_price', function ($item) {
            return $item->total_price;
        })
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
        $menuCateogryItems = MenuCategory::get();
        return view("orders.create", compact("customers", "menuCateogryItems"));
    }
    public function edit($order)
    {
        $order = Order::with(['items', 'customer', 'user', 'items.menu_item', 'items.menu_category', 'items.menu_item_variant'])->findOrFail($order);
        $customers = Customer::get();
        $menuCateogryItems = MenuCategory::get();
        return view("orders.edit", compact("customers", "menuCateogryItems", "order"));
    }
    public function store(Request $request)
    {
        Validator::make($request->except('_token'), [
            'type' => 'required',
            'menutItems.*' => 'required',
            'menuItems.*.item' => 'required'
        ])->validate();
        $order = Order::create([
            'customer_id' => $request->input('customer'),
            'user_id' => Auth::user()->id,
            'type' => $request->input('type'),
            'table_number' => $request->input('table_number'),
            'instructions' => $request->input('instructions')
        ]);

        foreach ($request->menuItems as $item) {
            $menuCategory = MenuCategory::find($item['category']);
            $menuItem = MenuItem::where('id', $item['item'])->first();
            $menuItemVariant = MenuItemVariant::where('id', $item['variant'])->first();
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $menuItem->id,
                'menu_category_id' => $menuCategory->id,
                'menu_item_variant_id' => @$menuItemVariant->id,
                'name' => $menuItem->name . ' ' . @$menuItemVariant->name,
                'qty' => $item['qty'],
                'price' => @$menuItemVariant->current_price > 0 ? $menuItemVariant->current_price : $menuItem->current_price,
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
            'customer_id' => $request->input('customer'),
            'type' => $request->input('type'),
            'table_number' => $request->input('table_number'),
            'instructions' => $request->input('instructions')
        ]);

        OrderItem::where('order_id', $order->id)->delete();

        foreach ($request->menuItems as $item) {
            $menuCategory = MenuCategory::find($item['category']);
            $menuItem = MenuItem::where('id', $item['item'])->first();
            $menuItemVariant = MenuItemVariant::where('id', @$item['variant'])->first();
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $menuItem->id,
                'menu_category_id' => $menuCategory->id,
                'menu_item_variant_id' => @$menuItemVariant->id,
                'name' => $menuItem->name . ' ' . @$menuItemVariant->name,
                'qty' => $item['qty'],
                'price' => @$menuItemVariant->current_price > 0 ? $menuItemVariant->current_price : $menuItem->current_price,
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
        $printers = $this->getAvailablePrinters();
        $order = Order::with('items', 'customer', 'user')->where('id', $order)->first();
        return view("orders.receipt", compact("order", "printers"));
    }
    private function getAvailablePrinters()
    {
        $result = Process::run('lpstat -p | grep printer');

        if ($result->successful()) {
            $output = $result->output();
            $printers = [];

            $lines = explode("\n", $output);
            foreach ($lines as $line) {
                if (empty($line)) continue;

                if (preg_match('/printer (.*?) is/', $line, $matches)) {
                    $printers[] = $matches[1];
                }
            }

            return $printers;
        }

        return [];
    }

    public function update_status(Order $order, $status)
    {
        if ($status === "completed" || $status === "cancelled") {
            $order->update([
                "status" => $status
            ]);
            if ($status === "completed") {
                $order->update([
                    "payment_status" => "paid"
                ]);
                return redirect()->route('order.receipt', ['order' => $order->id]);
            } else {
                return redirect()->back();
            }
        }
        return redirect()->back()->with('success', 'Order status successfully updated');
    }

    public function printTestPage(Request $request, $order)
    {
        $request->validate([
            'printer' => 'required|string',
        ]);

        $order = Order::with('items', 'customer', 'user')->where('id', $order)->first();

        $printerName = $request->input('printer');
        $printer = null;

        try {

            $connector = new CupsPrintConnector($printerName);
            $printer = new Printer($connector);

            $printer->initialize();
            $printer->text(" \n");
            $printer->feed(2);
            $printer->cut(Printer::CUT_PARTIAL);

            $printer->initialize();
            $printer->feed(5);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("DOWNTOWN\n");
            $printer->setEmphasis(false);
            $printer->feed(1);
            $printer->text("Jail Road, Mall of Bahawalnagar\n");
            $printer->feed(1);
            $printer->text("Tel: (063) 2280-988\n");
            $printer->feed(1);
            $printer->text("Date: " . now()->format('F j, Y g:i A') . " \n");
            $printer->feed(1);
            $printer->text("-------------------\n");

            $printer->feed(1);

            $printer->setJustification(Printer::JUSTIFY_LEFT);

            $total = 0;
            foreach ($order->items as $item) {
                $itemTotal = $item->price * $item->qty;

                $printer->text($item->name . "\n");

                $lineWidth = 42;
                $qtyText = "QTY: " . $item->qty;
                $priceText = "PKR " . number_format($itemTotal, 2);

                $spacesNeeded = $lineWidth - strlen($qtyText) - strlen($priceText);
                $spaces = str_repeat(" ", max(1, $spacesNeeded));

                $printer->text($qtyText . $spaces . $priceText . "\n");
                $printer->feed();

                $total += $itemTotal;
            }
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("------------------\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);

            $lineWidth = 42;
            $totalText = "TOTAL:";
            $totalValue = "PKR " . number_format($total, 2);
            $spacesNeeded = $lineWidth - strlen($totalText) - strlen($totalValue);
            $spaces = str_repeat(" ", max(1, $spacesNeeded));

            $printer->setEmphasis(true);
            $printer->text($totalText . $spaces . $totalValue . "\n");
            $printer->setEmphasis(false);

            $printer->feed(1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for your purchase!\n");
            $printer->text("Please come again\n");

            $printer->feed(3);
            $printer->cut();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to print test page',
                'error' => $e->getMessage(),
            ], 500);
        } finally {
            if (isset($printer)) {
                $printer->close();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Test page sent to printer successfully ',
        ]);
    }
}
