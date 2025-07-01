@extends('layouts.admin')

@section('title', 'Daftar Orderan')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Daftar Orderan</h1>
    <a href="{{ route('kurir.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-semibold shadow">
        Kelola Kurir
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded shadow">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded shadow">
        {{ session('error') }}
    </div>
@endif

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Order</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Harga</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kurir</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($orders as $order)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $order->id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->user->name ?? 'Pengguna Terhapus' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm capitalize">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($order->status == 'pending') bg-yellow-200 text-yellow-800
                        @elseif($order->status == 'taken') bg-green-200 text-green-800
                        @else bg-gray-200 text-gray-800
                        @endif">
                        {{ $order->status }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    {{ $order->kurir->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    @if($order->status == 'pending')
                    <form action="{{ route('kurir.takeOrder', ['orderId' => $order->id]) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded shadow text-sm">
                            Ambil Order
                        </button>
                    </form>
                    @else
                        <span class="text-gray-500 text-sm">Order sudah diambil</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Tidak ada data orderan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection
