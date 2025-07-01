@extends('layouts.admin')

@section('title', 'Daftar Pembayaran')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Daftar Pembayaran</h1>

        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('toko'))
            <a href="{{ route('payments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                + Tambah Pembayaran Baru
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detil Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode Pembayaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bukti Bayar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
          <tbody>
              @forelse($payments as $payment)
                  <tr>
                      <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->id }}</td>
                      <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->order_id }}</td>
                      <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->order->user->name ?? '-' }}</td>
                      <td class="px-6 py-4 text-sm text-gray-900">
                          {{ $payment->order->user->alamat ?? '-' }}
                      </td>
                      <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->payment_method }}</td>
                      <td class="px-6 py-4 text-center cursor-pointer">
                          @if($payment->payment_proof)
                              <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti Pembayaran" class="w-16 h-16 object-cover rounded shadow" onclick="showImage('{{ asset('storage/' . $payment->payment_proof) }}')">
                          @endif
                      </td>
                      <td class="px-6 py-4 text-sm text-gray-900">
                          <span class="px-2 py-1 rounded-full text-white
                              @if($payment->status == 'pending') bg-yellow-400
                              @elseif($payment->status == 'completed') bg-green-500
                              @elseif($payment->status == 'failed') bg-red-500
                              @else bg-gray-400
                              @endif">
                              {{ ucfirst($payment->status) }}
                          </span>
                      </td>
                      <td class="px-6 py-4 text-center text-sm font-medium space-x-2">
                          <a href="{{ route('payments.show', $payment->order_id) }}" class="text-blue-600 hover:underline font-medium">Detail</a>
                          <a href="{{ route('payments.edit', $payment->id) }}" class="text-green-600 hover:underline font-medium">Edit</a>
                          @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('toko'))
                              <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus pembayaran ini?')">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit" class="text-red-600 hover:underline font-medium">Hapus</button>
                              </form>
                          @endif
                      </td>
                  </tr>
              @empty
                  <tr>
                      <td colspan="9" class="text-center py-4 text-gray-500">Belum ada pembayaran</td>
                  </tr>
              @endforelse
          </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payments->links() }}
    </div>

    <!-- Modal untuk menampilkan gambar besar -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-4 rounded shadow-lg max-w-xl max-h-full overflow-auto relative">
            <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800 text-2xl">&times;</button>
            <img id="modalImage" src="" alt="Gambar Pembayaran" class="w-full h-auto max-h-screen">
        </div>
    </div>

    <!-- Script untuk modal -->
    <script>
        function showImage(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('imageModal').classList.remove('flex');
        }
    </script>
@endsection
