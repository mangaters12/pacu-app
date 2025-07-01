<!DOCTYPE html>
<html lang="id" >
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Pacu App - Kurir')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

<!-- Main Content -->
<main class="flex-1 container mx-auto px-4 py-4 pb-20"> <!-- Tambahkan pb-20 di sini -->
    @yield('content')
</main>

<!-- Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg z-50 rounded-t-lg">
    <div class="max-w-4xl mx-auto flex justify-around items-center py-3 px-4 space-x-4">
        <!-- Pendapatan -->
        <a href="{{ route('kurir.index') }}" class="flex flex-col items-center px-4 py-2 transition-transform hover:-translate-y-1 hover:text-[#0A2E6E] duration-200 ease-in-out">
            <div class="flex items-center justify-center w-12 h-12 bg-[#0A2E6E] text-white rounded-full shadow-lg mb-1">
                <i data-feather="dollar-sign" class="w-6 h-6"></i>
            </div>
            <span class="mt-1 text-sm font-semibold text-gray-700">Pendapatan</span>
        </a>

        <!-- On Bid Button -->
        <button onclick="aktifkanBid()" class="flex flex-col items-center px-4 py-2 transition-transform hover:-translate-y-1 hover:text-[#0A2E6E] duration-200 ease-in-out focus:outline-none">
            <div class="flex items-center justify-center w-12 h-12 bg-[#0A2E6E] text-white rounded-full shadow-lg mb-1">
                <i data-feather="plus-circle" class="w-6 h-6"></i>
            </div>
            <span class="mt-1 text-sm font-semibold text-gray-700">On Bid</span>
        </button>

        <!-- Hasil -->
        <a href="{{ route('kurir.index') }}" class="flex flex-col items-center px-4 py-2 transition-transform hover:-translate-y-1 hover:text-[#0A2E6E] duration-200 ease-in-out">
            <div class="flex items-center justify-center w-12 h-12 bg-[#0A2E6E] text-white rounded-full shadow-lg mb-1">
                <i data-feather="check-circle" class="w-6 h-6"></i>
            </div>
            <span class="mt-1 text-sm font-semibold text-gray-700">Hasil</span>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="flex flex-col items-center px-4 py-2" onsubmit="return confirm('Keluar dari aplikasi?')">
            @csrf
            <button type="submit" class="flex flex-col items-center focus:outline-none transition-transform hover:-translate-y-1 hover:text-red-600 duration-200 ease-in-out">
                <div class="flex items-center justify-center w-12 h-12 bg-red-600 text-white rounded-full shadow-lg mb-1">
                    <i data-feather="log-out" class="w-6 h-6"></i>
                </div>
                <span class="mt-1 text-sm font-semibold text-gray-700">Logout</span>
            </button>
        </form>
    </div>
</nav>

<!-- Feather Icons -->
<script>
    feather.replace();
</script>

</body>
</html>
