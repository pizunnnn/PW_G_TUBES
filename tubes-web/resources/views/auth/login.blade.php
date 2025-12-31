@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">
            <i class="fas fa-sign-in-alt text-purple-600"></i> Login
        </h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('email') border-red-500 @enderror"
                    required
                    autofocus
                >
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('password') border-red-500 @enderror"
                    required
                >
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-gray-700">Remember Me</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition"
            >
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <p class="text-center mt-6 text-gray-600">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-purple-600 hover:underline font-semibold">
                Daftar disini
            </a>
        </p>
    </div>
</div>
@endsection