@extends('layouts.user')

@section('title', 'Keranjang')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-[120px]">
    <h1 class="text-3xl font-bold mb-8 flex items-center gap-3 text-[#0A2E6E]">
        <i data-feather="shopping-cart" class="w-7 h-7"></i> Keranjang Belanja
    </h1>

    @if($cartItems->isEmpty())
        <div class="bg-white shadow rounded-lg p-8 text-center text-gray-600 mx-auto max-w-md">
            <p class="text-xl mb-6">Keranjang kamu kosong.</p>
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-3 bg-[#0A2E6E] text-white px-6 py-3 rounded-md hover:bg-[#072147] transition font-semibold justify-center mx-auto">
                <i data-feather="arrow-left" class="w-5 h-5"></i> Belanja Sekarang
            </a>
        </div>
    @else
        @php $total = 0; @endphp

        <!-- VERSI MOBILE -->
        <div class="block md:hidden max-w-full space-y-6">
            <ul class="divide-y divide-gray-300 border border-gray-300 rounded-md">
                @foreach($cartItems as $item)
                    @php
                        $subtotal = $item->product->harga * $item->quantity;
                        $total += $subtotal;
                    @endphp
                    <li class="flex flex-wrap items-center gap-4 p-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold text-[#0A2E6E] truncate">{{ $item->product->nama }}</h3>
                            <p class="text-xs text-gray-600 mt-1">
                                Harga satuan: Rp {{ number_format($item->product->harga, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('cart.decrease', $item->id) }}">
                                @csrf
                                <button type="submit"
                                    class="w-7 h-7 bg-gray-200 rounded-md hover:bg-gray-300 font-bold">−</button>
                            </form>
                            <span class="font-semibold min-w-[20px] text-center">{{ $item->quantity }}</span>
                            <form method="POST" action="{{ route('cart.increase', $item->id) }}">
                                @csrf
                                <button type="submit"
                                    class="w-7 h-7 bg-gray-200 rounded-md hover:bg-gray-300 font-bold">+</button>
                            </form>
                        </div>

                        <div class="w-28 text-right font-bold text-blue-700 text-sm whitespace-nowrap">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>

                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini dari keranjang?')" class="flex-shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center transition"
                                aria-label="Hapus {{ $item->product->nama }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 7H5m14 0L17 19a2 2 0 01-2 2H9a2 2 0 01-2-2L7 7m3 4v6m4-6v6"></path>
                                </svg>
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4 max-w-md mx-auto px-0">
                <p class="text-lg font-bold text-center sm:text-left">
                    Total: <span class="text-blue-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </p>
                <a href="{{ route('checkout') }}"
                   class="inline-flex items-center gap-2 bg-[#0A2E6E] text-white px-6 py-3 rounded-md hover:bg-[#072147] transition font-semibold text-sm">
                    <i data-feather="credit-card" class="w-4 h-4"></i> Beli Sekarang
                </a>
            </div>
        </div>

        <!-- VERSI DESKTOP -->
        <div class="hidden md:flex max-w-full mx-auto px-6 gap-10">

           <!-- Daftar Produk dengan scroll jika terlalu panjang -->
           <div class="flex-1 max-h-[700px] overflow-y-auto border border-gray-300 rounded-md shadow-sm">
               <ul class="divide-y divide-gray-300">
                   @php $totalDesktop = 0; @endphp
                   @foreach($cartItems as $item)


                       <li class="grid grid-cols-[80px_1fr_80px_140px_50px] items-center gap-5 p-5 border-b last:border-none hover:bg-gray-50 transition">

                           <!-- Gabungkan nama, deskripsi, dan harga satuan di sini -->
                           <div class="min-w-0">
                               <h2 class="text-sm font-semibold text-[#0A2E6E] truncate">{{ $item->product->nama }}</h2>
                               @if(!empty($item->product->deskripsi))
                                   <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ $item->product->deskripsi }}</p>
                               @endif
                               <p class="text-gray-700 font-medium mt-2">
                                   Rp {{ number_format($item->product->harga, 0, ',', '.') }}
                               </p>
                           </div>

                           <div class="text-center bg-gray-100 rounded-md py-1 font-semibold text-gray-800 select-none">
                               {{ $item->quantity }}
                           </div>

                           <div class="text-center font-bold text-blue-700 whitespace-nowrap">
                               Rp {{ number_format($subtotal, 0, ',', '.') }}
                           </div>

                           <div class="flex justify-center">
                               <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                   @csrf
                                   @method('DELETE')
                                   <button type="submit"
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition"
                                           aria-label="Hapus {{ $item->product->nama }}">
                                       <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                           <path d="M19 7H5m14 0L17 19a2 2 0 01-2 2H9a2 2 0 01-2-2L7 7m3 4v6m4-6v6"></path>
                                       </svg>
                                   </button>
                               </form>
                           </div>
                       </li>
                   @endforeach
               </ul>
           </div>


            <!-- Sidebar Checkout tetap seperti sebelumnya -->
            <aside class="w-72 bg-gray-50 rounded-xl shadow-md p-6 flex flex-col justify-between sticky top-6 self-start">
                <h3 class="text-xl font-semibold mb-6 text-[#0A2E6E] text-center md:text-left">Checkout</h3>
                <p class="text-md font-semibold mb-6 text-center md:text-left">
                    Total Pembayaran:
                    <span class="text-blue-700 text-2xl block mt-2">Rp {{ number_format($totalDesktop, 0, ',', '.') }}</span>
                </p>
                <a href="{{ route('checkout') }}"
                   class="block bg-[#0A2E6E] hover:bg-[#072147] text-white py-3 rounded-md text-center font-semibold transition mb-2">
                    Lanjut ke Pembayaran
                </a>
            </aside>
        </div>

    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.feather) {
            window.feather.replace();
        }
    });
</script>
@endsection
