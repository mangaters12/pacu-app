@extends('layouts.admin')

@section('title', 'Detail Order #' . $order->id)

@section('content')
    <h1 class="text-3xl font-bold mb-6">Detail Order #{{ $order->id }}</h1>

    <div class="mb-4">
        <p><strong>User:</strong> {{ $order->user->name ?? '-' }}</p>
        <p><strong>Status:</strong> <span class="capitalize">{{ $order->status }}</span></p>
        <p><strong>Total Harga:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        <p><strong>Tanggal Order:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
    </div>

    {{-- Jika admin atau toko, bisa update status --}}
    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('toko'))
        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="mb-6 max-w-xs">
            @csrf
            <label for="status" class="block mb-2 font-semibold">Update Status Order:</label>
            <select name="status" id="status" class="w-full border border-gray-300 rounded px-3 py-2">
                @php
                    $statuses = ['pending', 'paid', 'shipped', 'completed', 'canceled'];
                @endphp
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
        </form>
    @endif

    <h2 class="text-2xl font-semibold mb-4">Detail Produk</h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($order->orderDetails as $detail)
                    <tr>
