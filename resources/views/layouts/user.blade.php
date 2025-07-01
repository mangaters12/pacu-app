<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title>@yield('title', 'Pacu App')</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Alpine.js -->
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <!-- Feather Icons -->
  <script src="https://unpkg.com/feather-icons"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <style>
    /* Override default feather icon size di mobile */
    @media (max-width: 640px) {
      i[data-feather] {
        width: 18px !important;
        height: 18px !important;
      }
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- Navbar -->
<nav class="bg-[#0A2E6E] shadow-md sticky top-0 z-50" x-data="{ cartOpen: false, mobileSearch: false }">
  <div class="max-w-screen-xl mx-auto px-4 flex items-center justify-between h-14 sm:h-16">

   <!-- Logo -->
   <a href="{{ url('/') }}" class="flex items-center space-x-2 min-w-[100px] sm:min-w-[140px]">
     <img src="{{ asset('storage/logonih.png') }}" alt="Logo" class="h-8 w-8 sm:h-10 sm:w-10 rounded-full object-cover" />
     <span class="text-lg sm:text-xl font-bold text-white truncate">Pacu App</span>
   </a>



    <!-- Search Desktop -->
    <form action="{{ route('home') }}" method="GET" class="hidden md:flex mx-2 flex-grow max-w-md min-w-0">
      <input name="search" value="{{ request('search') }}" type="search" placeholder="Cari produk, toko..."
        class="flex-grow rounded-l-md px-2 py-1 sm:px-3 sm:py-2 border focus:ring-[#0A2E6E] focus:border-[#0A2E6E] text-sm sm:text-base" />
      <button class="bg-[#0A2E6E] hover:bg-[#072147] px-3 sm:px-4 rounded-r-md text-white font-medium text-sm sm:text-base">Cari</button>
    </form>

    <!-- Ikon -->
    <div class="flex items-center space-x-2 sm:space-x-3 text-white text-sm sm:text-base min-w-[100px] justify-end">

      <!-- Search Toggle Mobile -->
      <button @click="mobileSearch = !mobileSearch" class="md:hidden p-1 rounded hover:bg-white hover:bg-opacity-20" aria-label="Toggle Search Mobile">
        <i data-feather="search" class="w-5 h-5 sm:w-6 sm:h-6"></i>
      </button>

      <!-- Chat -->
      <a href="#" title="Chat" class="hover:text-gray-300 transition hidden sm:inline-block p-1 rounded hover:bg-white hover:bg-opacity-20">
        <i data-feather="message-circle" class="w-4 h-4 sm:w-5 sm:h-5"></i>
      </a>

      <!-- Wishlist -->
      <a href="#" title="Wishlist" class="hover:text-gray-300 transition hidden sm:inline-block p-1 rounded hover:bg-white hover:bg-opacity-20">
        <i data-feather="heart" class="w-4 h-4 sm:w-5 sm:h-5"></i>
      </a>

      <!-- Cart -->
      <div class="relative" x-data="{ cartOpen: false }" @click.away="cartOpen = false">
        <button @click="cartOpen = !cartOpen" title="Keranjang" class="relative p-1 rounded hover:bg-white hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-white">
          <i data-feather="shopping-cart" class="w-5 h-5 sm:w-6 sm:h-6"></i>
          @if($cartItems->count() > 0)
            <span class="absolute -top-1.5 -right-2 bg-red-600 text-white text-xs rounded-full px-1.5 leading-none select-none">
              {{ $cartItems->sum('quantity') }}
            </span>
          @endif
        </button>

        <!-- Mini-Cart -->
        <div
          x-show="cartOpen"
          x-transition
          class="absolute right-0 mt-2 w-64 sm:w-72 bg-white shadow-lg rounded-lg z-50 overflow-hidden text-gray-700"
          style="display: none;"
        >
          <div class="p-3 text-sm sm:text-base max-h-64 overflow-y-auto">
            <p class="font-semibold mb-2 text-gray-800">Isi Keranjang</p>
            @if($cartItems->isEmpty())
              <p class="text-gray-500 text-sm">Keranjang kosong</p>
            @else
              <ul>
                @foreach($cartItems as $item)
                  <li class="flex justify-between items-start mb-2">
                    <div class="min-w-0">
                      <p class="font-medium truncate max-w-[150px] sm:max-w-xs">{{ $item->product->nama }}</p>
                      <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                    </div>
                    <span class="font-bold text-blue-700 whitespace-nowrap">
                      Rp {{ number_format($item->product->harga * $item->quantity, 0, ',', '.') }}
                    </span>
                  </li>
                @endforeach
              </ul>
              <a href="{{ route('cart') }}" class="block mt-3 text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 text-sm sm:text-base">Lihat Keranjang</a>
            @endif
          </div>
        </div>
      </div>

      <!-- Logout -->
      @auth
      <form method="POST" action="{{ route('logout') }}" class="ml-1">
        @csrf
        <button type="submit" class="bg-red-600 hover:bg-red-700 px-2 py-1 rounded text-white text-xs sm:text-sm font-semibold whitespace-nowrap">
          Logout
        </button>
      </form>
      @endauth
    </div>
  </div>

  <!-- Search Mobile Form -->
  <div x-show="mobileSearch" x-transition class="md:hidden bg-white p-2 border-t border-gray-300">
    <form action="{{ route('home') }}" method="GET" class="flex items-center space-x-1">
      <input name="search" value="{{ request('search') }}" type="search" placeholder="Cari produk, toko..."
        class="flex-grow border rounded px-2 py-1 text-sm" />
      <button class="bg-[#0A2E6E] text-white px-3 py-1 rounded text-sm font-medium">Cari</button>
    </form>
  </div>
</nav>

<!-- Main -->
<main class="flex-1 max-w-screen-xl mx-auto p-2 sm:p-4">
  @yield('content')
</main>

<!-- Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-md z-40 md:hidden">
  <div class="flex justify-around items-center text-sm text-gray-700 py-3">
    <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 {{ request()->is('home') ? 'text-[#0A2E6E] font-semibold' : '' }}">
      <i data-feather="home" class="w-6 h-6"></i>
      <span class="leading-none">Home</span>
    </a>
    <a href="{{ route('cart') }}" class="flex flex-col items-center gap-1 {{ request()->is('keranjang') ? 'text-[#0A2E6E] font-semibold' : '' }}">
      <i data-feather="shopping-cart" class="w-6 h-6"></i>
      <span class="leading-none">Keranjang</span>
    </a>
    <a href="{{ route('orders.user.index') }}" class="flex flex-col items-center gap-1 {{ request()->is('my-orders*') ? 'text-[#0A2E6E] font-semibold' : '' }}">
      <i data-feather="package" class="w-6 h-6"></i>
      <span class="leading-none">Pesanan</span>
    </a>
    <a href="{{ route('cart') }}" class="flex flex-col items-center gap-1 {{ request()->is('profile*') ? 'text-[#0A2E6E] font-semibold' : '' }}">
      <i data-feather="user" class="w-6 h-6"></i>
      <span class="leading-none">Akun</span>
    </a>
  </div>
</nav>


<!-- Feather Icons -->
<script>
  feather.replace();
</script>
@stack('scripts')

</body>
</html>
