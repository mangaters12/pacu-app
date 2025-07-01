@extends('layouts.admin')

@section('title', 'Produk Saya')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Produk Saya</h1>
        <a href="{{ route('toko.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
            + Tambah Produk
        </a>
    </div>

    {{-- Form pencarian --}}
    <form method="GET" action="{{ route('toko.dashboard') }}" class="mb-4 max-w-sm">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari produk..."
            class="border border-gray-300 px-3 py-2 rounded w-full"
        />
    </form>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow">
       <table class="min-w-full divide-y divide-gray-200">
           <thead>
               <tr>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toko</th>
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Pembuat</th> <!-- Ini kolom baru -->
                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                   <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
               </tr>
           </thead>
           <tbody class="bg-white divide-y divide-gray-200">
               @forelse ($products as $product)
               <tr>
                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->id }}</td>

                   <td class="px-6 py-4 whitespace-nowrap">
                       @if ($product->images->first())
                           <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="gambar produk" class="w-16 h-16 object-cover rounded">
                       @else
                           <span class="text-gray-400 italic text-sm">Tidak ada gambar</span>
                       @endif
                   </td>

                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->nama }}</td>
                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->toko->nama ?? '-' }}</td>

                   <!-- User Pembuat -->
                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                       {{ optional($product->toko->user)->name ?? '-' }}
                   </td>

                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $product->created_at->format('d M Y H:i') }}</td>
                   <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                       <a href="{{ route('toko.edit', $product->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                       <form action="{{ route('toko.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin hapus produk ini?')">
                           @csrf
                           @method('DELETE')
                           <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                       </form>
                   </td>
               </tr>
               @empty
               <tr>
                   <td colspan="8" class="text-center py-4 text-gray-500">Belum ada produk</td>
               </tr>
               @endforelse
           </tbody>
       </table>

    </div>
@endsection
