@extends('layouts.kurir')

@section('title', 'Login Kurir')

@section('content')
<!-- Splash Screen -->
<div id="splash" class="fixed inset-0 bg-blue-600 flex items-center justify-center z-50">
    <h1 class="text-white text-4xl font-bold animate-pulse">PACU Driver</h1>
</div>

<!-- Background with image -->
<div class="relative min-h-screen bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1610449487462-5c8e5e8f3440?auto=format&fit=crop&w=1000&q=80');">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>

    <!-- Form Container -->
    <div class="relative z-10 flex items-center justify-center min-h-screen">
        <div class="bg-white bg-opacity-95 backdrop-blur-md p-6 rounded-xl shadow-lg w-full max-w-sm">
            <h1 class="text-3xl font-bold text-center mb-6 text-blue-700">Login Kurir</h1>

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('kurir.login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block font-medium mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <div>
                    <label for="password" class="block font-medium mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                    Login
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-gray-600">
                Belum punya akun?
                <a href="{{ route('kurir.registerForm') }}" class="text-blue-600 hover:underline">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>

<script>
    // Splash screen hilang setelah 3 detik
    setTimeout(() => {
        document.getElementById('splash').style.display = 'none';
    }, 1000);
</script>
@endsection
