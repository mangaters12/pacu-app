@extends('layouts.admin')

@section('title', 'Edit Pembayaran #' . $payment->id)

@section('content')
<h1 class="text-3xl font-bold mb-6">Edit Pembayaran #{{ $payment->id }}</h1>

@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label for="order_id" class="block font-semibold mb-1">Pilih Order</label>
        <select name="order_id" id="order_id" class="w-full border rounded px-3 py-2" required>
            @foreach ($orders as $order)
                <option value="{{ $order->id }}" {{ $payment->order_id == $order->id ? 'selected' : '' }}>
                    Order #{{ $order->id }} - {{ $order->status }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="payment_method" class="block font-semibold mb-1">Metode Pembayaran</label>
        <input type="text" name="payment_method" id="payment_method" value="{{ $payment->payment_method }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label for="payment_proof" class="block font-semibold mb-1">Bukti Pembayaran (gambar)</label>
        <input type="file" name="payment_proof" id="payment_proof" accept="image/*">

        @if($payment->payment_proof)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti Pembayaran" class="max-w-xs rounded shadow">
            </div>
        @endif
    </div>

    <div class="mb-4">
        <label for="status" class="block font-semibold mb-1">Status Pembayaran</label>
        <select name="status" id="status" class="w-full border rounded px-3 py-2" required>
            <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ $payment->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="rejected" {{ $payment->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>

    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update</button>
</form>
@endsection
