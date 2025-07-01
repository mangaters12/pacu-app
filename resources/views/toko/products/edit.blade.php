@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-3xl font-bold mb-8">Edit Produk</h2>

    <form action="{{ route('toko.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Nama Produk --}}
        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="nama" class="md:w-1/4 font-semibold text-gray-700 mb-2 md:mb-0">Nama Produk <span class="text-red-500">*</span></label>
            <input type="text" id="nama" name="nama" value="{{ $product->nama }}" required
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Harga --}}
        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="harga" class="md:w-1/4 font-semibold text-gray-700 mb-2 md:mb-0">Harga <span class="text-red-500">*</span></label>
            <input type="number" id="harga" name="harga" value="{{ $product->harga }}" required min="0" step="0.01"
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Deskripsi --}}
        <div class="flex flex-col md:flex-row md:items-start md:space-x-6">
            <label for="deskripsi" class="md:w-1/4 font-semibold text-gray-700 mb-2 md:mb-0">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4"
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $product->deskripsi }}</textarea>
        </div>

        {{-- Gambar Saat Ini --}}
        <div class="flex flex-col md:flex-row md:items-start md:space-x-6">
            <label class="md:w-1/4 font-semibold text-gray-700 mb-2 md:mb-0">Gambar Saat Ini</label>
            <div class="md:flex-1 grid grid-cols-3 gap-4">
                @foreach($product->images as $image)
                <div class="relative rounded overflow-hidden border border-gray-200">
                    <img src="{{ asset($image->image_path) }}" alt="Gambar Produk" class="w-full h-32 object-cover rounded">
                    <form action="{{ route('toko.image.delete', ['productId' => $product->id, 'imageId' => $image->id]) }}" method="POST" class="absolute top-1 right-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Hapus gambar" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs focus:outline-none">
                            &times;
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tambah Gambar Baru --}}
        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="images" class="md:w-1/4 font-semibold text-gray-700 mb-2 md:mb-0">Tambah Gambar Baru</label>
            <input type="file" id="images" name="images[]" multiple
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Gambar URL Baru --}}
        <div class="flex flex-col md:flex-row md:items-start md:space-x-6">
            <label class="md:w-1/4 font-semibold text-gray-700 mb-2 md:mb-0">Gambar URL Baru (opsional)</label>
            <div class="md:flex-1 flex flex-col space-y-2">
                <input type="url" name="image_urls[]" placeholder="https://example.com/image1.jpg"
                    class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="url" name="image_urls[]" placeholder="https://example.com/image2.jpg"
                    class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded transition duration-300">
                Update Produk
            </button>
        </div>
    </form>
</div>
@endsection
