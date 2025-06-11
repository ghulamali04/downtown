<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InventoryTrackingController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\MenuItemVariantController;
use App\Http\Controllers\OpenApiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminLevelAuth;
use App\Http\Middleware\IpCheck;
use App\Http\Middleware\SuperAdminLevelAuth;
use Illuminate\Support\Facades\Route;

Route::post('/open/print/receipt', [PrintController::class, 'printReceipt']);

Route::get('/', function () {
    return redirect('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::middleware([IpCheck::class])->group(function () {
        Route::post('/print/receipt', [OrderController::class, 'printReceipt']);

        Route::get('/dashboard', function () {
            return view('index');
        })
        ->name('home')
        ->middleware('auth');

        Route::get('/openapi/menu/categories', [OpenApiController::class, 'getMenuCategories'])->name('openapi.menu.categories');
        Route::get('/openapi/menu/items', [OpenApiController::class, 'getMenuItems'])->name('openapi.menu.items');
        Route::get('/openapi/menu/variants', [OpenApiController::class, 'getMenuItemVariants'])->name('openapi.menu.variants');

        Route::get('/account-settings', [AccountController::class, 'index'])->name('account.settings');
        Route::put('/account-settings', [AccountController::class, 'update'])->name('account.settings.update');
        Route::put('/account-settings/password', [AccountController::class, 'change_password'])->name('account.settings.password.update');

        Route::get('/orders/export', [OrderController::class, 'exportOrders']);
        Route::get('/order/current/balance/{start_date?}/{end_date?}', [OrderController::class, 'getCurrentBalance']);
        Route::post('/order/receipt/print/{order}', [OrderController::class, 'printTestPage'])->name('order.receipt.print');
        Route::get('/order/receipt/{order}', [OrderController::class, 'receipt'])->name('order.receipt');
        Route::get('/order/data', [OrderController::class, 'get_data'])->name('order.data');
        Route::resource('/order', OrderController::class);

        Route::get('/customer/live', [CustomerController::class, 'get_live'])->name('customer.live');
        Route::get('/customer/data', [CustomerController::class, 'get_data'])->name('customer.data');
        Route::resource('customer', CustomerController::class);

        Route::get('/order/data/latest', [OrderController::class, 'get_latest_pending_orders'])->name('order.data.latest');
        Route::get('/order/{order}/update/{status}', [OrderController::class, 'update_status'])->name('order.update.status');

        Route::middleware([AdminLevelAuth::class])->group(function () {

            Route::get('/menu/category/data', [MenuCategoryController::class, 'get_data'])->name('category.data');
            Route::resource('/menu/category', MenuCategoryController::class);

            Route::get('/menu/variant/data', [MenuItemVariantController::class, 'get_data'])->name('variant.data');
            Route::resource('/menu/variant', MenuItemVariantController::class);

            Route::get('/menu/data', [MenuItemController::class, 'get_data'])->name('menu.data');
            Route::resource('/menu', MenuItemController::class);

            Route::get('/inventory/item/data', [InventoryItemController::class, 'get_data'])->name('item.data');
            Route::resource('/inventory/item', InventoryItemController::class);

            Route::get('/inventory/tracking/current/balance/{date}', [InventoryTrackingController::class, 'getCurrentBalance'])->name('tracking.current.balance');
            Route::get('/inventory/tracking/data', [InventoryTrackingController::class, 'get_data'])->name('tracking.data');
            Route::resource('/inventory/tracking', InventoryTrackingController::class);

            Route::get('/user/data', [UserController::class, 'get_data'])->name('user.data');
            Route::resource('/user', UserController::class);
            Route::put('/user/change-password/{user}', [UserController::class, 'change_password'])->name('user.password.update');
        });
    });
});
