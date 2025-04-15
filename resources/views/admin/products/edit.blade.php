@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Produk</h1>
@endsection

@section('content')
<div class="container px-4 mx-auto">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="max-w-2xl mx-auto">
        @csrf
        @method('PUT')

        <!-- Card Container -->
        <div class="overflow-hidden transition-all duration-300 bg-white shadow-xl dark:bg-gray-800 rounded-2xl hover:shadow-2xl">
            <!-- Header Gradient -->
            <div class="p-6 bg-gradient-to-r from-blue-500 to-purple-600">
                <h2 class="text-xl font-bold text-white">Edit Produk</h2>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6">
                <!-- Drag & Drop Area -->
                <div
                    id="drop-zone"
                    class="relative flex items-center justify-center h-48 transition-all duration-300 border-2 border-gray-300 border-dashed cursor-pointer group dark:border-gray-600 rounded-xl hover:border-blue-500 hover:bg-gray-50/50 dark:hover:bg-gray-700/50"
                    ondragover="event.preventDefault(); document.getElementById('drop-zone').classList.add('border-blue-500', 'bg-gray-50/50', 'dark:bg-gray-700/50')"
                    ondragleave="document.getElementById('drop-zone').classList.remove('border-blue-500', 'bg-gray-50/50', 'dark:bg-gray-700/50')"
                    ondrop="handleDrop(event)"
                >
                    <!-- Hidden File Input -->
                    <input type="file" name="image" id="image" class="hidden" accept="image/*">

                    <!-- Preview Content -->
                    <div class="space-y-2 text-center">
                        <!-- Preview Image -->
                        <div id="image-preview" class="w-24 h-24 mx-auto overflow-hidden border-2 border-white rounded-full shadow-lg">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Preview" class="object-cover w-full h-full">
                            @else
                                <div class="flex items-center justify-center w-full h-full bg-gray-100 dark:bg-gray-700">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Text -->
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="text-blue-600 dark:text-blue-400">Upload gambar</span> atau drag & drop
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Format: PNG, JPG, JPEG (Max 2MB)
                            </p>
                        </div>
                    </div>

                    <!-- Loading Overlay -->
                    <div id="loading-overlay" class="absolute inset-0 items-center justify-center hidden bg-black/50 rounded-xl">
                        <svg class="w-8 h-8 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="space-y-4">
                    <!-- Floating Label Input -->
                    <div class="relative">
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ $product->name ?? old('name') }}"
                            class="w-full px-4 py-2 placeholder-transparent bg-transparent border-0 border-b-2 border-gray-300 peer dark:border-gray-600 focus:ring-0 focus:border-blue-500 dark:focus:border-blue-400"
                            placeholder=" "
                        />
                        <label
                            for="name"
                            class="absolute left-0 -top-3.5 text-gray-600 dark:text-gray-400 text-sm transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3.5 peer-focus:text-blue-500 peer-focus:text-sm"
                        >
                            Nama Produk
                        </label>
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price Input with Currency Symbol -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400">Rp</span>
                        </div>
                        <input
                            type="number"
                            name="price"
                            id="price"
                            value="{{ $product->price ?? old('price') }}"
                            class="w-full px-4 py-2 pl-10 placeholder-transparent bg-transparent border-0 border-b-2 border-gray-300 peer dark:border-gray-600 focus:ring-0 focus:border-blue-500 dark:focus:border-blue-400"
                            placeholder=" "
                        />
                        <label
                            for="price"
                            class="absolute left-10 -top-3.5 text-gray-600 dark:text-gray-400 text-sm transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3.5 peer-focus:text-blue-500 peer-focus:text-sm"
                        >
                            Harga
                        </label>
                        @error('price')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock Input -->
                    <div class="relative">
                        <input
                            type="number"
                            name="stock"
                            id="stock"
                            value="{{ $product->stock ?? old('stock') }}"
                            class="w-full px-4 py-2 placeholder-transparent bg-transparent border-0 border-b-2 border-gray-300 peer dark:border-gray-600 focus:ring-0 focus:border-blue-500 dark:focus:border-blue-400"
                            placeholder=" "
                        />
                        <label
                            for="stock"
                            class="absolute left-0 -top-3.5 text-gray-600 dark:text-gray-400 text-sm transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3.5 peer-focus:text-blue-500 peer-focus:text-sm"
                        >
                            Stok
                        </label>
                        @error('stock')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-6 rounded-lg font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-[1.02] shadow-md"
                >
                    Update Produk
                </button>
            </div>
        </div>
    </form>
</div>

@section('script')
<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('image');
    const preview = document.getElementById('image-preview');
    const loadingOverlay = document.getElementById('loading-overlay');

    // Handle Drag & Drop
    function handleDrop(e) {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-gray-50/50', 'dark:bg-gray-700/50');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFile(files[0]);
        }
    }

    // Handle Click
    dropZone.addEventListener('click', () => fileInput.click());

    // Handle File Select
    fileInput.addEventListener('change', (e) => handleFile(e.target.files[0]));

    // Process File
    function handleFile(file) {
        if (!file.type.startsWith('image/')) return;

        // Show loading
        loadingOverlay.classList.remove('hidden');
        loadingOverlay.classList.add('flex');

        // Simulate upload delay
        setTimeout(() => {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.innerHTML = `<img src="${e.target.result}" class="object-cover w-full h-full animate-fade-in">`;
                loadingOverlay.classList.add('hidden');
                loadingOverlay.classList.remove('flex');
            }
            reader.readAsDataURL(file);
        }, 1000);
    }

    // Animation handler
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('!border-blue-500');
            });
            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('!border-blue-500');
            });
        });
    });
</script>
<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
@endsection
@endsection