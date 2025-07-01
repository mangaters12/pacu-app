@extends('layouts.admin')

@section('title', 'Tambah Data Kurir')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Data Kurir</h1>

<form action="{{ route('kurir.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow space-y-4">
    @csrf
    <div>
        <label class="block mb-1 font-semibold">Nama</label>
        <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('name') }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Email</label>
        <input type="email" name="email" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('email') }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Telepon</label>
        <input type="text" name="phone" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('phone') }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">No KTP</label>
        <input type="text" name="ktp_number" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('ktp_number') }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Plat Nomor</label>
        <input type="text" name="plate_number" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('plate_number') }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Alamat</label>
        <textarea name="address" class="w-full border border-gray-300 rounded px-3 py-2" required>{{ old('address') }}</textarea>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Jenis Kendaraan</label>
        <input type="text" name="vehicle_type" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('vehicle_type') }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Foto</label>
        <input type="file" name="photo" class="w-full" accept="image/*">
    </div>
    <div>
        <label class="block mb-1 font-semibold">No SIM</label>
        <input type="text" name="driver_license_number" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('driver_license_number') }}">
    </div>
    <div class="mt-4">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
    </div>
</form>
@endsection
