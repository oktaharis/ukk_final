<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <!-- Notifikasi -->
    <div id="notification" class="fixed hidden p-4 text-white rounded-lg shadow-lg top-4 right-4 z-[99999]">
        <div class="flex items-center">
            <span id="notification-message"></span>
            <button onclick="hideNotification()" class="ml-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header class="bg-white shadow dark:bg-gray-800">
            <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset
        <br>
        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Script Section -->
    @yield('script')
    <script>
        // Fungsi untuk menampilkan notifikasi
            function showNotification(message, type = 'success') {
                const notification = document.getElementById('notification');
                const notificationMessage = document.getElementById('notification-message');

                // Set pesan notifikasi
                notificationMessage.textContent = message;

                // Set warna notifikasi berdasarkan jenis
                if (type === 'success') {
                    notification.classList.remove('bg-red-500', 'bg-blue-500');
                    notification.classList.add('bg-green-500');
                } else if (type === 'error') {
                    notification.classList.remove('bg-green-500', 'bg-blue-500');
                    notification.classList.add('bg-red-500');
                } else if (type === 'info') {
                    notification.classList.remove('bg-green-500', 'bg-red-500');
                    notification.classList.add('bg-blue-500');
                }

                // Tampilkan notifikasi
                notification.classList.remove('hidden');
                notification.classList.add('block');

                // Sembunyikan notifikasi setelah 5 detik
                setTimeout(() => {
                    hideNotification();
                }, 5000);
            }

            // Fungsi untuk menyembunyikan notifikasi
            function hideNotification() {
                const notification = document.getElementById('notification');
                notification.classList.remove('block');
                notification.classList.add('hidden');
            }

            // Tampilkan notifikasi jika ada pesan dari session
            @if (session('status'))
                showNotification("{{ session('status') }}", 'success');
            @endif

            @if (session('error'))
                showNotification("{{ session('error') }}", 'error');
            @endif

            @if (session('info'))
                showNotification("{{ session('info') }}", 'info');
            @endif
    </script>
</body>

</html>
