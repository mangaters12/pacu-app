@extends('layouts.user')

@section('title', $product->nama ?? 'Detail Produk')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded-xl shadow-lg p-4 sm:p-6 md:p-10 pb-32 min-h-screen">

    <div class="flex flex-col md:flex-row gap-4 md:gap-6"> {{-- kurangin gap --}}

        {{-- Gambar Produk --}}
        <div class="md:w-1/2 flex flex-col">
            <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm mb-3 max-h-[280px] sm:max-h-[360px] md:max-h-[600px]"> {{-- kurangin mb --}}
                <img
                    id="mainImage"
                    src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : 'https://via.placeholder.com/600x600?text=No+Image' }}"
                    alt="{{ $product->nama }}"
                    class="w-full h-full object-contain bg-white"
                />
            </div>

            <div class="flex space-x-2 sm:space-x-3 overflow-x-auto no-scrollbar py-2">
                @foreach ($product->images as $img)
               <button
                   type="button"
                   class="flex-shrink-0 border border-gray-300 rounded-lg p-1 hover:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700 transition"
                   onclick="document.getElementById('mainImage').src='{{ asset('storage/' . $img->image_path) }}'"
                   aria-label="Thumbnail {{ $loop->iteration }}"
               >
                    <img
                      src="{{ asset('storage/' . $img->image_path) }}"
                      alt="Thumbnail"
                      class="w-14 sm:w-20 h-14 sm:h-20 object-cover rounded-lg"
                    />
                </button>
                @endforeach
            </div>
        </div>

        {{-- Detail Produk --}}
        <div class="md:w-1/2 flex flex-col justify-between">

            <div>
                <h1 class="text-xl sm:text-3xl md:text-5xl font-extrabold text-gray-900 leading-normal mb-2 tracking-tight truncate"> {{-- line-height normal, mb kecil --}}
                    {{ $product->nama }}
                </h1>

                <p class="text-xs sm:text-sm md:text-base text-gray-600 mb-1 leading-tight">
                    Toko: <span class="text-blue-700 font-semibold">{{ $product->toko->nama ?? 'Toko tidak diketahui' }}</span>
                </p>
                <p class="text-[9px] sm:text-xs md:text-sm text-gray-400 italic max-w-md mb-4 truncate leading-tight"> {{-- mb dikurangi --}}
                    {{ $product->toko->alamat ?? 'Alamat toko belum tersedia.' }}
                </p>

                <div class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-blue-700 mb-6 tracking-wide leading-tight"> {{-- mb dikurangi --}}
                    Rp {{ number_format($product->harga, 0, ',', '.') }}
                </div>

                <div class="flex space-x-6 text-gray-700 font-medium mb-6 text-xs sm:text-sm md:text-base leading-tight">
                    <div>Stok: <span class="font-semibold text-gray-900">{{ $product->stock ?? 25 }}</span></div>
                    <div>Terjual: <span class="font-semibold text-gray-900">{{ $product->sold ?? 150 }}</span></div>
                </div>

                <div class="prose prose-xs sm:prose-sm md:prose-base max-w-none text-gray-800 mb-6 leading-normal"> {{-- mb dikurangi, line-height normal --}}
                    {!! nl2br(e($product->deskripsi ?? "Deskripsi produk belum tersedia.")) !!}
                </div>

                <form method="POST" action="{{ route('cart.tambah') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button
                        type="submit"
                        class="w-full py-3 sm:py-4 md:py-5 bg-green-600 hover:bg-green-800 active:bg-gray-700 text-white text-lg sm:text-xl md:text-2xl font-bold text-center rounded-lg shadow-lg transition transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-600"
                    >
                        Tambah ke Keranjang
                    </button>
                </form>
<div class="mt-4">
    <a
        href="{{ route('checkout.single', $product->id) }}"
        class="block w-full py-3 sm:py-4 md:py-5 bg-blue-600 hover:bg-blue-800 active:bg-gray-700 text-white text-lg sm:text-xl md:text-2xl font-bold text-center rounded-lg shadow-lg transition transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-600"
    >
        Beli Sekarang
    </a>
