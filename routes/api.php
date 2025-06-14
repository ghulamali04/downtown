<?php

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/print-orders', function () {
    $queued = Cache::get('print_queue', []);

    if (empty($queued)) {
        return response()->json(['status' => 'no_data']);
    }

    $orderId = array_shift($queued);
    $orderData = Cache::pull("print_order_{$orderId}");

    Cache::put('print_queue', $queued);

    OrderItem::where('order_id', $orderData["id"])->update([
        'is_processed_by_kitchen' => 1
    ]);
    return response()->json(['order' => $orderData]);
});
