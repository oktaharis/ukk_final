<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $dailySales = Sale::selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailySalesLabels = $dailySales->pluck('date');
        $dailySalesData = $dailySales->pluck('total');

        $productSales = Product::withSum('sales as total_sold', 'product_sale.quantity')
            ->orderByDesc('total_sold')
            ->get();

        $productSalesLabels = $productSales->pluck('name');
        $productSalesData = $productSales->pluck('total_sold');

        return view('admin.dashboard', compact('dailySalesLabels', 'dailySalesData', 'productSalesLabels', 'productSalesData'));
    }

    public function indexPtEmployee()
    {
        // Ambil produk dengan stok > 0
        $products = Product::where('stock', '>', 0)->get();

        // Hitung total penjualan hari ini
        $totalSalesToday = Sale::whereDate('created_at', today())
            ->sum('total_price');

        return view('employee.dashboard', compact('products', 'totalSalesToday'));
    }
}
