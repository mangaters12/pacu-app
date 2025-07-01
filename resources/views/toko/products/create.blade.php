@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-5xl mx-auto p-6 bg-white rounded shadow">

    <h2 class="text-3xl font-bold mb-6">Tambah Produk</h2>

    {{-- Notifikasi sukses --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    {{-- Notifikasi error --}}
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('toko.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Nama Produk --}}
        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="nama" class="md:w-1/4 text-gray-700 font-semibold mb-2 md:mb-0">Nama Produk <span class="text-red-500">*</span></label>
            <input type="text" name="nama" id="nama" required
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="{{ old('nama') }}">
        </div>

        {{-- Deskripsi Produk --}}
        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="deskripsi" class="md:w-1/4 text-gray-700 font-semibold mb-2 md:mb-0">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="3"
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi') }}</textarea>
        </div>

        {{-- Harga --}}
        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="harga" class="md:w-1/4 text-gray-700 font-semibold mb-2 md:mb-0">Harga <span class="text-red-500">*</span></label>
            <input type="number" name="harga" id="harga" required min="0" step="0.01"
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="{{ old('harga') }}">
        </div>

        {{-- Upload Gambar --}}
        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="images" class="md:w-1/4 text-gray-700 font-semibold mb-2 md:mb-0">Upload Gambar</label>
            <div class="md:flex-1">
                <input type="file" name="images[]" id="images" multiple
                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Format: jpg, png, max 2MB per file</p>
            </div>
        </div>

        {{-- URL Gambar --}}
       <label class="md:w-1/4 text-gray-700 font-semibold mb-2 md:mb-0">Link Gambar (Opsional)</label>
       <div class="md:flex-1 flex space-x-4">
           <input type="url" name="image_urls[]" placeholder="https://example.com/image1.jpg"
               class="flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
               value="{{ old('image_urls.0') }}">
           <input type="url" name="image_urls[]" placeholder="https://example.com/image2.jpg"
               class="flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
               value="{{ old('image_urls.1') }}">
       </div>
       <p class="text-sm text-gray-500 mt-1">Isi link gambar hanya jika ada, tidak wajib.</p>


        {{-- Bagian khusus admin --}}
        @if(Auth::user()->hasRole('admin'))
        <hr class="my-6 border-gray-300">

        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="toko_id" class="md:w-1/4 text-gray-700 font-semibold mb-2 md:mb-0">Pilih Toko</label>
            <select name="toko_id" id="toko_id"
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 mb-3 md:mb-0 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" {{ old('toko_id') == '' ? 'selected' : '' }}>-- Buat Toko Baru --</option>
                @foreach($toko as $tk)
                <option value="{{ $tk->id }}" {{ old('toko_id') == $tk->id ? 'selected' : '' }}>{{ $tk->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="toko_nama" class="md:w-1/4 text-gray-700 font-semibold mb-2 md:mb-0">Nama Toko Baru (jika membuat toko baru)</label>
            <input type="text" name="toko_nama" id="toko_nama"
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="{{ old('toko_nama') }}">
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="toko_alamat" class="md:w-1/4 text-gray-700 font-semibold mb-2 md:mb-0">Alamat Toko Baru (jika membuat toko baru)</label>
            <textarea name="toko_alamat" id="toko_alamat" rows="3"
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('toko_alamat') }}</textarea>
        </div>
        @endif

        {{-- Bagian khusus user role toko yang belum punya toko --}}
        @if(Auth::user()->hasRole('toko') && Auth::user()->toko === null)
        <hr class="my-6 border-gray-300">

        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="toko_nama" class="md:w-1/4 text-gray-700 font-semibold mb-2 md:mb-0">Nama Toko Baru <span class="text-red-500">*</span></label>
            <input type="text" name="toko_nama" id="toko_nama"
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="{{ old('toko_nama') }}" required>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
            <label for="toko_alamat" class="md:w-1/4 text-gray-700 font-semibold mb-2 md:mb-0">Alamat Toko Baru <span class="text-red-500">*</span></label>
            <textarea name="toko_alamat" id="toko_alamat" rows="3" required
                class="md:flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('toko_alamat') }}</textarea>
        </div>
        @endif

        {{-- Tombol Submit --}}
        <div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded w-full transition duration-300">
                Simpan Produk
            </button>
        </div>

    </form>
</div>
@endsection
