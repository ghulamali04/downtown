<?php

namespace App\Http\Controllers;

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

        $result = $this->printerService->printReceipt($request->order);

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
