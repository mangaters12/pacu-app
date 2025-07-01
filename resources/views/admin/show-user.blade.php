@extends('layouts.admin')

@section('title', 'Detail User')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-900">Detail User: {{ $user->name }}</h1>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <p><strong>ID:</strong> {{ $user->id }}</p>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
        <p><strong>Status:</strong>
            @if($user->is_active)
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Aktif
                </span>
            @else
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    Non-Aktif
                </span>
            @endif
        </p>
    </div>

    <a href="{{ route('admin.users.index') }}" class="inline-block mt-6 text-[#0A2E6E] hover:underline font-semibold">Kembali ke Daftar Users</a>
@endsection
