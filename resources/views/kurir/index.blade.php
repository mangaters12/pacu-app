@extends('layouts.admin')

@section('title', 'Data Kurir')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Data Kurir</h1>
    <a href="{{ route('kurir.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
        + Tambah Kurir Baru
    </a>
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No KTP</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plat No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kurirs as $kurir)
            <tr>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $kurir->id }}</td>
                <td class="px-6 py-4 text-center">
                    <!-- Gambar kecil yang bisa diklik -->
                    <img src="{{ asset('storage/' . $kurir->photo_path) }}"
                         style="width:80px; cursor:pointer;"
                         onclick="showImage('{{ asset('storage/' . $kurir->photo_path) }}')"
                         alt="Foto Kurir" />
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $kurir->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $kurir->email }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $kurir->phone }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $kurir->ktp_number }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $kurir->plate_number }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $kurir->address }}</td>
                <td class="px-6 py-4 text-center space-x-2">
                    <a href="{{ route('kurir.show', $kurir->id) }}" class="text-blue-600 hover:underline">Detail</a>
                    <a href="{{ route('kurir.edit', $kurir->id) }}" class="text-green-600 hover:underline">Edit</a>
                    <form action="{{ route('kurir.destroy', $kurir->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4 text-gray-500">Tidak ada data kurir.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $kurirs->links() }}
</div>

<!-- Modal untuk menampilkan gambar besar -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-4 rounded shadow-lg max-w-xl max-h-full overflow-auto relative">
        <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800 text-2xl">×</button>
        <img id="modalImage" src="" alt="Gambar Kurir" class="w-full h-auto max-h-screen">
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
