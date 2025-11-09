<header class="absolute top-0 bg-gray-50 left-0 right-0 border-b border-gray-300 z-50">
    <nav class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between px-4 py-3">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('storage/image.png') }}" alt="Logo" class="h-9 sm:h-11">
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden lg:flex nav-links gap-1">
                <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">Home</x-nav-link>
                <x-nav-link href="{{ route('project') }}" :active="request()->routeIs('project')">Project</x-nav-link>
                <x-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">Tentang Kami</x-nav-link>
                <x-nav-link href="{{ route('blog') }}" :active="request()->routeIs('blog')">Blog</x-nav-link>
                <x-nav-link href="{{ route('qa') }}" :active="request()->routeIs('qa')">Q&A</x-nav-link>
            </div>

            <!-- Desktop Auth Buttons -->
            <div class="hidden lg:flex gap-3 text-[0.9rem] items-center">
                <!-- Notification (Desktop Only) -->
                <div x-data="{ notifOpen: false }" class="relative">
                    <button @click="notifOpen = !notifOpen" class="border-gray-300 flex gap-2 text-gray-600 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300 relative">
                        <i class="ri-notification-3-line"></i>
                        <!-- Notification badge -->
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#b01116] text-white text-[10px] font-bold rounded-full flex items-center justify-center">3</span>
                    </button>
                    <div x-show="notifOpen" 
                         x-transition 
                         @click.away="notifOpen = false"
                         class="absolute z-50 right-0 top-full mt-2 w-80 bg-white border border-gray-300 rounded-md shadow-lg overflow-hidden">
                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                            <h3 class="font-semibold text-gray-900">Notifikasi</h3>
                        </div>
                        <!-- Notifications List -->
                        <div class="max-h-96 overflow-y-auto">
                            <!-- Notification Item 1 -->
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="ri-message-3-line text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Komentar Baru</p>
                                        <p class="text-xs text-gray-500 mt-1">Seseorang mengomentari proyek Anda</p>
                                        <p class="text-xs text-gray-400 mt-1">5 menit yang lalu</p>
                                    </div>
                                </div>
                            </a>
                            <!-- Notification Item 2 -->
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="ri-heart-line text-green-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Proyek Disukai</p>
                                        <p class="text-xs text-gray-500 mt-1">10 orang menyukai proyek Anda</p>
                                        <p class="text-xs text-gray-400 mt-1">2 jam yang lalu</p>
                                    </div>
                                </div>
                            </a>
                            <!-- Notification Item 3 -->
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="ri-user-add-line text-purple-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Pengikut Baru</p>
                                        <p class="text-xs text-gray-500 mt-1">3 orang mulai mengikuti Anda</p>
                                        <p class="text-xs text-gray-400 mt-1">1 hari yang lalu</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Footer -->
                        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                            <a href="#" class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </div>

                <!-- Search (Desktop Only) -->
                <div x-data="{ searchOpen: false }" class="relative hidden lg:block">
                    <button @click="searchOpen = !searchOpen" class="border-gray-300 flex gap-2 text-gray-600 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300">
                        <i class="ri-search-line"></i>
                    </button>
                    <form x-show="searchOpen" 
                          x-transition 
                          @click.away="searchOpen = false"
                          action="" 
                          method="GET" 
                          autocomplete="off"
                          class="absolute z-50 right-0 top-full mt-2 w-64 bg-white border border-gray-300 rounded-md shadow-lg p-2">
                        <input type="text" 
                               name="q" 
                               placeholder="Cari Proyek di sini..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#b01116] focus:border-[#b01116]">
                    </form>
                </div>
                <a href="{{ route('login') }}" class="border-gray-300 flex gap-2 text-gray-600 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300">
                    Login <i class="ri-arrow-right-double-fill"></i>
                </a>
                <a href="{{ route('register') }}" class="border-gray-300 flex gap-2 text-gray-600 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300">
                    Register <i class="ri-arrow-right-double-fill"></i>
                </a>
            </div>

            <!-- Desktop Search & Mobile Menu Button -->
            <div class="flex gap-3 items-center lg:hidden">
                <!-- Mobile Notification Button -->
                <div x-data="{ notifOpen: false }" class="relative">
                    <button @click="notifOpen = !notifOpen" class="border-gray-300 flex gap-2 text-gray-600 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300 relative">
                        <i class="ri-notification-3-line"></i>
                        <!-- Notification badge -->
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#b01116] text-white text-[10px] font-bold rounded-full flex items-center justify-center">3</span>
                    </button>
                    <div x-show="notifOpen" 
                         x-transition 
                         @click.away="notifOpen = false"
                         class="fixed z-50 left-4 right-4 top-[72px] bg-white border border-gray-300 rounded-md shadow-lg overflow-hidden">
                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                            <h3 class="font-semibold text-gray-900">Notifikasi</h3>
                        </div>
                        <!-- Notifications List -->
                        <div class="max-h-96 overflow-y-auto">
                            <!-- Notification Item 1 -->
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="ri-message-3-line text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Komentar Baru</p>
                                        <p class="text-xs text-gray-500 mt-1">Seseorang mengomentari proyek Anda</p>
                                        <p class="text-xs text-gray-400 mt-1">5 menit yang lalu</p>
                                    </div>
                                </div>
                            </a>
                            <!-- Notification Item 2 -->
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="ri-heart-line text-green-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Proyek Disukai</p>
                                        <p class="text-xs text-gray-500 mt-1">10 orang menyukai proyek Anda</p>
                                        <p class="text-xs text-gray-400 mt-1">2 jam yang lalu</p>
                                    </div>
                                </div>
                            </a>
                            <!-- Notification Item 3 -->
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <i class="ri-user-add-line text-purple-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Pengikut Baru</p>
                                        <p class="text-xs text-gray-500 mt-1">3 orang mulai mengikuti Anda</p>
                                        <p class="text-xs text-gray-400 mt-1">1 hari yang lalu</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Footer -->
                        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                            <a href="#" class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Search Button -->
                <div x-data="{ searchOpen: false }" class="relative">
                    <button @click="searchOpen = !searchOpen" class="border-gray-300 flex gap-2 text-gray-600 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300">
                        <i class="ri-search-line"></i>
                    </button>
                    <form x-show="searchOpen" 
                          x-transition 
                          @click.away="searchOpen = false"
                          action="" 
                          method="GET" 
                          autocomplete="off"
                          class="absolute z-50 right-0 top-full mt-2 w-64 bg-white border border-gray-300 rounded-md shadow-lg p-2">
                        <input type="text" 
                               name="q" 
                               placeholder="Cari Proyek di sini..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#b01116] focus:border-[#b01116]">
                    </form>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-toggle" class=" hamburger-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-300 bg-gray-50">
            <!-- Mobile Navigation Links -->
            <div class="px-4 py-3 space-y-1">
                <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')" class="block w-full text-left">Home</x-nav-link>
                <x-nav-link href="{{ route('project') }}" :active="request()->routeIs('project')" class="block w-full text-left">Project</x-nav-link>
                <x-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')" class="block w-full text-left">Tentang Kami</x-nav-link>
                <x-nav-link href="{{ route('blog') }}" :active="request()->routeIs('blog')" class="block w-full text-left">Blog</x-nav-link>
                <x-nav-link href="{{ route('qa') }}" :active="request()->routeIs('qa')" class="block w-full text-left">Q&A</x-nav-link>
            </div>

            <!-- Mobile Auth Buttons -->
            <div class="px-4 py-3 border-t border-gray-300 flex gap-3 text-sm">
                <a href="{{ route('login') }}" class="flex-1 text-center border-gray-300 flex gap-2 items-center justify-center text-gray-600 rounded-md border font-medium px-3 py-2 hover:bg-gray-100 transition-colors ease-in-out duration-300">
                    Login <i class="ri-arrow-right-double-fill"></i>
                </a>
                <a href="{{ route('register') }}" class="flex-1 text-center border-gray-300 flex gap-2 items-center justify-center text-gray-600 rounded-md border font-medium px-3 py-2 hover:bg-gray-100 transition-colors ease-in-out duration-300">
                    Register <i class="ri-arrow-right-double-fill"></i>
                </a>
            </div>
        </div>
    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('header');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuToggle && mobileMenu) {
            // Toggle mobile menu
            mobileMenuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                mobileMenuToggle.classList.toggle('active');
            });

            // Close mobile menu on window resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024 && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    mobileMenuToggle.classList.remove('active');
                }
            });
        }

        
        if (navbar) {
            // Show/hide button based on scroll position
            window.addEventListener('scroll', function() {
                if (window.scrollY > 0) {
                    navbar.classList.remove('absolute')
                    navbar.classList.add('fixed')
                } else {
                    navbar.classList.remove('fixed')
                    navbar.classList.add('absolute')
                }
            });
        }
    });
</script>