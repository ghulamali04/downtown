<?php

use App\Models\Order;
use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;


Route::post('/print/receipt', [PrintController::class, 'printReceipt']);

// Get new print orders
Route::get('/print-orders', function () {
    return response()->json([
        'orders' => Order::where('printed', 0)->get()
    ]);
});

// Mark order as printed
Route::post('/print-orders/{order}/printed', function (Order $order) {
    $order->printed = 1;
    $order->save();
    return response()->json(['success' => true]);
});
