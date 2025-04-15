@extends('layouts.app')

@section('header')
    <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
        {{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}
    </h1>
@endsection

@section('content')
<div class="max-w-xl px-4 py-6 mx-auto">
    <form
        action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="p-6 space-y-6 bg-white rounded-lg shadow dark:bg-gray-800"
    >
        @csrf
        @if (isset($product))
            @method('PUT')
        @endif

        <!-- Gambar -->
        <div class="space-y-2">
            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gambar Produk</label>
            <input
                type="file"
                name="image"
                id="image"
                accept="image/*"
                class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
            >
            @if (isset($product) && $product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="Preview" class="object-cover w-24 h-24 mt-2 rounded">
            @endif
        </div>

        <!-- Nama Produk -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Produk</label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $product->name ?? '') }}"
                class="w-full px-3 py-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600"
            >
            @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
        </div>

        <!-- Harga -->
        <div>
            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga</label>
            <input
                type="number"
                name="price"
                id="price"
                value="{{ old('price', $product->price ?? '') }}"
                class="w-full px-3 py-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600"
            >
            @error('price') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
        </div>

        <!-- Stok -->
        <div>
            <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stok</label>
            <input
                type="number"
                name="stock"
                id="stock"
                value="{{ old('stock', $product->stock ?? '') }}"
                class="w-full px-3 py-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600"
            >
            @error('stock') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
        </div>

        <!-- Tombol Submit -->
        <div>
            <button
                type="submit"
                class="w-full py-2 text-white transition bg-blue-600 rounded hover:bg-blue-700"
            >
                {{ isset($product) ? 'Update Produk' : 'Simpan Produk' }}
            </button>
        </div>
    </form>
</div>
@endsection
