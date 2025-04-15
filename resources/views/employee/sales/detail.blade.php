@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Detail Pesanan</h1>
@endsection

@section('content')
<div class="container px-4 mx-auto">
    <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <!-- Tombol Kembali dan Unduh PDF -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('employee.sales.index') }}" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                Kembali
            </a>
            <a href="{{ route('employee.sales.pdf', $sale->id) }}" class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700">
                Unduh PDF
            </a>
        </div>

        <!-- Detail Transaksi -->
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2">
            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Informasi Transaksi</h2>
                <div class="space-y-2">
                    <p class="text-gray-700 dark:text-gray-300">
                        <span class="font-medium">Nama Pelanggan:</span> {{ $sale->customer_name ?? 'N/A' }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        <span class="font-medium">Status:</span> {{ ucfirst($sale->status ?? 'pending') }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        <span class="font-medium">Tanggal Transaksi:</span> {{ $sale->created_at ? $sale->created_at->format('d M Y H:i') : '-' }}
                    </p>
                </div>
            </div>

            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Ringkasan Pembayaran</h2>
                <div class="space-y-2">
                    <p class="text-gray-700 dark:text-gray-300">
                        <span class="font-medium">Total Harga:</span> Rp {{ number_format($sale->total_price ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        <span class="font-medium">Jumlah Bayar:</span> Rp {{ number_format($sale->amount_paid ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        <span class="font-medium">Kembalian:</span> Rp {{ number_format($sale->change ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Daftar Produk -->
        <div class="mb-8">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Produk yang Dibeli</h2>
            @if ($sale->products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-600">
                            <tr>
                                <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Nama Produk</th>
                                <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Harga</th>
                                <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Qty</th>
                                <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($sale->products as $product)
                            <tr class="transition duration-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $product->pivot->quantity }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Rp {{ number_format($product->price * $product->pivot->quantity, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-700 dark:text-gray-300">Tidak ada produk dalam transaksi ini.</p>
            @endif
        </div>
    </div>
</div>
@endsection