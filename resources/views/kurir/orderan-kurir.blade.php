@extends('layouts.kurir')

@section('title', 'Orderan Kurir')

@section('content')
<div class="min-h-screen bg-gray-50 flex justify-center py-10 px-4">
  <div class="w-full max-w-6xl bg-white rounded-3xl shadow-lg p-6 sm:p-8 space-y-6">

    <!-- Header -->
    <h1 class="text-3xl sm:text-4xl font-extrabold text-center text-blue-900 mb-4 tracking-widest">
      Daftar Order yang Bisa Diambil
    </h1>

    <!-- Icon lokasi mengambang -->
    <div class="fixed top-4 right-4 z-50">
      <button
        onclick="aktifkanBid()"
        class="p-3 bg-green-600 hover:bg-green-700 rounded-full shadow-lg focus:outline-none transition"
        aria-label="Tampilkan Lokasi Saya"
      >
        <i data-feather="map-pin" class="w-6 h-6 text-white"></i>
      </button>
    </div>

    <!-- Map -->
    <div id="map-kurir" class="w-full h-64 sm:h-96 rounded-xl border border-gray-300 shadow-md overflow-hidden hidden"></div>

    <!-- Notifikasi -->
    @foreach (['success', 'error', 'kurir_nama'] as $msg)
      @if(session($msg))
        @php
          $colors = ['success'=>'green', 'error'=>'red', 'kurir_nama'=>'blue'];
          $color = $colors[$msg] ?? 'gray';
        @endphp
        <div class="p-4 rounded-lg border-4 border-{{ $color }}-400 bg-{{ $color }}-100 text-{{ $color }}-800 font-semibold text-center shadow-md mb-4">
          @if($msg === 'kurir_nama')
            <span class="font-bold">Order diambil oleh:</span> {{ session('kurir_nama') }}
          @else
            {{ session($msg) }}
          @endif
        </div>
      @endif
    @endforeach

    <!-- Daftar Order -->
    @if($orders->isEmpty())
      <div class="text-center text-gray-500 mt-16">
        <p class="text-lg font-semibold">Tidak ada order saat ini.</p>
      </div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($orders as $order)
          @if($order->status == 'pending')
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 flex flex-col justify-between transition-transform hover:scale-105 duration-300">

              <!-- Header Order -->
              <div class="mb-2">
                <h2 class="text-lg font-semibold text-blue-900 mb-1 truncate">
                  Order #{{ $order->id }} — {{ ucfirst($order->status) }}
                </h2>
                <p class="text-sm text-gray-600"><strong>Nama:</strong> {{ $order->user->name ?? 'Unknown' }}</p>
                <p class="text-sm text-gray-600"><strong>Alamat:</strong> {{ $order->user->alamat ?? '-' }}</p>
              </div>

              <!-- Detail Order -->
              <div class="mb-2">
                <h3 class="text-sm font-semibold text-blue-900 mb-1">Detail Order:</h3>
                <ul class="list-disc list-inside max-h-24 overflow-y-auto text-gray-700 text-xs">
                  @foreach($order->orderDetails ?? [] as $detail)
                    <li>{{ $detail->product->nama ?? 'Produk tidak ditemukan' }} — Qty: {{ $detail->jumlah ?? '?' }}</li>
                  @endforeach
                </ul>
              </div>

              <!-- Jarak & Ongkir -->
              <div class="mb-2">
                <p class="text-xs font-semibold text-gray-600 distance-info"
                   data-alamat="{{ $order->user->alamat ?? '' }}"
                   data-lat="{{ $order->user->lat }}"
                   data-long="{{ $order->user->long }}"
                >Menghitung jarak...</p>
              </div>

              <!-- Button Google Maps -->
              @if ($order->user->lat && $order->user->long)
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->user->lat }},{{ $order->user->long }}" target="_blank" class="block w-full text-center mb-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-semibold shadow-md transition text-sm">
                  Buka Google Maps
                </a>
              @endif

              <!-- Tampilkan Route -->
              <button onclick="tampilkanRoute({{ $order->user->lat }}, {{ $order->user->long }})" class="block w-full text-center mb-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-full font-semibold shadow-md transition text-sm">
                Tampilkan Route
              </button>

              <!-- Button Ambil Order -->
              <form action="{{ route('kurir.orders.take', $order->id) }}" method="POST" class="mt-auto" onsubmit="return confirm('Ambil order ini?');">
                @csrf
                <button type="submit" class="w-full px-4 py-3 bg-green-500 hover:bg-green-600 rounded-full text-white font-semibold shadow-lg transition text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                  Ambil Order
                </button>
              </form>
            </div>
          @endif
        @endforeach
      </div>

      <!-- Pagination -->
      <div class="mt-8 flex justify-center">
        {{ $orders->links() }}
      </div>
    @endif

  </div>
