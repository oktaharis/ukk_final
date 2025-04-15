@extends('layouts.app')

@section('header')
<h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Daftar Penjualan</h1>
@endsection

@section('content')
<div class="container px-4 mx-auto"
    x-data="{
        openModal: false,
        selectedSale: null,
        search: '',
        get filteredSales() {
            return this.sales.filter(sale =>
                sale.customer_name.toLowerCase().includes(this.search.toLowerCase()) ||
                sale.status.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        sales: {{ Js::from($sales->map(function ($sale) {
            return [
                'id' => $sale->id,
                'customer_name' => $sale->customer_name,
                'status' => $sale->status,
                'created_at' => $sale->created_at->format('d M Y'),
                'total_price' => $sale->total_price,
                'user_role' => $sale->user->role,
            ];
        })) }}
    }"
>
    <!-- Action Bar -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex space-x-2">
            <form action="{{ route('admin.sales.export') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="px-4 py-2 text-white transition duration-200 bg-green-500 rounded-lg hover:bg-green-600">
                    Export Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <input
            type="text"
            x-model="search"
            placeholder="Cari nama pelanggan atau status..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
        >
    </div>

    <!-- Table -->
    <div class="overflow-hidden bg-white rounded-lg shadow-md dark:bg-gray-800">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">ID</th>
                    <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Nama Pelanggan</th>
                    <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Tanggal</th>
                    <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Total Harga</th>
                    <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Role</th>
                    <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="sale in filteredSales" :key="sale.id">
                    <tr class="transition duration-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100" x-text="sale.id"></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100" x-text="sale.customer_name"></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100" x-text="sale.created_at"></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(sale.total_price)"></span>
                        </td>
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100" x-text="sale.user_role"></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <button @click="openModal = true; selectedSale = sale.id"
                                    class="text-blue-500 transition duration-200 hover:text-blue-700 dark:hover:text-blue-400">
                                    Lihat
                                </button>
                                <span class="text-gray-400">|</span>
                                <a :href="`/admin/sales/pdf-persale/${sale.id}`"
                                    class="text-red-500 transition duration-200 hover:text-red-700 dark:hover:text-red-400"
                                    title="Cetak PDF">
                                    <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Tidak Ada Data -->
    <div x-show="filteredSales.length === 0" class="py-4 text-center text-gray-500 dark:text-gray-400">
        Tidak ada data penjualan ditemukan.
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $sales->links() }}
    </div>

    <!-- Modal -->
    <div x-show="openModal" @click.away="openModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div class="w-11/12 p-6 bg-white rounded-lg shadow-lg md:w-1/2 dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Detail Pesanan</h2>
                <button @click="openModal = false"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-400">&times;</button>
            </div>
            <div>
                @foreach ($sales as $sale)
                    <div x-show="selectedSale === {{ $sale->id }}">
                        @include('admin.sales.show', ['sale' => $sale])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
