@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Manajemen User</h1>
@endsection

@section('content')
<div class="container px-4 mx-auto">
    <!-- Tombol Tambah User -->
    <div class="mb-6">
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
            Tambah User
        </a>
    </div>

    <!-- Tabel User -->
    <div class="overflow-hidden bg-white rounded-lg shadow-md dark:bg-gray-800">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Avatar</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Nama</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Email</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Role</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($users as $user)
                <tr class="transition duration-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <!-- Avatar -->
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 overflow-hidden rounded-full">
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://via.placeholder.com/150' }}" alt="Avatar" class="object-cover w-full h-full">
                        </div>
                    </td>
                    <!-- Nama -->
                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                    <!-- Email -->
                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                    <!-- Role -->
                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ ucfirst($user->role) }}</td>
                    <!-- Aksi -->
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="gap-0 text-blue-500 hover:text-blue-700">
                            Edit
                        </a>
                        <span class="text-gray-400">|</span>

                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
