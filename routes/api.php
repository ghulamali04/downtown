<?php

use App\Models\Order;
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

    return response()->json(['order' => $orderData]);
});
