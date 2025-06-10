<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\ThermalPrinterService;

class PrintController extends Controller
{
    private $printerService;

    public function __construct()
    {
        // Configure your printer IP here
        $this->printerService = new ThermalPrinterService('192.168.1.100', 9100);
    }

    public function printReceipt(Request $request)
    {
        // $orderData = [
        //     'restaurant_name' => 'My Restaurant',
        //     'address' => '123 Main St, City',
        //     'phone' => '555-1234',
        //     'order_id' => $request->input('order_id'),
        //     'table' => $request->input('table'),
        //     'customer' => $request->input('customer'),
        //     'items' => $request->input('items'),
        //     'subtotal' => $request->input('subtotal'),
        //     'tax' => $request->input('tax'),
        //     'discount' => $request->input('discount', 0),
        //     'payment_method' => $request->input('payment_method')
        // ];

        $order = $request->order;


        $result = $this->printerService->printReceipt($order);

        return response()->json($result);
    }

    public function printKitchenOrder(Request $request)
    {
        $orderData = [
            'order_id' => $request->input('order_id'),
            'table' => $request->input('table'),
            'items' => $request->input('items')
        ];

        $result = $this->printerService->printKitchenOrder($orderData);

        return response()->json($result);
    }
}
