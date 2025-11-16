<header class="fixed top-0 bg-gray-50 left-0 right-0 border-b border-gray-300 z-50">
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
                @auth
                    @if(auth()->user()->isStudent() || auth()->user()->isInvestor())
                        @php
                            $unreadCount = auth()->user()->notifications()->unread()->count();
                            $recentNotifications = auth()->user()->notifications()->recent()->limit(5)->get();
                        @endphp
                        <!-- Notification (Desktop Only) -->
                        <div x-data="{ notifOpen: false }" class="relative">
                            <button @click="notifOpen = !notifOpen" class="border-gray-300 flex gap-2 text-gray-600 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300 relative">
                                <i class="ri-notification-3-line"></i>
                                <!-- Notification badge -->
                                @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#b01116] text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadCount }}</span>
                                @endif
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
                                    @forelse($recentNotifications as $notification)
                                        @php
                                            $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                                        @endphp
                                        <a href="@if($notification->type === 'team_mention'){{ route('projects.show', $data['project_slug']) }}@else#@endif" 
                                           class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 {{ $notification->isUnread() ? 'bg-blue-50' : '' }}">
                                            <div class="flex gap-3">
                                                <div class="flex-shrink-0">
                                                    @if($notification->type === 'team_mention')
                                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                                        <i class="ri-team-line text-[#b01116]"></i>
                                                    </div>
                                                    @else
                                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <i class="ri-notification-line text-blue-600"></i>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900">{{ $data['leader_name'] ?? 'Notification' }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $data['message'] ?? '' }}</p>
                                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-gray-500">
                                            <i class="ri-notification-off-line text-2xl mb-2"></i>
                                            <p class="text-sm">Tidak ada notifikasi</p>
                                        </div>
                                    @endforelse
                                </div>
                                <!-- Footer -->
                                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                                    <a href="{{ route(auth()->user()->isStudent() ? 'student.notifications.index' : 'investor.notifications.index') }}" 
                                       class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Semua Notifikasi</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth

                <!-- Search (Desktop Only) -->
                <div x-data="{ searchOpen: false }" class="relative hidden lg:block">
                    <button @click="searchOpen = !searchOpen" class="border-gray-300 flex gap-2 text-gray-600 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300">
                        <i class="ri-search-line"></i>
                    </button>
                    <div x-show="searchOpen" 
                         x-transition
                         @click.away="searchOpen = false"
                         class="absolute z-50 right-0 top-full mt-2 w-96 bg-white border border-gray-300 rounded-lg shadow-xl overflow-hidden">
                        <!-- Search Header -->
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="ri-search-line text-[#b01116]"></i>
                                    Cari Proyek
                                </h3>
                                <button @click="searchOpen = false" class="text-gray-400 hover:text-gray-600">
                                    <i class="ri-close-line text-xl"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Search Form -->
                        <form action="" method="GET" autocomplete="off" class="p-4">
                            <div class="relative">
                                <input type="text" 
                                       name="q" 
                                       placeholder="Cari proyek, mahasiswa, atau kategori..." 
                                       x-data="{ value: '' }"
                                       x-model="value"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            
                            <!-- Quick Filters -->
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Filter Cepat</p>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-[#b01116] hover:text-white text-gray-700 rounded-full transition-colors">
                                        UI/UX Design
                                    </button>
                                    <button type="button" class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-[#b01116] hover:text-white text-gray-700 rounded-full transition-colors">
                                        Web Development
                                    </button>
                                    <button type="button" class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-[#b01116] hover:text-white text-gray-700 rounded-full transition-colors">
                                        Mobile App
                                    </button>
                                    <button type="button" class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-[#b01116] hover:text-white text-gray-700 rounded-full transition-colors">
                                        Data Science
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Recent Searches -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Pencarian Terkini</p>
                                <div class="space-y-2">
                                    <a href="#" class="flex items-center gap-2 text-sm text-gray-600 hover:text-[#b01116] hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                        <i class="ri-time-line text-gray-400"></i>
                                        <span>Manajemen Proyek</span>
                                    </a>
                                    <a href="#" class="flex items-center gap-2 text-sm text-gray-600 hover:text-[#b01116] hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                        <i class="ri-time-line text-gray-400"></i>
                                        <span>Aplikasi Kesehatan</span>
                                    </a>
                                    <a href="#" class="flex items-center gap-2 text-sm text-gray-600 hover:text-[#b01116] hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                        <i class="ri-time-line text-gray-400"></i>
                                        <span>E-commerce Platform</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Search Button -->
                            <button type="submit" class="w-full mt-4 bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                                <i class="ri-search-line"></i>
                                Cari Sekarang
                            </button>
                        </form>
                    </div>
                </div>
                
                @auth
                    @if(auth()->user()->isStudent() || auth()->user()->isInvestor())
                        <!-- Profile Dropdown -->
                        <div x-data="{ profileOpen: false }" class="relative">
                            <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 border-gray-300 text-gray-700 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->username }}" class="w-7 h-7 rounded-full object-cover">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-xs font-semibold">
                                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="hidden md:block">{{ auth()->user()->username }}</span>
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                            <div x-show="profileOpen" 
                                 x-transition 
                                 @click.away="profileOpen = false"
                                 class="absolute z-50 right-0 top-full mt-2 w-56 bg-white border border-gray-300 rounded-lg shadow-lg overflow-hidden">
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                                    <p class="font-semibold text-gray-900">{{ auth()->user()->username }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-2 px-2 py-0.5 text-xs font-medium rounded-full {{ auth()->user()->isStudent() ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ auth()->user()->isStudent() ? 'Student' : 'Investor' }}
                                    </span>
                                </div>
                                <!-- Menu Items -->
                                <div class="py-2">
                                    @if(auth()->user()->isStudent())
                                        <a href="{{ route('student.profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="ri-user-line text-gray-400"></i>
                                            <span>Profil Saya</span>
                                        </a>
                                    @else
                                        <a href="{{ route('investor.profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="ri-user-line text-gray-400"></i>
                                            <span>Profil Saya</span>
                                        </a>
                                    @endif
                                </div>
                                <!-- Logout -->
                                <div class="border-t border-gray-200">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="ri-logout-box-line"></i>
                                            <span>Keluar</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="flex gap-2 text-white bg-[#b01116] rounded-md font-medium px-3 py-1 hover:bg-[#8d0d11] transition-colors ease-in-out duration-300">
                        Login <i class="ri-arrow-right-double-fill"></i>
                    </a>
                    <a href="{{ route('register') }}" class="flex gap-2 rounded-md font-medium bg-pink-50 hover:bg-pink-100 text-[#b01116] border border-pink-200 px-3 py-1 transition-colors ease-in-out duration-300">
                        Register <i class="ri-arrow-right-double-fill"></i>
                    </a>
                @endauth
            </div>

            <!-- Desktop Search & Mobile Menu Button -->
            <div class="flex gap-3 items-center lg:hidden">
                @auth
                    @if(auth()->user()->isStudent() || auth()->user()->isInvestor())
                        <!-- Mobile Notification Button -->
                        <div x-data="{ notifOpen: false }" class="relative">
                            <button @click="notifOpen = !notifOpen" class="border-gray-300 flex gap-2 text-gray-600 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300 relative">
                                <i class="ri-notification-3-line"></i>
                                <!-- Notification badge -->
                                @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#b01116] text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadCount }}</span>
                                @endif
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
                                    @forelse($recentNotifications as $notification)
                                        @php
                                            $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                                        @endphp
                                        <a href="@if($notification->type === 'team_mention'){{ route('projects.show', $data['project_slug']) }}@else#@endif" 
                                           class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 {{ $notification->isUnread() ? 'bg-blue-50' : '' }}">
                                            <div class="flex gap-3">
                                                <div class="flex-shrink-0">
                                                    @if($notification->type === 'team_mention')
                                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                                        <i class="ri-team-line text-[#b01116]"></i>
                                                    </div>
                                                    @else
                                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <i class="ri-notification-line text-blue-600"></i>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900">{{ $data['leader_name'] ?? 'Notification' }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $data['message'] ?? '' }}</p>
                                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-gray-500">
                                            <i class="ri-notification-off-line text-2xl mb-2"></i>
                                            <p class="text-sm">Tidak ada notifikasi</p>
                                        </div>
                                    @endforelse
                                </div>
                                <!-- Footer -->
                                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                                    <a href="{{ route(auth()->user()->isStudent() ? 'student.notifications.index' : 'investor.notifications.index') }}" 
                                       class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Semua Notifikasi</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth

                <!-- Mobile Search Button -->
                <div x-data="{ searchOpen: false }" class="relative">
                    <button @click="searchOpen = !searchOpen" class="border-gray-300 flex gap-2 text-gray-600 rounded-md border font-medium px-3 py-1 hover:bg-gray-100 transition-colors ease-in-out duration-300">
                        <i class="ri-search-line"></i>
                    </button>
                    <div x-show="searchOpen" 
                         x-transition
                         @click.away="searchOpen = false"
                         class="fixed z-50 left-4 right-4 top-[72px] bg-white border border-gray-300 rounded-lg shadow-xl overflow-hidden max-h-[calc(100vh-88px)] overflow-y-auto">
                        <!-- Search Header -->
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 sticky top-0">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="ri-search-line text-[#b01116]"></i>
                                    Cari Proyek
                                </h3>
                                <button @click="searchOpen = false" class="text-gray-400 hover:text-gray-600">
                                    <i class="ri-close-line text-xl"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Search Form -->
                        <form action="" method="GET" autocomplete="off" class="p-4">
                            <div class="relative">
                                <input type="text" 
                                       name="q" 
                                       placeholder="Cari proyek, mahasiswa..." 
                                       x-data="{ value: '' }"
                                       x-model="value"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            
                            <!-- Quick Filters -->
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Filter Cepat</p>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-[#b01116] hover:text-white text-gray-700 rounded-full transition-colors">
                                        UI/UX Design
                                    </button>
                                    <button type="button" class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-[#b01116] hover:text-white text-gray-700 rounded-full transition-colors">
                                        Web Dev
                                    </button>
                                    <button type="button" class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-[#b01116] hover:text-white text-gray-700 rounded-full transition-colors">
                                        Mobile App
                                    </button>
                                    <button type="button" class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-[#b01116] hover:text-white text-gray-700 rounded-full transition-colors">
                                        Data Science
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Recent Searches -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Pencarian Terkini</p>
                                <div class="space-y-2">
                                    <a href="#" class="flex items-center gap-2 text-sm text-gray-600 hover:text-[#b01116] hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                        <i class="ri-time-line text-gray-400"></i>
                                        <span>Manajemen Proyek</span>
                                    </a>
                                    <a href="#" class="flex items-center gap-2 text-sm text-gray-600 hover:text-[#b01116] hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                        <i class="ri-time-line text-gray-400"></i>
                                        <span>Aplikasi Kesehatan</span>
                                    </a>
                                    <a href="#" class="flex items-center gap-2 text-sm text-gray-600 hover:text-[#b01116] hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                        <i class="ri-time-line text-gray-400"></i>
                                        <span>E-commerce Platform</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Search Button -->
                            <button type="submit" class="w-full mt-4 bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                                <i class="ri-search-line"></i>
                                Cari Sekarang
                            </button>
                        </form>
                    </div>
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

            <!-- Mobile Auth Buttons / Profile -->
            @auth
                @if(auth()->user()->isStudent() || auth()->user()->isInvestor())
                    <div class="px-4 py-3 border-t border-gray-300">
                        <!-- User Info -->
                        <div class="flex items-center gap-3 mb-3 pb-3 border-b border-gray-200">
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->username }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-lg font-semibold">
                                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ auth()->user()->username }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full {{ auth()->user()->isStudent() ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ auth()->user()->isStudent() ? 'Student' : 'Investor' }}
                                </span>
                            </div>
                        </div>
                        <!-- Menu Items -->
                        <div class="space-y-1">
                            @if(auth()->user()->isStudent())
                                <a href="{{ route('student.profile') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                                    <i class="ri-user-line text-gray-400"></i>
                                    <span>Profil Saya</span>
                                </a>
                            @else
                                <a href="{{ route('investor.profile') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                                    <i class="ri-user-line text-gray-400"></i>
                                    <span>Profil Saya</span>
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md transition-colors">
                                    <i class="ri-logout-box-line"></i>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @else
                <div class="px-4 py-3 border-t border-gray-300 flex gap-3 text-sm">
                    <a href="{{ route('login') }}" class="flex-1 text-center text-white bg-[#b01116] hover:bg-[#8d0d11] flex gap-2 items-center justify-center rounded-md border font-medium px-3 py-2 transition-colors ease-in-out duration-300">
                        Login <i class="ri-arrow-right-double-fill"></i>
                    </a>
                    <a href="{{ route('register') }}" class="flex-1 text-center border-pink-200 bg-pink-50 hover:bg-pink-100 text-[#b01116] flex gap-2 items-center justify-center rounded-md border font-medium px-3 py-2 transition-colors ease-in-out duration-300">
                        Register <i class="ri-arrow-right-double-fill"></i>
                    </a>
                </div>
            @endauth
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

    });
</script>