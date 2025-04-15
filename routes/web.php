<?php

use App\Exports\SalesExport;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('welcome');
    // Log::info('Tes Libur');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('/products', ProductController::class);
        Route::resource('/sales', SaleController::class);
        Route::resource('/users', UserController::class);
        Route::post('/sales/export', function () {
            return Excel::download(new SalesExport(), 'sales.xlsx');
        })->name('sales.export');
        Route::get('/sales/{sale}/pdf', [SaleController::class, 'exportPdf'])->name('sales.pdf');
        Route::get('/sale/{id}/export-pdf', [SaleController::class, 'exportPdfPerSale'])->name('sales.pdf-persale');
    });

    Route::middleware('role:employee')->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'indexPtEmployee'])->name('dashboard');
        Route::get('/products', [ProductController::class, 'indexPtEmployee'])->name('products.index');

        Route::get('/sales', [SaleController::class, 'indexPtEmployee'])->name('sales.index');
        Route::get('/sales/create', [SaleController::class, 'createPtEmployee'])->name('sales.create');
        Route::post('/sales/confirm', [SaleController::class, 'confirmSale'])->name('sales.confirm');
        // Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::post('/sales/store', [SaleController::class, 'storeSale'])->name('sales.store');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::get('/employee/sales/detail/{id}', [SaleController::class, 'detail'])->name('sales.detail');

        // ini fitur export
        Route::post('/sales/export', [SaleController::class, 'export'])
        ->name('sales.export');

        Route::get('/sales/{sale}/pdf', [SaleController::class, 'exportPdf'])->name('sales.pdf');

    });
    Route::prefix('member')->name('member.')->group(function() {
        Route::post('/sales/check-member', [MemberController::class, 'checkMember'])->name('sales.check-member');
        Route::post('/sales/register-member', [MemberController::class, 'registerMember'])->name('sales.register-member');    });
    });

Route::get('/storage-link', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');
    symlink($target, $link);
    return 'Storage link created!';
});

require __DIR__ . '/auth.php';
