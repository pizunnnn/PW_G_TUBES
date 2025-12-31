<!DOCTYPE html>
<html lang="id" x-data="darkMode()" x-bind:class="{ 'dark': isDark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ROCKETEER') - Voucher Game Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function darkMode() {
            return {
                isDark: localStorage.getItem('darkMode') === 'true',
                toggle() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('darkMode', this.isDark);
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300" x-data="{ mobileMenuOpen: false }">
    <!-- Navbar -->
    <nav class="gradient-bg shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="/" class="text-white text-2xl font-bold hover:scale-105 transition">
                        <i class="fas fa-rocket"></i> ROCKETEER
                    </a>

                    <!-- Navigation Links - Desktop -->
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('products.index') }}" class="text-white hover:text-gray-200 transition font-medium">
                            <i class="fas fa-store"></i> Products
                        </a>
                        @auth
                            <a href="{{ route('transactions.list') }}" class="text-white hover:text-gray-200 transition font-medium">
                                <i class="fas fa-receipt"></i> My Orders
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="flex items-center space-x-2 md:space-x-4">
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white p-2 rounded-lg hover:bg-white/10 transition">
                        <i x-show="!mobileMenuOpen" class="fas fa-bars text-xl"></i>
                        <i x-show="mobileMenuOpen" class="fas fa-times text-xl"></i>
                    </button>
                    <!-- Dark Mode Toggle -->
                    <button @click="toggle()" class="text-white hover:text-gray-200 transition p-2 rounded-lg hover:bg-white/10">
                        <i x-show="!isDark" class="fas fa-moon text-xl"></i>
                        <i x-show="isDark" class="fas fa-sun text-xl"></i>
                    </button>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="/admin/dashboard" class="text-white hover:text-gray-200 transition">
                                <i class="fas fa-gauge"></i> Admin
                            </a>
                        @endif
                        
                        <!-- Profile Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 text-white hover:text-gray-200 transition">
                                <i class="fas fa-user-circle text-2xl"></i>
                                <span class="hidden md:block font-medium">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2">
                                <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition">
                                    <i class="fas fa-user"></i> Profile
                                </a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 transition">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:inline-flex text-white hover:text-gray-200 transition font-medium">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="hidden md:inline-flex bg-white text-purple-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition font-semibold">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden border-t border-white/20">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-md text-white hover:bg-white/10 transition font-medium">
                        <i class="fas fa-store"></i> Products
                    </a>
                    @auth
                        <a href="{{ route('transactions.list') }}" class="block px-3 py-2 rounded-md text-white hover:bg-white/10 transition font-medium">
                            <i class="fas fa-receipt"></i> My Orders
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="/admin/dashboard" class="block px-3 py-2 rounded-md text-white hover:bg-white/10 transition font-medium">
                                <i class="fas fa-gauge"></i> Admin Dashboard
                            </a>
                        @endif
                        <a href="{{ route('profile.index') }}" class="block px-3 py-2 rounded-md text-white hover:bg-white/10 transition font-medium">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-red-200 hover:bg-white/10 transition font-medium">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-white hover:bg-white/10 transition font-medium">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md bg-white/20 text-white hover:bg-white/30 transition font-semibold">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded shadow-sm" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded shadow-sm" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-gray-950 text-white mt-20 py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4"><i class="fas fa-rocket"></i> ROCKETEER</h3>
                    <p class="text-gray-400">Platform jual beli voucher game terpercaya dengan harga terbaik.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('products.index') }}" class="hover:text-white transition">Products</a></li>
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">About Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <p class="text-gray-400">
                        <i class="fas fa-envelope"></i> support@antigravity.com<br>
                        <i class="fas fa-phone"></i> +62 123 456 7890
                    </p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 ROCKETEER Voucher Store. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>