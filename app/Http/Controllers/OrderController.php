<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Models\Customer;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\EscposImage;

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
            'table_number' => 'required_if:type,dining',
            'customer' => 'required_if:type,delivery',
            'menutItems.*' => 'required'
        ])->validate();
        $order = Order::create([
            'customer_id' => $request->input('customer'),
            'user_id' => Auth::user()->id,
            'type' => $request->input('type'),
            'table_number' => $request->input('table_number'),
            'is_bar' => @$request->input('is_bar') ? 1 : 0,
            'instructions' => $request->input('instructions')
        ]);

        $total_price = 0;
        foreach ($request->menuItems as $item) {
            $menuCategory = MenuCategory::find($item['category']);
            //$menuItem = MenuItem::where('id', $item['item'])->first();
            $menuItemVariant = MenuItemVariant::with('item')->where('id', $item['variant'])->first();
            $price = @$menuItemVariant->current_price > 0 ? $menuItemVariant->current_price : @$menuItemVariant->item->current_price;
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => @$menuItemVariant->item->id,
                'menu_category_id' => $menuCategory->id,
                'menu_item_variant_id' => @$menuItemVariant->id,
                'name' => @$menuItemVariant->item->name . ' ' . @$menuItemVariant->name,
                'qty' => $item['qty'],
                'to_be_processed' => $item['qty'],
                'price' => $price,
            ]);
            $total_price += $item['qty'] * $price;
        }
        $serviceCharges = optional(SystemSetting::serviceCharges()->first())->payload ?? '0';
        if ($serviceCharges > 0) {
            $total_service_charges = $total_price * ($serviceCharges / 100);
            $order->service_charges = $total_service_charges;
            $order->save();
        }
        return redirect()->route('order.receipt', ['order' => $order->id])->with('success', 'Order successfully created');
    }
    public function update(Request $request, $order)
    {
        Validator::make($request->except('_token'), [
            'type' => 'required',
            'table_number' => 'required_if:type,dining',
            'customer' => 'required_if:type,delivery',
            'menutItems.*' => 'required'
        ])->validate();

        $order = Order::findOrFail($order);

        $order->forceFill([
            'customer_id' => $request->input('customer'),
            'type' => $request->input('type'),
            'table_number' => $request->input('table_number'),
            'is_bar' => @$request->input('is_bar') ? 1 : 0,
            'instructions' => $request->input('instructions')
        ]);

        //OrderItem::where('order_id', $order->id)->delete();

        $orderItems = [];
        $total_price = 0;
        foreach ($request->menuItems as $item) {
            $menuCategory = MenuCategory::find($item['category']);
            //$menuItem = MenuItem::where('id', $item['item'])->first();
            $menuItemVariant = MenuItemVariant::with('item')->where('id', $item['variant'])->first();
            $price = @$menuItemVariant->current_price > 0 ? $menuItemVariant->current_price : @$menuItemVariant->item->current_price;
            $oldOrderItem = OrderItem::where('order_id', $order->id)
            ->where('menu_item_id', @$menuItemVariant->item->id)
            ->where('menu_category_id', $menuCategory->id)
            ->where('menu_item_variant_id', @$menuItemVariant->id)
            ->first();
            if ($oldOrderItem) {
                $oldOrderItem->forceFill([
                    'order_id' => $order->id,
                    'menu_item_id' => @$menuItemVariant->item->id,
                    'menu_category_id' => $menuCategory->id,
                    'menu_item_variant_id' => @$menuItemVariant->id,
                    'name' => @$menuItemVariant->item->name . ' ' . @$menuItemVariant->name,
                    'qty' => $item['qty'],
                    'to_be_processed' => $item['qty'] > $oldOrderItem->qty ? $item['qty'] - $oldOrderItem->qty : $oldOrderItem->to_be_processed + $item['qty'] - $oldOrderItem->qty ,
                    'price' => $price,
                ])->save();
                $total_price += $price * $item['qty'];
                $orderItems[] = $oldOrderItem->id;
            } else {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => @$menuItemVariant->item->id,
                    'menu_category_id' => $menuCategory->id,
                    'menu_item_variant_id' => @$menuItemVariant->id,
                    'name' => @$menuItemVariant->item->name . ' ' . @$menuItemVariant->name,
                    'qty' => $item['qty'],
                    'to_be_processed' => $item['qty'],
                    'price' => $price,
                ]);
                $total_price += $price * $item['qty'];
                $orderItems[] = $orderItem->id;
            }
        }
        $serviceCharges = optional(SystemSetting::serviceCharges()->first())->payload ?? '0';
        if ($serviceCharges > 0) {
            $total_service_charges = $total_price * ($serviceCharges / 100);
            $order->service_charges = $total_service_charges;
            $order->save();
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
        $order = Order::with('items', 'customer', 'user')->where('id', $order)->first();

        $response = Http::post('http://127.0.0.1:8000/api/print/receipt', [
            'order' => $order
        ]);

        return response()->json([
            "success" => true,
            "message" => $response->body()
        ]);

//         $response = Http::post('http://localhost:8080/print', [
//             'type' => 'receipt',
//             'printerType' => 'food', // or 'drink', 'receipt', 'all'
//             'data' => [
//                 'restaurantName' => 'Downtown',
//                 'orderNumber' => $order->id,
//                 'customerName' => $order->customer->first_name . ' ' . $order->customer->last_name,
//                 'items' => $order->items->map(function($item) {
//                     return [
//                         'name' => $item->name,
//                         'quantity' => $item->qty,
//                         'price' => $item->price
//                     ];
//                 })->toArray()
//             ]
//         ]);

//         return response()->json([
//             "success" => true,
//             "message" => $response->body()
//         ]);

//         $request->validate([
//             'printer' => 'required|string',
//         ]);

//         $order = Order::with('items', 'customer', 'user')->where('id', $order)->first();

//         if ($request->get('type') == 'paid') {
//             $order->is_paid = 1;
//             $order->save();
//         } else {
//             $order->is_paid = 0;
//             $order->save();
//         }

//         $printerName = $request->input('printer');
//         $printer = null;

//         try {
//             shell_exec("lprm -P $printerName 2>&1");
//             $connector = new CupsPrintConnector($printerName);
//             $printer = new Printer($connector);

//             $printer->initialize();
//             $printer->text(" \n");
//             $printer->feed(2);
//             usleep(500000);
//             $printer->cut(Printer::CUT_PARTIAL);
//             usleep(200000);


//             $printer->initialize();
//             $printer->feed(2);
// // Header Section
// $printer->setJustification(Printer::JUSTIFY_CENTER);
// $printer->setEmphasis(true);
// $printer->text("DOWNTOWN\n");
// $printer->text("BAHAWALNAGAR\n");
// $printer->setEmphasis(false);
// $printer->text("Phone: 03202280987\n");
// $printer->text("03132890988\n");
// $printer->text("".$order->is_paid == 1 ? 'PAID' : 'UNPAID'."\n");
// $printer->feed(1);

// // Token and Order Info
// $printer->setJustification(Printer::JUSTIFY_LEFT);
// $lineWidth = 42;
// $orderIdText = "ORDER ID: " . $order->id;
// $orderTypeText = "Order Type: " . ucfirst($order->type);
// $spacesNeeded = $lineWidth - strlen($orderIdText) - strlen($orderTypeText);
// $spaces = str_repeat(" ", max(1, $spacesNeeded));
// $printer->text($orderIdText . $spaces);
// $printer->setEmphasis(true);
// $printer->text($orderTypeText . "\n");
// $printer->setEmphasis(false);
// $printer->text("Date: " . now()->format('d/m/Y H:i') . "\n");
// $printer->text("User: " . $order->user->first_name . ' ' . $order->user->last_name . "\n");
// $printer->feed(1);

// // Order Details Header
// $printer->setJustification(Printer::JUSTIFY_CENTER);
// $printer->text(str_repeat("-", 42) . "\n");
// $printer->text("Order Detail\n");
// $printer->text(str_repeat("-", 42) . "\n");
// $printer->setJustification(Printer::JUSTIFY_LEFT);

// // Order Items
// $total = 0;
// $printer->text(sprintf("%-20s %-5s %-8s %8s\n", "Item", "Qty", "Rate", "Total"));
// $printer->setJustification(Printer::JUSTIFY_CENTER);
// $printer->text(str_repeat("-", 42) . "\n");
// $printer->setJustification(Printer::JUSTIFY_LEFT);
// foreach ($order->items as $item) {
//     $itemTotal = $item->price * $item->qty;
//     $total += $itemTotal;

//     // Item name on one line
//     $printer->text($item->name . "\n");

//     // Quantity, Rate, and Total on the next line with right-aligned amounts
//     $printer->text(sprintf("%-20s %-5s %-8s %8s\n", "", $item->qty, number_format($item->price, 2), number_format($itemTotal, 2)));
//     $printer->feed();
// }

// // Subtotal, VAT/GST, and Grand Total
// $printer->setJustification(Printer::JUSTIFY_CENTER);
// $printer->text(str_repeat("-", 42) . "\n");
// $printer->setJustification(Printer::JUSTIFY_LEFT);
// $subTotalText = "Sub Total";
// $subTotalValue = number_format($total, 2);
// $printer->text(sprintf("%-30s %8s Rs\n", $subTotalText, $subTotalValue));

// $vatText = "VAT/GST (0% on Cash)";
// $vatValue = "0.00";
// $printer->text(sprintf("%-30s %8s Rs\n", $vatText, $vatValue));

// $grandTotalText = "GRAND TOTAL";
// $grandTotalValue = number_format($total, 2);
// $printer->setEmphasis(true);
// $printer->text(sprintf("%-30s %8s Rs\n", $grandTotalText, $grandTotalValue));
// $printer->setEmphasis(false);
// $printer->feed(1);

// // Customer Details
// $printer->setJustification(Printer::JUSTIFY_CENTER);
// $printer->text("Customer Detail\n");
// $printer->text(str_repeat("-", 42) . "\n");
// $printer->setJustification(Printer::JUSTIFY_LEFT);
// $printer->text("".$order->customer->phone_number."\n");
// $printer->text("Delivery Address: ".$order->customer->address."\n");
// $printer->text("Order-Taker: ".$order->user->first_name.' '.$order->user->last_name."\n");
// $printer->feed(1);

// // Footer
// $printer->setJustification(Printer::JUSTIFY_CENTER);
// $printer->text(str_repeat("-", 42) . "\n");
// $printer->text("Printed: " . now()->format('d/m/Y H:i') . "\n");
// $printer->text("FOR ANY COMPLAINT & SUGGESTIONS\n");
// $printer->text("PLEASE CONTACT US @ (063) 2280-988\n");
// $printer->text("Software By Bitzsol\n");
// $printer->feed(3);

// // Finalize
// $printer->cut();

            // $connector = new CupsPrintConnector($printerName);
            // $printer = new Printer($connector);

            // $printer->initialize();
            // $printer->text(" \n");
            // $printer->feed(2);
            // $printer->cut(Printer::CUT_PARTIAL);

            // $printer->initialize();
            // $printer->feed(5);
            // $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $printer->setEmphasis(true);
            // $printer->text("DOWNTOWN\n");
            // $printer->setEmphasis(false);
            // $printer->feed(1);
            // $printer->text("Jail Road, Mall of Bahawalnagar\n");
            // $printer->feed(1);
            // $printer->text("Tel: (063) 2280-988\n");
            // $printer->feed(1);
            // $printer->text("Date: " . now()->format('F j, Y g:i A') . " \n");
            // $printer->feed(1);
            // $printer->text("-------------------\n");

            // $printer->feed(1);

            // $printer->setJustification(Printer::JUSTIFY_LEFT);

            // $total = 0;
            // foreach ($order->items as $item) {
            //     $itemTotal = $item->price * $item->qty;

            //     $printer->text($item->name . "\n");

            //     $lineWidth = 42;
            //     $qtyText = "QTY: " . $item->qty;
            //     $priceText = "PKR " . number_format($itemTotal, 2);

            //     $spacesNeeded = $lineWidth - strlen($qtyText) - strlen($priceText);
            //     $spaces = str_repeat(" ", max(1, $spacesNeeded));

            //     $printer->text($qtyText . $spaces . $priceText . "\n");
            //     $printer->feed();

            //     $total += $itemTotal;
            // }
            // $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $printer->text("------------------\n");
            // $printer->setJustification(Printer::JUSTIFY_LEFT);

            // $lineWidth = 42;
            // $totalText = "TOTAL:";
            // $totalValue = "PKR " . number_format($total, 2);
            // $spacesNeeded = $lineWidth - strlen($totalText) - strlen($totalValue);
            // $spaces = str_repeat(" ", max(1, $spacesNeeded));

            // $printer->setEmphasis(true);
            // $printer->text($totalText . $spaces . $totalValue . "\n");
            // $printer->setEmphasis(false);

            // $printer->feed(1);
            // $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $printer->text("Thank you for your purchase!\n");
            // $printer->text("Please come again\n");

            // $printer->feed(3);
            // $printer->cut();
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to print test page',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     } finally {
    //         $printer->close();
    // //shell_exec("lprm -P $printerName 2>&1");
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Test page sent to printer successfully ',
    //     ]);
    }

    public function printReceipt(Request $request)
    {
        $printMode = optional(SystemSetting::printMode()->first())->payload ?? 'tunnel';
        $order = Order::with('items', 'customer', 'user')->where('id', $request->order_id)->first();
        $order->is_paid = $request->type == 'paid' ? 1 : 0;
        $order->is_final_print = $request->is_final_print == 'final' ? 1 : 0;
        $order->paid_amount = $request->paid_amount;
        $order->change = $request->change;
        $order->save();
        if ($printMode == 'tunnel') {
            $response = Http::timeout(60)
        // ->withOptions([
        //     'curl' => [
        //         CURLOPT_DNS_SERVERS => '1.1.1.1,8.8.8.8', // Use Cloudflare/Google DNS
        //     ],
        // ])
        ->post('https://dt.thedowntownrestaurant.com/open/print/receipt', [
            'order' => $order
        ]);

        if ($response->successful()) {
            OrderItem::where('order_id', $order->id)->update([
                'to_be_processed' => 0
            ]);
            return response()->json([
                "success" => true
            ]);
        }
        } else {
            Cache::put("print_order_{$order->id}", $order, now()->addMinutes(1));
            $queued = Cache::get('print_queue', []);
            $queued[] = $order->id;
            Cache::put('print_queue', $queued);
            return response()->json([
                "success" => true
            ]);
        }

        return response()->json([
            "success" => false
        ]);
    }
}