</div>

            </div>

        </div>

    </div>

    {{-- Produk lain dari toko yang sama --}}
    @if($relatedProductsFromSameStore->count())
    <section class="mt-10"> {{-- kurangin mt --}}
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-semibold mb-4 border-b border-blue-700 pb-2">Produk Lain dari Toko Ini</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6"> {{-- kurangin gap --}}
            @foreach($relatedProductsFromSameStore as $prod)
                <div class="relative rounded-lg overflow-hidden border-2 border-blue-700 shadow hover:shadow-xl transition transform hover:scale-[1.05] bg-white">
                    <a href="{{ route('detail-product', $prod->id) }}" class="block">
                        <img
                            src="{{ $prod->images->first() ? asset('storage/' . $prod->images->first()->image_path) : 'https://via.placeholder.com/300?text=No+Image' }}"
                            alt="{{ $prod->nama }}"
                            class="w-full aspect-[4/3] object-cover"
                            loading="lazy"
                        />
                        <div class="p-3"> {{-- padding dikurangi --}}
                            <h3 class="text-base sm:text-lg font-semibold truncate leading-tight">{{ $prod->nama }}</h3>
                            <p class="text-gray-600 font-bold mt-1 text-base sm:text-lg leading-tight">Rp {{ number_format($prod->harga, 0, ',', '.') }}</p>
                        </div>
                    </a>
                    @auth
                    <form action="{{ route('cart.tambah') }}" method="POST" class="absolute bottom-3 right-3"> {{-- posisinya agak naik --}}
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $prod->id }}">
                        <button
                            type="submit"
                            aria-label="Tambah produk {{ $prod->nama }} ke keranjang"
                            class="bg-blue-600 hover:bg-blue-800 text-white rounded-full p-1.5 shadow-lg transition transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-gray-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 7M7 13l-1.5 6h13m-7-6v6" />
                            </svg>
                        </button>
                    </form>
                    @else
                   <a href="{{ route('login') }}" class="absolute bottom-3 right-3 bg-blue-600 rounded-full p-2 shadow flex items-center justify-center hover:bg-blue-800 transition">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 7M7 13l-1.5 6h13m-7-6v6" />
                       </svg>
                   </a>

                    @endauth
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Produk dari toko lain --}}
    @if($relatedProductsFromOtherStores->count())
    <section class="mt-10"> {{-- kurangin mt --}}
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-semibold mb-4 border-b border-gray-300 pb-2">Produk dari Toko Lain</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6"> {{-- kurangin gap --}}
            @foreach($relatedProductsFromOtherStores as $prod)
                <div class="relative rounded-lg overflow-hidden border border-gray-300 shadow hover:shadow-md transition transform hover:scale-[1.03] bg-white">
                    <a href="{{ route('detail-product', $prod->id) }}" class="block">
                        <img
                            src="{{ $prod->images->first() ? asset('storage/' . $prod->images->first()->image_path) : 'https://via.placeholder.com/300?text=No+Image' }}"
                            alt="{{ $prod->nama }}"
                            class="w-full aspect-[4/3] object-cover"
                            loading="lazy"
                        />
                        <div class="p-3"> {{-- padding dikurangi --}}
                            <h3 class="text-base sm:text-lg font-semibold truncate leading-tight">{{ $prod->nama }}</h3>
                            <p class="text-gray-600 text-sm sm:text-base mt-1 mb-1 truncate leading-tight">Toko: {{ $prod->toko->nama ?? 'Tidak diketahui' }}</p>
                            <p class="text-blue-700 font-bold text-base sm:text-lg leading-tight">Rp {{ number_format($prod->harga, 0, ',', '.') }}</p>
                        </div>
                    </a>
                    @auth
                    <form action="{{ route('cart.tambah') }}" method="POST" class="absolute bottom-3 right-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $prod->id }}">
                        <button
                            type="submit"
                            aria-label="Tambah produk {{ $prod->nama }} ke keranjang"
                            class="bg-blue-600 hover:bg-blue-800 text-white rounded-full p-1.5 shadow-lg transition transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-800"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 7M7 13l-1.5 6h13m-7-6v6" />
                            </svg>
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="absolute bottom-3 right-3 bg-blue-600 rounded-full p-2 shadow flex items-center justify-center hover:bg-blue-800 transition">
                                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 7M7 13l-1.5 6h13m-7-6v6" />
                                          </svg>
                                      </a>
                    @endauth
                </div>
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
