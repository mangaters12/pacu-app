@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100 py-8 px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-semibold text-center text-blue-900 mb-6">Login</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 mb-2 font-medium">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent @error('email') border-red-500 @enderror" />
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-gray-700 mb-2 font-medium">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent @error('password') border-red-500 @enderror" />
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-4 flex items-center">
                <input type="checkbox" id="remember" name="remember" class="mr-2 accent-blue-900" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="text-gray-700 text-sm">Remember Me</label>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col space-y-3">
                <button type="submit"
                    class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                    Login
                </button>
                <!-- Link Register -->
                <a href="{{ route('register') }}"
                    class="w-full text-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                    Register
                </a>
            </div>

            <!-- Forgot Password -->
            @if (Route::has('password.request'))
                <div class="mt-4 text-center">
                    <a href="{{ route('password.request') }}" class="text-blue-700 hover:underline text-sm">
                        Forgot Your Password?
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
