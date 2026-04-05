<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LayawayController;
use App\Http\Controllers\ReportController;

Route::get('/', fn() => redirect()->route('dashboard'));

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class);
    Route::get('products-search', [ProductController::class, 'search'])->name('products.search');

    Route::resource('customers', CustomerController::class);
    Route::get('customers-search', [CustomerController::class, 'search'])->name('customers.search');

    Route::resource('cash-registers', CashRegisterController::class)->except(['edit', 'update', 'destroy']);
    Route::get('cash-registers/{cashRegister}/close', [CashRegisterController::class, 'close'])->name('cash-registers.close');
    Route::post('cash-registers/{cashRegister}/close', [CashRegisterController::class, 'doClose'])->name('cash-registers.do-close');

    Route::resource('sales', SaleController::class)->except(['edit', 'update', 'destroy']);
    Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    Route::get('sales/{sale}/ticket', [SaleController::class, 'ticket'])->name('sales.ticket');
    Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::get('inventory/{variant}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::post('inventory/{variant}/adjust', [InventoryController::class, 'doAdjust'])->name('inventory.do-adjust');

    Route::resource('layaways', LayawayController::class)->except(['edit', 'update', 'destroy']);
    Route::post('layaways/{layaway}/payment', [LayawayController::class, 'addPayment'])->name('layaways.payment');
    Route::post('layaways/{layaway}/cancel', [LayawayController::class, 'cancel'])->name('layaways.cancel');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');
});
