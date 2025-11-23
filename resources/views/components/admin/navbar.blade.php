<header class="fixed top-0 left-0 right-0 bg-white border-b border-gray-300 z-50">
    <div class="mx-auto max-w-full">
        <div class="flex items-center justify-between px-4 py-3">
            <!-- Mobile Menu Button -->
            <button @click="$dispatch('toggle-sidebar')" class="lg:hidden text-gray-600 hover:text-gray-800 mr-3">
                <i class="ri-menu-line text-2xl"></i>
            </button>

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('storage/image.png') }}" alt="Logo" class="h-9 sm:h-11">
            </a>

            <!-- Right Section -->
            <div class="flex items-center gap-3">
                <!-- Notifications -->
                {{-- <div x-data="{ notifOpen: false }" class="relative">
                    <button @click="notifOpen = !notifOpen" class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="ri-notification-3-line text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-[#b01116] rounded-full"></span>
                    </button>
                    <!-- Desktop Notification -->
                    <div x-show="notifOpen" 
                         x-transition
                         @click.away="notifOpen = false"
                         class="hidden lg:block absolute right-0 top-full mt-2 w-80 bg-white border border-gray-300 rounded-lg shadow-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-900">Notifikasi</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">Proyek Baru Ditambahkan</p>
                                <p class="text-xs text-gray-500 mt-1">5 menit yang lalu</p>
                            </a>
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">Pelajar Mendaftar</p>
                                <p class="text-xs text-gray-500 mt-1">1 jam yang lalu</p>
                            </a>
                        </div>
                    </div>
                    <!-- Mobile Notification -->
                    <div x-show="notifOpen" 
                         x-transition
                         @click.away="notifOpen = false"
                         class="lg:hidden fixed left-4 right-4 top-[72px] w-auto bg-white border border-gray-300 rounded-lg shadow-xl overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-900">Notifikasi</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">Proyek Baru Ditambahkan</p>
                                <p class="text-xs text-gray-500 mt-1">5 menit yang lalu</p>
                            </a>
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">Pelajar Mendaftar</p>
                                <p class="text-xs text-gray-500 mt-1">1 jam yang lalu</p>
                            </a>
                        </div>
                    </div>
                </div> --}}

                <!-- User Menu -->
                <div x-data="{ userMenuOpen: false }" class="relative">
                    <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <div class="w-8 h-8 bg-[#b01116] rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">A</span>
                        </div>
                        <i class="ri-arrow-down-s-line text-gray-600"></i>
                    </button>
                    <div x-show="userMenuOpen" 
                         x-transition
                         @click.away="userMenuOpen = false"
                         class="absolute right-0 top-full mt-2 w-56 bg-white border border-gray-300 rounded-lg shadow-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <p class="font-semibold text-gray-900">Admin User</p>
                            <p class="text-xs text-gray-500">admin@example.com</p>
                        </div>
                        <div class="py-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50">
                                    <i class="ri-logout-box-line"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>