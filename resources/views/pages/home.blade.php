@extends('layout.layout')

@section("title", "Home")

@section("content")
<section id="hero" class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto text-center py-16 sm:py-20 md:py-24">
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-4 sm:mb-6">
            Galeri Karya Mahasiswa yang<br>
            <span class="text-[#b01116]">Inovatif dan Inspiratif</span>
        </h1>
        
        <p class="text-base sm:text-lg md:text-xl text-gray-600 leading-relaxed mb-8 sm:mb-10 max-w-3xl mx-auto px-4">
            Temukan berbagai proyek mahasiswa Telkom University dari desain,<br class="hidden sm:block">
            aplikasi, hingga penelitian yang menginspirasi. Dibuat untuk berbagi,<br class="hidden sm:block">
            belajar, dan memberi inspirasi bagi semua.
        </p>
        
        <a href="{{ route('project') }}" class="inline-flex items-center gap-2 bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold text-sm sm:text-base px-6 sm:px-8 py-3 sm:py-4 rounded-lg transition-all duration-300 ease-in-out shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            Jelajahi
            <i class="ri-arrow-right-line text-lg"></i>
        </a>
    </div>
</section>
<section id="popular_this_week" class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
            <!-- Left Column - Title and Description -->
            <div class="lg:w-1/3 space-y-6 text-center lg:text-left flex flex-col items-center lg:items-start">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#b01116] rounded-full">
                    <i class="ri-fire-fill text-white text-2xl"></i>
                </div>
                
                <div class="space-y-4">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                        Proyek Populer<br>Minggu ini
                    </h2>
                    
                    <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                        Ketahui semua Proyek yang sedang ramai pada beberapa minggu ini
                    </p>
                </div>
                
                <a href="{{ route('project') }}" class="inline-flex items-center gap-2 text-[#b01116] hover:text-[#8d0d11] font-semibold text-base transition-colors duration-200">
                    jelajahi lebih lanjut
                    <i class="ri-arrow-right-line text-lg"></i>
                </a>
            </div>
            
            <!-- Right Column - Project Cards -->
            <div class="lg:w-2/3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                    <div class="aspect-[4/3] relative bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex gap-2 flex-wrap">
                            <span class="px-4 py-1.5 bg-pink-50 text-pink-700 rounded-full text-xs font-medium">Analisis Data</span>
                            <span class="px-4 py-1.5 bg-teal-50 text-teal-700 rounded-full text-xs font-medium">Kelompok</span>
                        </div>
                        <p class="text-sm text-gray-400">10 desember 2024</p>
                        <h3 class="text-xl font-bold text-gray-900 leading-snug">HealthTrack — Pemantauan Kesehatan Berbasis AI</h3>
                        <div class="space-y-1 pt-2">
                            <p class="text-[#b01116] font-bold text-lg">InnovApp</p>
                            <p class="text-sm text-gray-400">Budi Pratama - 102042400019</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                    <div class="aspect-[4/3] relative bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex gap-2 flex-wrap">
                            <span class="px-4 py-1.5 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">Website Development</span>
                            <span class="px-4 py-1.5 bg-purple-50 text-purple-700 rounded-full text-xs font-medium">Individu</span>
                        </div>
                        <p class="text-sm text-gray-400">15 November 2024</p>
                        <h3 class="text-xl font-bold text-gray-900 leading-snug">EduTech Platform — Solusi Pembelajaran Online</h3>
                        <div class="space-y-1 pt-2">
                            <p class="text-[#b01116] font-bold text-lg">Akbar Setiawan</p>
                            <p class="text-sm text-gray-400">102042400018</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                    <div class="aspect-[4/3] relative bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex gap-2 flex-wrap">
                            <span class="px-4 py-1.5 bg-pink-50 text-pink-700 rounded-full text-xs font-medium">Analisis Data</span>
                            <span class="px-4 py-1.5 bg-teal-50 text-teal-700 rounded-full text-xs font-medium">Kelompok</span>
                        </div>
                        <p class="text-sm text-gray-400">20 februari 2025</p>
                        <h3 class="text-xl font-bold text-gray-900 leading-snug">MarketBoost — Pemasaran Digital Efektif</h3>
                        <div class="space-y-1 pt-2">
                            <p class="text-[#b01116] font-bold text-lg">PlayMakers</p>
                            <p class="text-sm text-gray-400">Andi Susanto - 102042400021</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="project" class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <!-- Header Text -->
        <div class="max-w-3xl mx-auto mb-12 text-center">
            <p class="text-lg sm:text-xl text-gray-700 leading-relaxed">
            Jelajahi berbagai karya mahasiswa, mulai dari <span class="text-[#b01116] font-semibold">desain kreatif</span>, 
            <span class="text-[#b01116] font-semibold">pemrograman</span>, hingga <span class="text-[#b01116] font-semibold">riset sistem informasi</span>.<br>
            Semua ide hebat ini lahir dari semangat belajar dan berinovasi.
            </p>
        </div>

        <!-- Filter Tabs and Search -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-8">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap gap-2">
                <button class="px-5 py-2 bg-[#b01116] text-white rounded-full text-sm font-medium hover:bg-[#8d0d11] transition-colors">All</button>
                <button class="px-5 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-200 transition-colors">Pengembangan Website</button>
                <button class="px-5 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-200 transition-colors">Design UI/UX</button>
                <button class="px-5 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-200 transition-colors">Business Model</button>
                <button class="px-5 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-200 transition-colors">Analisis Data</button>
                <button class="px-5 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-200 transition-colors">Sistem Informasi</button>
            </div>

            <!-- Search Button -->
            <form action="{{ route('project') }}" method="GET" class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:border-gray-400 transition-colors bg-white">
                <i class="ri-search-line text-lg text-gray-500"></i>
                <input type="text" autocomplete="off" name="search" placeholder="Cari Proyek Disini" class="text-sm text-gray-700 placeholder-gray-400 outline-none flex-1 bg-transparent" value="{{ request('search') }}">
                <button type="submit" class="sr-only">Search</button>
            </form>
        </div>

        <!-- Project Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Project Card 1 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div class="aspect-[4/3] relative bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-medium">Mobile App Development</span>
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">Individu</span>
                    </div>
                    <p class="text-xs text-gray-400">05 februari 2025</p>
                    <h3 class="text-base font-bold text-gray-900 leading-snug line-clamp-2">TravelMate — Aplikasi Perencanaan Perjalanan</h3>
                    <div class="space-y-1">
                        <p class="text-[#b01116] font-bold text-sm">Siti Nurhaliza</p>
                        <p class="text-xs text-gray-400">102042400020</p>
                    </div>
                    <a href="#" class="inline-flex items-center gap-1 text-[#b01116] hover:text-[#8d0d11] font-semibold text-sm transition-colors mt-2">
                        Pelajari Lebih Lanjut
                        <i class="ri-arrow-right-line text-base"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 2 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div class="aspect-[4/3] relative bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-teal-50 text-teal-700 rounded-full text-xs font-medium">UI/UX Design</span>
                        <span class="px-3 py-1 bg-teal-50 text-teal-700 rounded-full text-xs font-medium">Kelompok</span>
                    </div>
                    <p class="text-xs text-gray-400">Minggu Lalu - 02/01/24/2027</p>
                    <h3 class="text-base font-bold text-gray-900 leading-snug line-clamp-2">SamosaPiu Pintar — Sistem Manajemen Pengumpulan dan Olah Ulang Sampah</h3>
                    <div class="space-y-1">
                        <p class="text-[#b01116] font-bold text-sm">Mindscape</p>
                    </div>
                    <a href="#" class="inline-flex items-center gap-1 text-[#b01116] hover:text-[#8d0d11] font-semibold text-sm transition-colors mt-2">
                        Jelajah Sekarang
                        <i class="ri-arrow-right-line text-base"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 3 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div class="aspect-[4/3] relative bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">Website Development</span>
                        <span class="px-3 py-1 bg-teal-50 text-teal-700 rounded-full text-xs font-medium">Kelompok</span>
                    </div>
                    <p class="text-xs text-gray-400">05 februari 2025</p>
                    <h3 class="text-base font-bold text-gray-900 leading-snug line-clamp-2">ShopSmart — Platform E-commerce Terintegrasi</h3>
                    <div class="space-y-1">
                        <p class="text-[#b01116] font-bold text-sm">TechZone</p>
                        <p class="text-xs text-gray-400">Rina Rahmawati - 102042400016</p>
                    </div>
                    <a href="#" class="inline-flex items-center gap-1 text-[#b01116] hover:text-[#8d0d11] font-semibold text-sm transition-colors mt-2">
                        Pelajari Lebih Lanjut
                        <i class="ri-arrow-right-line text-base"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 4 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div class="aspect-[4/3] relative bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-teal-50 text-teal-700 rounded-full text-xs font-medium">UI/UX Design</span>
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">Individu</span>
                    </div>
                    <p class="text-xs text-gray-400">05 februari 2025</p>
                    <h3 class="text-base font-bold text-gray-900 leading-snug line-clamp-2">SamosaPiu Pintar — Sistem Manajemen Pengumpulan dan Olah Ulang Sampah</h3>
                    <div class="space-y-1">
                        <p class="text-[#b01116] font-bold text-sm">Kalingga Rafif</p>
                        <p class="text-xs text-gray-400">102042400001</p>
                    </div>
                    <a href="#" class="inline-flex items-center gap-1 text-[#b01116] hover:text-[#8d0d11] font-semibold text-sm transition-colors mt-2">
                        Jelajah Sekarang
                        <i class="ri-arrow-right-line text-base"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 5 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div class="aspect-[4/3] relative bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-pink-50 text-pink-700 rounded-full text-xs font-medium">Business Model</span>
                        <span class="px-3 py-1 bg-teal-50 text-teal-700 rounded-full text-xs font-medium">Kelompok</span>
                    </div>
                    <p class="text-xs text-gray-400">5 januari 30 April 2025</p>
                    <h3 class="text-base font-bold text-gray-900 leading-snug line-clamp-2">BrandBoost — Solusi Pemasaran Digital</h3>
                    <div class="space-y-1">
                        <p class="text-[#b01116] font-bold text-sm">AdVision</p>
                        <p class="text-xs text-gray-400">Siti Nurmaliza - 102042400009</p>
                    </div>
                    <a href="#" class="inline-flex items-center gap-1 text-[#b01116] hover:text-[#8d0d11] font-semibold text-sm transition-colors mt-2">
                        Gabung Dengan Kami
                        <i class="ri-arrow-right-line text-base"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 6 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div class="aspect-[4/3] relative bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-medium">Sistem Informasi</span>
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">Individu</span>
                    </div>
                    <p class="text-xs text-gray-400">05 Januari 2025</p>
                    <h3 class="text-base font-bold text-gray-900 leading-snug line-clamp-2">Fantasy Realm — Game Petualangan Interaktif</h3>
                    <div class="space-y-1">
                        <p class="text-[#b01116] font-bold text-sm">Budi Santoso</p>
                        <p class="text-xs text-gray-400">102042400020</p>
                    </div>
                    <a href="#" class="inline-flex items-center gap-1 text-[#b01116] hover:text-[#8d0d11] font-semibold text-sm transition-colors mt-2">
                        Mainkan Sekarang
                        <i class="ri-arrow-right-line text-base"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 7 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div class="aspect-[4/3] relative bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-pink-50 text-pink-700 rounded-full text-xs font-medium">Business Model</span>
                        <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-medium">Individu</span>
                    </div>
                    <p class="text-xs text-gray-400">20 Februari 2025</p>
                    <h3 class="text-base font-bold text-gray-900 leading-snug line-clamp-2">ShopSmart — Platform Belanja Cerdas</h3>
                    <div class="space-y-1">
                        <p class="text-[#b01116] font-bold text-sm">Rina Puspita</p>
                        <p class="text-xs text-gray-400">102042400021</p>
                    </div>
                    <a href="#" class="inline-flex items-center gap-1 text-[#b01116] hover:text-[#8d0d11] font-semibold text-sm transition-colors mt-2">
                        Kunjungi Toko
                        <i class="ri-arrow-right-line text-base"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 8 -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                <div class="aspect-[4/3] relative bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1555099962-4199c345e5dd?w=400&h=300&fit=crop" alt="Jakarta Building" class="w-full h-full object-cover">
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-medium">Sistem Informasi</span>
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">Individu</span>
                    </div>
                    <p class="text-xs text-gray-400">20 Januari 2025</p>
                    <h3 class="text-base font-bold text-gray-900 leading-snug line-clamp-2">FantasyQuest — Permainan Petualangan Interaktif</h3>
                    <div class="space-y-1">
                        <p class="text-[#b01116] font-bold text-sm">Siti Nurhaliza</p>
                        <p class="text-xs text-gray-400">102042400028</p>
                    </div>
                    <a href="#" class="inline-flex items-center gap-1 text-[#b01116] hover:text-[#8d0d11] font-semibold text-sm transition-colors mt-2">
                        Jelajah Selengkapnya
                        <i class="ri-arrow-right-line text-base"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection