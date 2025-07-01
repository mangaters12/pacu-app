@extends('layouts.admin')

@section('title', 'Daftar Order')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Daftar Order</h1>

        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('user'))
            <a href="{{ route('orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                + Tambah Order
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kurir</th> <!-- Tambahan -->
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Order</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $order->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $order->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $order->kurir ? $order->kurir->name : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('toko'))
                                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="dikemas" {{ $order->status == 'dikemas' ? 'selected' : '' }}>Dikemas</option>
                                        <option value="Dikirim" {{ $order->status == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                        <option value="sampai tujuan" {{ $order->status == 'sampai tujuan' ? 'selected' : '' }}>Sudah Sampai</option>
                                        <option value="batalkan" {{ $order->status == 'batalkan' ? 'selected' : '' }}>Batal</option>
                                    </select>
                                </form>
                            @else
                                {{ ucfirst($order->status) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-center text-sm font-medium space-x-2">
                            <a href="{{ route('orders.edit', $order->id) }}" class="text-yellow-600 hover:text-yellow-900 font-medium">✏️ Edit</a>

                            @if(auth()->user()->hasRole('admin'))
                                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin hapus order ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">Belum ada order</td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection
