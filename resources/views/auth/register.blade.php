@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100 py-8 px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-semibold text-center text-blue-900 mb-6">{{ __('Register') }}</h2>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block mb-2 text-gray-700 font-medium">{{ __('Name') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent @error('name') border-red-500 @enderror" />
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block mb-2 text-gray-700 font-medium">{{ __('Email Address') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent @error('email') border-red-500 @enderror" />
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block mb-2 text-gray-700 font-medium">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent @error('password') border-red-500 @enderror" />
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password-confirm" class="block mb-2 text-gray-700 font-medium">{{ __('Confirm Password') }}</label>
                <input id="password-confirm" type="password" name="password_confirmation" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" />
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role" class="block mb-2 text-gray-700 font-medium">Role</label>
                <select id="role" name="role" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent @error('role') border-red-500 @enderror">
                    <option value="">Pilih Role</option>
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="toko" {{ old('role') == 'toko' ? 'selected' : '' }}>Penjual</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Latitude -->
            <div class="mb-4">
                <label for="lat" class="block mb-2 text-gray-700 font-medium">Latitude</label>
                <input id="lat" type="text" name="lat" value="{{ old('lat') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" />
            </div>

            <!-- Longitude -->
            <div class="mb-4">
                <label for="long" class="block mb-2 text-gray-700 font-medium">Longitude</label>
                <input id="long" type="text" name="long" value="{{ old('long') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" />
            </div>

            <!-- Alamat (otomatis diisi) -->
            <div class="mb-4">
                <label for="alamat" class="block mb-2 text-gray-700 font-medium">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat akan otomatis terisi..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent">{{ old('alamat') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex flex-col space-y-3">
                <button type="submit"
                    class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                    {{ __('Register') }}
                </button>
                <a href="{{ route('login') }}"
                    class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-300">
                    {{ __('Already have an account? Login') }}
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Load fetch polyfill jika perlu (optional) -->
<!-- <script src="https://cdn.jsdelivr.net/npm/whatwg-fetch@3.6.2/dist/fetch.umd.min.js"></script> -->

<!-- Script otomatis ambil lokasi dan reverse geocode dari OpenStreetMap -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Cek support geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Isi field lat dan long
                    document.getElementById('lat').value = lat;
                    document.getElementById('long').value = lng;

                    // Panggil fungsi reverse geocode
                    getAddressFromCoords(lat, lng);
                },
                function(error) {
                    console.warn("Error geolocation:", error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            console.warn("Browser tidak mendukung Geolocation");
        }

        // Function reverse geocode dari Nominatim
        function getAddressFromCoords(lat, lng) {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('alamat').value = data.display_name;
                    } else {
                        console.warn("Alamat tidak ditemukan");
                    }
                })
                .catch(error => {
                    console.error("Error reverse geocoding:", error);
                });
        }
    });
</script>
@endsection
