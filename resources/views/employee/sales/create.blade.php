@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Pilih Produk</h1>
@endsection

@section('content')
<div class="container px-4 mx-auto" x-data="{
    search: '',
    get filteredProducts() {
        return this.products.filter(product => {
            return product.name.toLowerCase().includes(this.search.toLowerCase());
        });
    },
    products: {{ Js::from($products->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'image' => $product->image,
        ];
    })) }}
}">
    <!-- Search Bar -->
    <div class="mb-6">
        <input
            type="text"
            x-model="search"
            placeholder="Cari produk..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
        >
    </div>

    <!-- Daftar Produk dengan Scrollbar -->
    <form action="{{ route('employee.sales.confirm') }}" method="POST" class="max-w-2xl mx-auto">
        @csrf
        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
            <template x-for="product in filteredProducts" :key="product.id">
                <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                    <div class="flex items-center space-x-4">
                        <img :src="'{{ asset('storage') }}/' + product.image" :alt="product.name" class="object-cover w-16 h-16 rounded-lg">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="product.name"></h3>
                            <p class="text-gray-700 dark:text-gray-300">Rp <span x-text="new Intl.NumberFormat('id-ID').format(product.price)"></span></p>
                            <p class="text-gray-700 dark:text-gray-300">Stok: <span x-text="product.stock"></span></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input
                                type="number"
                                :name="'products[' + product.id + '][quantity]'"
                                min="0"
                                :max="product.stock"
                                class="w-20 px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                                placeholder="Qty"
                            >
                            <input type="hidden" :name="'products[' + product.id + '][id]'" :value="product.id">
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <div class="mt-6">
            <button type="submit" class="w-full px-6 py-2 text-white transition duration-200 bg-blue-500 rounded-lg hover:bg-blue-600">
                Selanjutnya
            </button>
        </div>
    </form>
</div>
@endsection
