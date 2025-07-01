@extends('layouts.user')

@section('title', 'Checkout Keranjang')

@section('content')
<h1 class="text-2xl font-semibold mb-4 text-center">Checkout Keranjang</h1>

<div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-5 hover:shadow-md transition">

    @if($cartItems->isEmpty())
        <p class="text-center text-red-600">Keranjang Anda kosong.</p>
    @else
        <table class="w-full mb-4">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Produk</th>
                    <th class="p-2">Harga</th>
                    <th class="p-2">Jumlah</th>
                    <th class="p-2">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                <tr class="border-b">
                    <td class="p-2">{{ $item->product->nama }}</td>
                    <td class="p-2">Rp {{ number_format($item->product->harga,0,',','.') }}</td>
                    <td class="p-2">{{ $item->quantity }}</td>
                    <td class="p-2 font-semibold">Rp {{ number_format($item->product->harga * $item->quantity,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right font-bold text-lg mb-6">
            Total Bayar: Rp {{ number_format($total,0,',','.') }}
        </div>

        <form method="POST" action="{{ route('checkout.processAll') }}" enctype="multipart/form-data" class="space-y-6 max-w-md mx-auto">
            @csrf

            {{-- Alamat Pengiriman --}}
            <label for="alamat" class="block font-medium text-gray-700 mb-1">Alamat Pengiriman</label>
            <textarea
                name="alamat"
                id="alamat"
                rows="4"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 resize-none"
                placeholder="Masukkan alamat pengiriman..."
                required
            >{{ old('address', $user->address ?? '') }}</textarea>
            @error('address')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror

            {{-- Status lokasi live --}}
            <p id="locationStatus" class="mt-2 text-sm text-gray-600"></p>

            {{-- Tombol Ambil Lokasi Live --}}
            <button type="button" id="btnGetLocation" class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded transition disabled:opacity-50" >
                <svg id="loadingSpinner" class="animate-spin -ml-1 mr-2 h-5 w-5 text-gray-600 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                📍 Ambil Lokasi Saya (Live Tracking)
            </button>

            {{-- Metode Pembayaran --}}
            <label for="payment_method" class="block font-medium text-gray-700 mt-4 mb-1">Metode Pembayaran</label>
            <select name="payment_method" id="payment_method" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="" disabled selected>-- Pilih Metode --</option>
                <option value="transfer_bank">🏦 Transfer Bank</option>
                <option value="ovo">📱 OVO</option>
                <option value="gopay">🚀 GoPay</option>
                <option value="dana">💰 Dana</option>
            </select>
            @error('payment_method')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror

            {{-- Upload Bukti Bayar --}}
            <label for="payment_proof" class="block font-medium text-gray-700 mt-4 mb-1">Upload Bukti Bayar</label>
            <input type="file" name="payment_proof" id="payment_proof" accept="image/*" class="w-full border border-gray-300 rounded px-3 py-2" required>
            @error('payment_proof')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold mt-6">
                Bayar Sekarang
            </button>
        </form>
    @endif

</div>

<script>
    const btnGetLocation = document.getElementById('btnGetLocation');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const addressField = document.getElementById('address');
    const locationStatus = document.getElementById('locationStatus');
    let watchId = null;

    btnGetLocation.addEventListener('click', () => {
        if (!navigator.geolocation) {
            locationStatus.textContent = 'Geolocation tidak didukung oleh browser ini.';
            locationStatus.classList.add('text-red-600');
            return;
        }

        locationStatus.textContent = 'Memulai live tracking lokasi...';
        locationStatus.classList.remove('text-red-600');

        // Jika sudah tracking, stop dulu
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }

        btnGetLocation.disabled = true;
        loadingSpinner.classList.remove('hidden');

        watchId = navigator.geolocation.watchPosition(
            async (position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                locationStatus.textContent = `Lokasi terbaru: Lat ${lat.toFixed(6)}, Lon ${lon.toFixed(6)}. Mengambil alamat...`;
                locationStatus.classList.remove('text-red-600');
                locationStatus.classList.remove('text-yellow-600');

                try {
                    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`;
                    const response = await fetch(url, {
                        headers: { 'Accept-Language': 'id' },
                        method: 'GET'
                    });

                    if (!response.ok) throw new Error('Gagal mengambil data alamat');

                    const data = await response.json();

                    if (data && data.display_name) {
                        addressField.value = data.display_name;
                        locationStatus.textContent = 'Alamat berhasil diperbarui.';
                        locationStatus.classList.remove('text-red-600');
                        locationStatus.classList.remove('text-yellow-600');
                    } else {
                        addressField.value = `${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                        locationStatus.textContent = 'Alamat tidak ditemukan, menampilkan koordinat.';
                        locationStatus.classList.add('text-yellow-600');
                    }
                } catch (err) {
                    addressField.value = `${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                    locationStatus.textContent = 'Gagal mengambil alamat, menampilkan koordinat.';
                    locationStatus.classList.add('text-red-600');
                } finally {
                    btnGetLocation.disabled = false;
                    loadingSpinner.classList.add('hidden');
                }
            },
            (error) => {
                locationStatus.textContent = `Gagal mengambil lokasi: ${error.message}`;
                locationStatus.classList.add('text-red-600');
                btnGetLocation.disabled = false;
                loadingSpinner.classList.add('hidden');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
</script>
@endsection
