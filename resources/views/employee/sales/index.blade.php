@extends('layouts.app')

@section('header')
<h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Daftar Penjualan</h1>
@endsection

@section('content')
<div class="container px-4 mx-auto" x-data="{
    openModal: false,
    selectedSale: null,
    filter: '{{ request('filter') }}',
    date: '{{ request('date') }}',
    search: '{{ request('search') }}'
}">
    <!-- Action Bar -->
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <!-- Export Excel -->
        <form action="{{ route('employee.sales.export') }}" method="POST" class="w-full sm:w-auto">
            @csrf
            <input type="hidden" name="filter" x-bind:value="filter">
            <input type="hidden" name="date" x-bind:value="date">
            <input type="hidden" name="search" x-bind:value="search">
            <button type="submit" class="w-full px-4 py-2 text-white transition duration-200 bg-green-500 rounded-lg hover:bg-green-600 sm:w-auto">
                Export Excel
            </button>
        </form>

        <!-- Tambah Penjualan -->
        <a href="{{ route('employee.sales.create') }}"
            class="w-full px-4 py-2 text-center text-white transition duration-200 bg-blue-500 rounded-lg hover:bg-blue-600 sm:w-auto">
            Tambah Penjualan
        </a>
    </div>

    <!-- Filter dan Search Bar -->
    <div class="mb-6">
        <form action="{{ route('employee.sales.index') }}" method="GET"
              class="flex flex-col gap-4 sm:flex-row sm:items-end sm:gap-4">
            <!-- Dropdown Filter -->
            <div class="flex-1">
                <label for="filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Filter Berdasarkan
                </label>
                <select name="filter" id="filter" x-model="filter"
                        class="block w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                        @change="date = ''">
                    <option value="">Semua</option>
                    <option value="daily" {{ request('filter') == 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="monthly" {{ request('filter') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="yearly" {{ request('filter') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>

            <!-- Input Tanggal Dinamis -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Pilih Waktu
                </label>
                <div class="mt-1">
                    <template x-if="filter === 'daily'">
                        <input type="date" name="date" x-model="date"
                               class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </template>

                    <template x-if="filter === 'monthly'">
                        <input type="month" name="date" x-model="date"
                               class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </template>

                    <template x-if="filter === 'yearly'">
                        <input type="number" name="date" x-model="date" min="2000" max="2100"
                               placeholder="Masukkan tahun (contoh: 2024)"
                               class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </template>
                </div>
            </div>

            <!-- Input Pencarian -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Cari Penjualan
                </label>
                <input type="text" name="search" id="search" x-model="search" placeholder="Cari..."
                       class="block w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
            </div>

            <!-- Tombol Terapkan -->
            <div class="sm:flex-none">
                <button type="submit"
                        class="w-full px-4 py-2 text-white transition duration-200 bg-blue-500 rounded-lg sm:w-auto hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Terapkan
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden bg-white rounded-lg shadow-md dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">ID</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Nama Pelanggan</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Tanggal Penjualan</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Total Harga</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Dibuat Oleh</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @foreach ($sales as $sale)
                <tr class="transition duration-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 text-gray-900 whitespace-nowrap dark:text-gray-100">{{ $sale->id }}</td>
                    <td class="px-6 py-4 text-gray-900 whitespace-nowrap dark:text-gray-100">{{ $sale->customer_name }}</td>
                    <td class="px-6 py-4 text-gray-900 whitespace-nowrap dark:text-gray-100">{{ $sale->created_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4 text-gray-900 whitespace-nowrap dark:text-gray-100">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-gray-900 whitespace-nowrap dark:text-gray-100">{{ $sale->user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <a href="#" @click="openModal = true; selectedSale = {{ $sale->id }}" class="text-blue-500 transition duration-200 hover:text-blue-700 dark:hover:text-blue-400">
                                Lihat
                            </a>
                            <span class="text-gray-400">|</span>
                            <a href="{{ route('employee.sales.pdf', $sale) }}"
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
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $sales->links() }}
    </div>

    <!-- Modal -->
    <div x-show="openModal" @click.away="openModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div class="w-11/12 p-6 bg-white rounded-lg shadow-lg md:w-1/2 dark:bg-gray-800">
            <!-- Header Modal -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Detail Pesanan</h2>
                <button @click="openModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-400">
                    &times;
                </button>
            </div>

            <!-- Konten Modal -->
            <div>
                @foreach ($sales as $sale)
                    <div x-show="selectedSale === {{ $sale->id }}">
                        @include('employee.sales.show', ['sale' => $sale])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
