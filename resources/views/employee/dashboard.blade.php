@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Dashboard Employee</h1>
@endsection

@section('content')
<div class="container px-4 py-8 mx-auto">
    <!-- Card Selamat Datang -->
    <div class="mb-8">
        <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Selamat Datang, {{ Auth::user()->name }}!</h2>
            <p class="mt-2 text-gray-700 dark:text-gray-300">Selamat bekerja dan raih target penjualan hari ini!</p>
        </div>
    </div>

    <!-- Card Total Penjualan Hari Ini -->
    <div class="mb-8">
        <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Penjualan Hari Ini</h3>
                    <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">
                        Rp {{ number_format($totalSalesToday, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Terakhir diperbarui: {{ now()->format('H:i') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Produk Tersedia -->
    <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-gray-100">Daftar Produk Tersedia</h2>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ($products as $product)
                <div class="p-4 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                    <p class="text-gray-700 dark:text-gray-300">Harga: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <p class="text-gray-700 dark:text-gray-300">Stok: {{ $product->stock }}</p>
                    <x-primary-button class="mt-4" onclick="window.location.href='{{ route('employee.sales.create') }}'">
                        Buat Transaksi
                    </x-primary-button>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
