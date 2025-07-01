<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard Admin')</title>
    <!-- Tailwind CSS CDN (atau dari build kamu) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden font-sans">

  <!-- Sidebar -->
  <aside class="bg-white w-64 flex flex-col shadow-lg">
      <div class="flex items-center justify-center h-16 border-b border-gray-200">
          <h1 class="text-2xl font-bold text-[#0A2E6E]">Pacu App</h1>
      </div>

      <nav class="flex flex-col flex-grow px-4 py-6 space-y-2 overflow-y-auto">

          @php
              $role = auth()->user()->role ?? ''; // Sesuaikan sesuai struktur role user
          @endphp

          @if($role === 'admin')
              <!-- Menu untuk admin -->
              <a href="{{ url('/admin') }}" class="px-4 py-2 rounded-lg hover:bg-[#c7d1f8] text-gray-700 font-medium
                  {{ request()->is('admin') ? 'bg-[#a2b1f5] text-[#0A2E6E] font-semibold' : '' }}">
                  Home
              </a>
              <a href="{{ url('/admin/users') }}" class="px-4 py-2 rounded-lg hover:bg-[#c7d1f8] text-gray-700 font-medium
                  {{ request()->is('admin/users*') ? 'bg-[#a2b1f5] text-[#0A2E6E] font-semibold' : '' }}">
                  Pengguna
              </a>
              <a href="{{ url('/toko/products') }}" class="px-4 py-2 rounded-lg hover:bg-[#c7d1f8] text-gray-700 font-medium
                  {{ request()->is('admin/stores*') ? 'bg-[#a2b1f5] text-[#0A2E6E] font-semibold' : '' }}">
                  Toko
              </a>
              <a href="{{ url('/orders') }}" class="px-4 py-2 rounded-lg hover:bg-[#c7d1f8] text-gray-700 font-medium
                          {{ request()->is('admin/orders*') ? 'bg-[#a2b1f5] text-[#0A2E6E] font-semibold' : '' }}">
                  Pesanan
              </a>
              <a href="{{ url('/payments') }}" class="px-4 py-2 rounded-lg hover:bg-[#c7d1f8] text-gray-700 font-medium
                          {{ request()->is('admin/payments*') ? 'bg-[#a2b1f5] text-[#0A2E6E] font-semibold' : '' }}">
                  Pembayaran
              </a>
              <a href="{{ url('admin/kurir') }}" class="px-4 py-2 rounded-lg hover:bg-[#c7d1f8] text-gray-700 font-medium
                          {{ request()->is('admin/kurir*') ? 'bg-[#a2b1f5] text-[#0A2E6E] font-semibold' : '' }}">
                  Kurir
              </a>
              <a href="{{ url('/admin/settings') }}" class="px-4 py-2 rounded-lg hover:bg-[#c7d1f8] text-gray-700 font-medium
                  {{ request()->is('admin/settings*') ? 'bg-[#a2b1f5] text-[#0A2E6E] font-semibold' : '' }}">
                  Pengaturan
              </a>
          @elseif($role === 'toko')
              <!-- Menu untuk toko -->
              <a href="{{ url('/toko/products') }}" class="px-4 py-2 rounded-lg hover:bg-[#c7d1f8] text-gray-700 font-medium
                  {{ request()->is('toko/products*') ? 'bg-[#a2b1f5] text-[#0A2E6E] font-semibold' : '' }}">
                  Pesanan
              </a>
              <a href="{{ url('/orders') }}" class="px-4 py-2 rounded-lg hover:bg-[#c7d1f8] text-gray-700 font-medium
                  {{ request()->is('/orders*') ? 'bg-[#a2b1f5] text-[#0A2E6E] font-semibold' : '' }}">
                  Kurir
              </a>
              <a href="{{ url('/payments') }}" class="px-4 py-2 rounded-lg hover:bg-[#c7d1f8] text-gray-700 font-medium
                  {{ request()->is('/pembayaran*') ? 'bg-[#a2b1f5] text-[#0A2E6E] font-semibold' : '' }}">
                  Pembayaran
              </a>
          @endif

      </nav>

      <div class="px-4 py-6 border-t border-gray-200">
          <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left text-red-600 font-semibold hover:text-red-800">Logout</button>
          </form>
      </div>
  </aside>

    {{-- Main content --}}
    <main class="flex-grow overflow-auto p-8">
        @yield('content')
    </main>

</body>
</html>