</div>

{{-- Map & JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" crossorigin=""></script>
<script>
  const motorIcon = L.icon({
    iconUrl: 'https://www.svgrepo.com/show/137787/motorcycle.svg',
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -40]
  });
  let mapInstance = null;
  let kurirMarker = null;
  let kurirCoords = null;
  let routeLayer = null;

  // Fungsi hitung jarak
  function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = deg2rad(lat2 - lat1);
    const dLon = deg2rad(lon2 - lon1);
    const a = Math.sin(dLat/2)**2 + Math.cos(deg2rad(lat1))*Math.cos(deg2rad(lat2))*Math.sin(dLon/2)**2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  }
  function deg2rad(deg) {
    return deg * Math.PI/180;
  }

  // Geocode alamat
  async function geocodeAlamat(alamat) {
    if (!alamat) return null;
    try {
      const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(alamat)}&limit=1`);
      const data = await res.json();
      if (data.length > 0) {
        return { lat: parseFloat(data[0].lat), lon: parseFloat(data[0].lon) };
      }
    } catch (e) { console.error(e); }
    return null;
  }

  // update jarak & ongkir
  async function updateDistances() {
    document.querySelectorAll('.distance-info').forEach(async (el) => {
      const alamat = el.dataset.alamat;
      const lat = el.dataset.lat;
      const lon = el.dataset.long;
      if (lat && lon) {
        const jarak = getDistanceFromLatLonInKm(kurirCoords.lat, kurirCoords.lon, parseFloat(lat), parseFloat(lon));
        const ongkir = Math.ceil(jarak) * 2000;
        el.textContent = `Jarak: ${jarak.toFixed(2)} km — Ongkir Estimasi: Rp ${ongkir.toLocaleString('id-ID')}`;
      } else if (alamat) {
        const coords = await geocodeAlamat(alamat);
        if (coords) {
          el.dataset.lat = coords.lat;
          el.dataset.long = coords.lon;
          const jarak = getDistanceFromLatLonInKm(kurirCoords.lat, kurirCoords.lon, coords.lat, coords.lon);
          const ongkir = Math.ceil(jarak) * 2000;
          el.textContent = `Jarak: ${jarak.toFixed(2)} km — Ongkir Estimasi: Rp ${ongkir.toLocaleString('id-ID')}`;
        } else {
          el.textContent = "Gagal mendapatkan koordinat.";
        }
      } else {
        el.textContent = "Alamat tidak lengkap.";
      }
    });
  }

  // Dapatkan lokasi kurir
  document.addEventListener('DOMContentLoaded', () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((pos) => {
        kurirCoords = { lat: pos.coords.latitude, lon: pos.coords.longitude };
        updateDistances();
      });
    }
  });

  // Map aktifkan
  function aktifkanBid() {
    if (!navigator.geolocation) {
      alert('Browser tidak mendukung geolocation.');
      return;
    }
    navigator.geolocation.getCurrentPosition((pos) => {
      kurirCoords = { lat: pos.coords.latitude, lon: pos.coords.longitude };
      document.getElementById('map-kurir').style.display = 'block';
      if (mapInstance) mapInstance.remove();
      mapInstance = L.map('map-kurir').setView([kurirCoords.lat, kurirCoords.lon], 14);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
      }).addTo(mapInstance);
      if (kurirMarker) kurirMarker.remove();
      kurirMarker = L.marker([kurirCoords.lat, kurirCoords.lon], { icon: motorIcon }).addTo(mapInstance).bindPopup('Lokasi Kurir').openPopup();
    });
  }

  // Tampilkan route
  async function tampilkanRoute(lat, lon) {
    if (!mapInstance || !kurirCoords) return;
    if (routeLayer) mapInstance.removeLayer(routeLayer);
    routeLayer = L.polyline([[kurirCoords.lat, kurirCoords.lon], [lat, lon]], { color: 'blue' }).addTo(mapInstance);
    mapInstance.fitBounds(routeLayer.getBounds());
  }
</script>
@endsection
