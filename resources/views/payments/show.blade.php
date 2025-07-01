@extends('layouts.admin')

@section('title', 'Detail Pembayaran Order #' . $order->id)

@section('content')
<h1 class="text-3xl font-bold mb-6">Pembayaran Order #{{ $order->id }}</h1>

<div class="mb-4">
    <p><strong>Status Order:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Total Harga:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
</div>

@if(session('success'))
    <div class="p-4 bg-green-100 text-green-700 rounded mb-4">{{ session('success') }}</div>
@endif

<form action="{{ route('payments.dashboard', $order->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label for="payment_method" class="block mb-2 font-semibold">Metode Pembayaran:</label>
    <input type="text" name="payment_method" id="payment_method" class="w-full border rounded px-3 py-2 mb-4" required>

    <label for="payment_proof" class="block mb-2 font-semibold">Upload Bukti Pembayaran (foto):</label>
    <input type="file" name="payment_proof" id="payment_proof" accept="image/*" class="mb-4" required>

    @error('payment_proof')
        <div class="text-red-600 mb-4">{{ $message }}</div>
    @enderror

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Kirim Pembayaran</button>
</form>

@if($payment && $payment->payment_proof)
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-2">Bukti Pembayaran Saat Ini:</h2>
        <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti Pembayaran" class="max-w-xs rounded shadow">
    </div>
@endif
@endsection
