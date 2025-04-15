<div class="space-y-6">
    <!-- Tabel Produk -->
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Nama Produk</th>
                    <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Harga</th>
                    <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Quantity</th>
                    <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($sale->products as $product)
                <tr class="transition duration-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $product->pivot->quantity }}</td>
                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Rp {{ number_format($product->price * $product->pivot->quantity, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Informasi Pembeli -->
    <div class="space-y-4">
        <p class="text-gray-700 dark:text-gray-300">
            <span class="font-medium">Nama Pembeli:</span> {{ $sale->customer_name }}
        </p>
        <p class="text-gray-700 dark:text-gray-300">
            <span class="font-medium">Jumlah Kembalian:</span> Rp {{ number_format($sale->change, 0, ',', '.') }}
        </p>
        <p class="text-gray-700 dark:text-gray-300">
            <span class="font-medium">Total Harga:</span> Rp {{ number_format($sale->total_price, 0, ',', '.') }}
        </p>
    </div>
</div>