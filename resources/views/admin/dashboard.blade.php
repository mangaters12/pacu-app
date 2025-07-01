@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-semibold mb-8 text-gray-900">Dashboard Admin</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <p class="text-sm font-medium text-gray-500">Total Toko</p>
            <p class="text-4xl font-bold text-green-600">{{ $tokoCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <p class="text-sm font-medium text-gray-500">Total Users</p>
            <p class="text-4xl font-bold text-green-600">{{ $userCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <p class="text-sm font-medium text-gray-500">Total Roles</p>
            <p class="text-4xl font-bold text-green-600">{{ $users->unique('role')->count() }}</p>
        </div>
    </div>

    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Welcome Dashboard</h2>


</div>
@endsection
