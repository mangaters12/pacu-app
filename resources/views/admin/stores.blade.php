@extends('layouts.app')

@section('title', 'Stores Management')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Daftar Stores</h1>
    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Nama Store</th>
                <th class="border px-4 py-2">Alamat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stores as $store)
                <tr>
                    <td class="border px-4 py-2">{{ $store->id }}</td>
                    <td class="border px-4 py-2">{{ $store->name }}</td>
                    <td class="border px-4 py-2">{{ $store->address }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
