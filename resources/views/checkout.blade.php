@extends('layouts.user')

@section('title', 'Checkout Keranjang')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-[120px]">

    <h1 class="text-3xl font-bold text-center text-[#0A2E6E] mb-10">Checkout Keranjang</h1>

    @if($cartItems->isEmpty())
        <div class="text-center text-red-600 font-semibold text-lg">Keranjang kamu masih kosong 😢</div>
    @else
        {{-- Tabel Produk --}}
        <div class="overflow-x-auto mb-6 border rounded-lg shadow-sm">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-[#0A2E6E] text-white uppercase text-xs font-semibold tracking-wide">
                        <th class="p-2 sm:p-3 text-left">Produk</th>
                        <th class="p-2 sm:p-3 text-center">Harga</th>
                        <th class="p-2 sm:p-3 text-center">Jumlah</th>
                        <th class="p-2 sm:p-3 text-center hidden sm:table-cell">Subtotal</th>
                        <th class="p-2 sm:p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2 sm:p-3 font-medium text-gray-800">{{ $item->product->nama }}</td>
                        <td class="p-2 sm:p-3 text-center">Rp {{ number_format($item->product->harga,0,',','.') }}</td>
                        <td class="p-2 sm:p-3 text-center">
                            <form method="POST" action="{{ route('cart.decrease', $item->id) }}" class="inline-block">
                                @csrf
                                <button type="submit" class="w-6 h-6 sm:w-7 sm:h-7 rounded bg-gray-200 hover:bg-gray-300 font-bold">−</button>
                            </form>
                            <span class="mx-2 sm:mx-3 font-semibold">{{ $item->quantity }}</span>
                            <form method="POST" action="{{ route('cart.increase', $item->id) }}" class="inline-block">
                                @csrf
                                <button type="submit" class="w-6 h-6 sm:w-7 sm:h-7 rounded bg-gray-200 hover:bg-gray-300 font-bold">+</button>
                            </form>
                        </td>
                        <td class="p-2 sm:p-3 text-center font-semibold text-gray-900 hidden sm:table-cell">Rp {{ number_format($item->product->harga * $item->quantity,0,',','.') }}</td>
                        <td class="p-2 sm:p-3 text-center">
                            <form method="POST" action="{{ route('cart.remove', $item->id) }}" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 font-semibold text-xs sm:text-sm">🗑️ Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Total Bayar --}}
        <div class="text-right text-lg sm:text-xl font-bold text-[#0A2E6E] mb-6">
            Total Bayar: Rp {{ number_format($total, 0, ',', '.') }}
        </div>


        {{-- Form Checkout --}}
        <form method="POST" action="{{ isset($product) ? route('checkout.process') : route('checkout.processAll') }}" enctype="multipart/form-data" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
            @csrf
            @if(isset($product))
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
            @endif

            <div>
                <label for="alamat" class="block mb-2 font-medium text-gray-700">Alamat Pengiriman</label>
                <textarea name="alamat" id="alamat" rows="4" placeholder="Masukkan alamat lengkap..." class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0A2E6E]">{{ old('alamat', $user->alamat ?? '') }}</textarea>
                @error('alamat')<p class="text-red-600 mt-1 text-sm">{{ $message }}</p>@enderror
            </div>

            <div>
                <button type="button" id="btnGetLocation" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold">
                    📍 Ambil Lokasi Saya
                </button>
                <span id="loadingSpinner" class="ml-3 hidden text-sm text-gray-500">⏳ Mengambil lokasi...</span>
            </div>

            <div>
                <label for="payment_method" class="block mt-6 mb-2 font-medium text-gray-700">Metode Pembayaran</label>
                <select name="payment_method" id="payment_method" required class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0A2E6E]">
                    <option value="" disabled selected>-- Pilih Metode --</option>
                    <option value="transfer_bank">🏦 Transfer Bank</option>
                    <option value="ovo">📱 OVO</option>
                    <option value="gopay">🚀 GoPay</option>
                    <option value="dana">💰 Dana</option>
                </select>
                @error('payment_method')<p class="text-red-600 mt-1 text-sm">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="payment_proof" class="block mt-6 mb-2 font-medium text-gray-700">Upload Bukti Bayar</label>
                <input type="file" name="payment_proof" id="payment_proof" accept="image/*" required class="w-full border rounded-lg px-4 py-2" />
                @error('payment_proof')<p class="text-red-600 mt-1 text-sm">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="w-full bg-[#0A2E6E] hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition">
                💳 Bayar Sekarang
            </button>
        </form>
    @endif
</div>

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 flex flex-col gap-3 z-50"></div>

<style>
  .toast {
    min-width: 250px;
    max-width: 320px;
    background-color: #333;
    color: white;
    padding: 12px 16px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgb(0 0 0 / 0.3);
    font-weight: 600;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    opacity: 0;
    transform: translateX(100%);
    transition: transform 0.3s ease, opacity 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .toast.show {
    opacity: 1;
    transform: translateX(0);
  }
  .toast.success { background-color: #22c55e; }
  .toast.error { background-color: #ef4444; }
  .toast.warning { background-color: #f59e0b; }
  .toast button.close-btn {
    background: transparent;
    border: none;
    color: white;
    font-weight: bold;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    margin-left: auto;
  }


</style>

<script>
  function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
      <span>${message}</span>
      <button class="close-btn" aria-label="Close">&times;</button>
    `;
    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 100);
    const hideTimeout = setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 3500);
    toast.querySelector('.close-btn').onclick = () => {
      clearTimeout(hideTimeout);
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    };
  }

  document.addEventListener('DOMContentLoaded', () => {
    @if(session('success'))
      showToast(`{{ session('success') }}`, 'success');
    @endif
    @if(session('error'))
      showToast(`{{ session('error') }}`, 'error');
    @endif

    const btnGetLocation = document.getElementById('btnGetLocation');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const addressField = document.getElementById('alamat');

    btnGetLocation.addEventListener('click', () => {
      if (!navigator.geolocation) {
        showToast('Geolocation tidak didukung oleh browser Anda.', 'error');
        return;
      }

      btnGetLocation.disabled = true;
      loadingSpinner.classList.remove('hidden');

      navigator.geolocation.getCurrentPosition(async (position) => {
        const { latitude, longitude } = position.coords;

        if (!latitude || !longitude) {
          showToast('Gagal mengambil koordinat lokasi.', 'error');
          btnGetLocation.disabled = false;
          loadingSpinner.classList.add('hidden');
          return;
        }

        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`;

        try {
          const response = await fetch(url);
          const data = await response.json();

          if (data && data.display_name) {
            addressField.value = data.display_name;
            showToast('Alamat berhasil diisi berdasarkan lokasi Anda.', 'success');
          } else {
            showToast('Gagal mendapatkan alamat dari lokasi Anda.', 'error');
          }
        } catch (error) {
          showToast('Terjadi kesalahan saat mengambil alamat.', 'error');
        } finally {
          btnGetLocation.disabled = false;
          loadingSpinner.classList.add('hidden');
        }
      }, (error) => {
        showToast('Gagal mendapatkan lokasi.', 'error');
        btnGetLocation.disabled = false;
        loadingSpinner.classList.add('hidden');
      }, {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0
      });
    });

    document.getElementById('payment_proof').addEventListener('change', function () {
      const file = this.files[0];
      if (file && file.size > 2 * 1024 * 1024) {
        showToast('Ukuran file maksimal 2MB.', 'warning');
        this.value = '';
      }
    });
  });
</script>
@endsection
