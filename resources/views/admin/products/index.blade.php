@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Manajemen Produk</h1>
@endsection

@section('content')
<div class="container px-4 mx-auto">
    <!-- Tombol Tambah Produk -->
    <div class="mb-6">
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 text-white transition duration-200 bg-blue-500 rounded-lg hover:bg-blue-600">
            Tambah Produk
        </a>
    </div>

    <!-- Tabel Daftar Produk -->
    <div class="overflow-hidden bg-white rounded-lg shadow-md dark:bg-gray-800">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr class="border-b dark:border-gray-700">
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">No</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Nama Produk</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Harga</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Stok</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Gambar</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($products as $index => $product)
                    <tr class="transition duration-200 hover:bg-gray-50 dark:hover:bg-gray-700">
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
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-500 transition duration-200 hover:text-blue-700 dark:hover:text-blue-400">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-red-500 transition duration-200 hover:text-red-700 dark:hover:text-red-400">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
