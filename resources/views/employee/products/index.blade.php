@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Daftar Produk</h1>
@endsection

@section('content')
<div class="container px-4 mx-auto">
    <!-- Tabel Daftar Produk -->
    <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <table class="min-w-full">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="px-4 py-2 text-left text-gray-900 dark:text-gray-100">No</th>
                    <th class="px-4 py-2 text-left text-gray-900 dark:text-gray-100">Nama Produk</th>
                    <th class="px-4 py-2 text-left text-gray-900 dark:text-gray-100">Harga</th>
                    <th class="px-4 py-2 text-left text-gray-900 dark:text-gray-100">Stok</th>
                    <th class="px-4 py-2 text-left text-gray-900 dark:text-gray-100">Gambar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $index => $product)
                    <tr class="transition duration-200 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $product->name }}</td>
                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $product->stock }}</td>
                        <td class="px-4 py-2">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="object-cover w-16 h-16 rounded-lg">
                            @else
                                <span class="text-gray-500 dark:text-gray-400">Tidak ada gambar</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection