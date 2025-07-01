@extends('layouts.user')

@section('title', 'Beranda Pacu App')

@section('content')

<!-- Carousel -->
<div
    x-data="carousel()"
    x-init="init()"
    class="relative w-full aspect-[2/1] sm:aspect-[3/1] md:aspect-[4/1] lg:h-[400px] mb-6 sm:mb-10 overflow-hidden rounded-xl shadow-xl"
>
    <!-- Slides -->
    <template x-for="(slide, index) in slides" :key="index">
        <div
            x-show="current === index"
            class="absolute inset-0 transition-opacity duration-700 ease-in-out"
            x-transition:enter="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="opacity-100"
            x-transition:leave-end="opacity-0"
            style="will-change: opacity;"
        >
            <img
                :src="slide.image"
                :alt="slide.title"
                class="w-full h-full object-cover cursor-pointer"
                @click="toggleDots()"
                @touchstart.stop.prevent="toggleDots()"
                style="
                    touch-action: pan-y;
                    -webkit-user-drag: auto; /* memungkinkan drag gambar di browser tertentu */
                    -webkit-touch-callout: none;
                    -webkit-user-select: none;
                    -webkit-tap-highlight-color: transparent;
                "
            />
            <!-- Gradient overlay for readability -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
            <!-- Title -->
            <div class="absolute inset-0 flex items-center justify-center px-6 text-center">
                <h1 class="text-white font-extrabold drop-shadow-lg text-2xl sm:text-4xl md:text-5xl leading-tight max-w-4xl" x-text="slide.title"></h1>
            </div>
        </div>
    </template>

    <!-- Prev Button -->
    <button
        @click="prev()"
        aria-label="Previous slide"
        class="absolute top-1/2 left-3 sm:left-6 -translate-y-1/2 bg-white bg-opacity-30 hover:bg-opacity-60 backdrop-blur rounded-full p-2 sm:p-3 text-[#0A2E6E] shadow-md transition"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    <!-- Next Button -->
    <button
        @click="next()"
        aria-label="Next slide"
        class="absolute top-1/2 right-3 sm:right-6 -translate-y-1/2 bg-white bg-opacity-30 hover:bg-opacity-60 backdrop-blur rounded-full p-2 sm:p-3 text-[#0A2E6E] shadow-md transition"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <!-- Dots Indicator, muncul/hide berdasarkan showDots -->
    <div
        class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-3"
        x-show="showDots"
        x-transition
    >
        <template x-for="(slide, index) in slides" :key="index">
            <button
                @click="goTo(index)"
                :class="current === index ? 'bg-[#0A2E6E] w-4 h-4' : 'bg-gray-400 w-3 h-3 hover:bg-[#0A2E6E] transition-colors'"
                class="rounded-full transition-colors focus:outline-none"
                aria-label="Slide indicator"
            ></button>
        </template>
    </div>
</div>

<!-- Produk Unggulan -->
<section class="px-2 sm:px-4 pb-24">
    <h2 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4 text-[#0A2E6E]">Produk Unggulan</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 sm:gap-4">
        @foreach ($products as $prod)
            @php
                $image = $prod->images->first();
                $imageUrl = $image ? asset('storage/' . $image->image_path) : 'https://via.placeholder.com/300x200?text=No+Image';
            @endphp
            <div class="relative bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 overflow-hidden">
                <a href="{{ route('detail-product', $prod->id) }}">
                    <img src="{{ $imageUrl }}" alt="{{ $prod->nama }}" class="w-full h-32 sm:h-36 md:h-44 object-cover group-hover:scale-105 transition-transform duration-300" />
                </a>
                <div class="p-3">
                    <h3 class="text-xs sm:text-sm font-medium text-gray-900 leading-tight line-clamp-2 min-h-[2.5rem]">{{ $prod->nama }}</h3>
                    <p class="text-[13px] sm:text-sm font-bold text-[#0A2E6E] mt-1">Rp {{ number_format($prod->harga, 0, ',', '.') }}</p>
                    <p class="text-[10px] sm:text-xs text-gray-500 truncate">{{ $prod->toko->nama ?? 'Toko tidak diketahui' }}</p>
                </div>

                <!-- Tombol Keranjang -->
                <form action="{{ route('cart.tambah') }}" method="POST" class="absolute bottom-3 right-3 z-10">
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
            </div>
        @endforeach
    </div>
    <div class="mt-5 sm:mt-6">
        {{ $products->links() }}
    </div>
</section>


@endsection

@push('scripts')
<script>
function carousel() {
    return {
        current: 0,
        showDots: true,
        slides: [
            { title: 'Flash Sale Pasar Kilat', image: 'http://travelio.com/blog/wp-content/uploads/2018/11/promo-diskon-sewa-apartemen-travelio.jpg' },
            { title: 'Promo Diskon Besar', image: 'https://s3-ap-southeast-1.amazonaws.com/paxelbucket/revamp/article-RUQX0FV-2GKACFG-VPPBDIU-G0LDLJY.jpg' },
            { title: 'Belanja Hemat', image: 'https://www.k24klik.com/blog/wp-content/uploads/2019/01/photo6224377029428619361.jpg' },
        ],
        init() {
            setInterval(() => this.next(), 5000);
        },
        next() {
            this.current = (this.current + 1) % this.slides.length;
        },
        prev() {
            this.current = (this.current - 1 + this.slides.length) % this.slides.length;
        },
        goTo(index) {
            this.current = index;
        },
        toggleDots() {
            this.showDots = !this.showDots;
        }
    }
}
</script>
@endpush
