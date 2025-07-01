@extends('layouts.admin')

@section('title', 'Tambah Pembayaran Baru')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Tambah Pembayaran Baru</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Order</label>
            <select name="order_id" id="order_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="">-- Pilih Order --</option>
                @foreach ($orders as $order)
                    <option value="{{ $order->id }}">Order #{{ $order->id }} - {{ $order->status }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
            <input type="text" name="payment_method" id="payment_method" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>

        <div>
            <label for="payment_proof" class="block text-sm font-medium text-gray-700 mb-1">Bukti Pembayaran (gambar)</label>
            <input type="file" name="payment_proof" id="payment_proof" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
            file:rounded-full file:border-0
            file:text-sm file:font-semibold
            file:bg-blue-50 file:text-blue-700
            hover:file:bg-blue-100" required>
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
            <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <div class="mt-4">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Simpan</button>
        </div>
    </form>
</div>
@endsection
