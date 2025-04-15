@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tambah User</h1>
@endsection

@section('content')
<div class="container px-4 mx-auto">
    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl mx-auto">
        @csrf

        <!-- Field Nama dan Email (Menyamping) -->
        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500" required>
            </div>
        </div>

        <!-- Field Password dan Konfirmasi Password (Menyamping) -->
        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Password</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500" required>
            </div>
        </div>

        <!-- Field Role -->
        <div class="mb-6">
            <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Role</label>
            <select name="role" id="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-blue-500" required>
                <option value="admin">Admin</option>
                <option value="employee">Employee</option>
            </select>
        </div>

        <!-- Field Avatar dengan Animasi -->
        <div class="mb-6">
            <label for="avatar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Avatar</label>
            <div
                id="drop-zone"
                class="relative flex items-center justify-center h-48 transition-all duration-300 border-2 border-gray-300 border-dashed cursor-pointer group dark:border-gray-600 rounded-xl hover:border-blue-500 hover:bg-gray-50/50 dark:hover:bg-gray-700/50"
                ondragover="event.preventDefault(); document.getElementById('drop-zone').classList.add('border-blue-500', 'bg-gray-50/50', 'dark:bg-gray-700/50')"
                ondragleave="document.getElementById('drop-zone').classList.remove('border-blue-500', 'bg-gray-50/50', 'dark:bg-gray-700/50')"
                ondrop="handleDrop(event)"
            >
                <!-- Hidden File Input -->
                <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*">

                <!-- Preview Content -->
                <div class="space-y-2 text-center">
                    <!-- Preview Image -->
                    <div id="image-preview" class="w-24 h-24 mx-auto overflow-hidden border-2 border-white rounded-full shadow-lg">
                        <div class="flex items-center justify-center w-full h-full bg-gray-100 dark:bg-gray-700">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
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
        </div>

        <!-- Tombol Submit -->
        <button type="submit" class="w-full px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
            Simpan
        </button>
    </form>
</div>

@section('script')
<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('avatar');
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