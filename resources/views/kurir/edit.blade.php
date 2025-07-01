@extends('layouts.admin')

@section('title', 'Edit Data Kurir')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Data Kurir</h1>

<form action="{{ route('kurir.update', $kurir->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block mb-1 font-semibold">Nama</label>
        <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('name', $kurir->name) }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Email</label>
        <input type="email" name="email" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('email', $kurir->email) }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Telepon</label>
        <input type="text" name="phone" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('phone', $kurir->phone) }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">No KTP</label>
        <input type="text" name="ktp_number" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('ktp_number', $kurir->ktp_number) }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Plat Nomor</label>
        <input type="text" name="plate_number" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('plate_number', $kurir->plate_number) }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Alamat</label>
        <textarea name="address" class="w-full border border-gray-300 rounded px-3 py-2" required>{{ old('address', $kurir->address) }}</textarea>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Jenis Kendaraan</label>
        <input type="text" name="vehicle_type" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('vehicle_type', $kurir->vehicle_type) }}" required>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Foto</label>
        @if($kurir->photo_path)
            <img src="{{ asset('storage/' . $kurir->photo_path) }}" class="w-20 h-20 object-cover mb-2" alt="Foto Kurir">
        @endif
        <input type="file" name="photo" class="w-full" accept="image/*">
    </div>
    <div>
        <label class="block mb-1 font-semibold">No SIM</label>
        <input type="text" name="driver_license_number" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ old('driver_license_number', $kurir->driver_license_number) }}">
    </div>
    <div class="mt-4">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Perbarui</button>
    </div>
</form>
@endsection
