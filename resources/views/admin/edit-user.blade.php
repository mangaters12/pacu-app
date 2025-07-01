@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-8">
    <h1 class="text-4xl font-extrabold mb-8 text-[#0A2E6E]">Edit User: {{ $user->name }}</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-400 text-red-700 rounded">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.update', $user->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Nama -->
        <div>
            <label for="name" class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                required
                placeholder="Masukkan nama lengkap"
                class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0A2E6E] focus:border-transparent transition"
            />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                required
                placeholder="example@email.com"
                class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0A2E6E] focus:border-transparent transition"
            />
        </div>

        <!-- Password & Konfirmasi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="password" class="block text-gray-700 font-semibold mb-2">Password <small class="text-gray-500">(kosongkan jika tidak diganti)</small></label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Minimal 6 karakter"
                    class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0A2E6E] focus:border-transparent transition"
                />
            </div>
            <div>
                <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="Ulangi password"
                    class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0A2E6E] focus:border-transparent transition"
                />
            </div>
        </div>

        <!-- Role -->
        <div>
            <label for="role" class="block text-gray-700 font-semibold mb-2">Role</label>
            <select
                id="role"
                name="role"
                required
                class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0A2E6E] focus:border-transparent transition"
            >
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
            </select>
        </div>

        <!-- Tambahkan field Alamat -->
        <div>
            <label for="alamat" class="block text-gray-700 font-semibold mb-2">Alamat</label>
            <input
                id="alamat"
                type="text"
                name="alamat"
                value="{{ old('alamat', $user->alamat ?? '') }}"
                placeholder="Masukkan alamat lengkap"
                class="w-full rounded-md border border-gray-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0A2E6E] focus:border-transparent transition"
            />
        </div>

        <!-- Status Aktif -->
        <label class="flex items-center space-x-2">
            <input type="hidden" name="is_active" value="0" />
            <input
                type="checkbox"
                name="is_active"
                value="1"
                {{ old('is_active', $user->is_active ?? false) ? 'checked' : '' }}
                class="h-5 w-5 text-[#0A2E6E] border-gray-300 rounded focus:ring-[#0A2E6E]"
            />
            <span class="text-gray-700 font-semibold select-none">Status Aktif</span>
        </label>

        <!-- Tombol -->
        <div class="flex items-center space-x-4">
            <button
                type="submit"
                class="bg-[#0A2E6E] hover:bg-[#0C3B8E] text-white font-semibold px-6 py-3 rounded-md shadow transition"
            >
                Update
            </button>
            <a
                href="{{ route('admin.dashboard') }}"
                class="text-[#0A2E6E] font-semibold hover:underline"
            >
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
