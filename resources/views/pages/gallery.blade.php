@extends('layout.layout')

@section('title', "Galeri Projek")

@section("content")
<section id="hero" class="min-h-[65vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-6xl mx-auto w-full py-16 sm:py-20 md:py-24">
        <!-- Badge -->
        <div class="flex justify-center mb-6">
            <span class="inline-flex items-center gap-2 bg-white px-4 py-2 rounded-full text-sm text-gray-600 border border-gray-200">
                <i class="ri-gallery-line"></i>
                Koleksi Projek yang Ada
            </span>
        </div>

        <!-- Main Heading -->
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 text-center leading-tight mb-6">
            Karya Inovatif Mahasiswa,<br>
            Satu Platform Serbabisa
        </h1>

        <!-- Description -->
        <p class="text-base sm:text-lg md:text-xl text-gray-600 text-center leading-relaxed mb-10 max-w-4xl mx-auto">
            Temukan proyek-proyek terbaik dari mahasiswa yang terus berinovasi<br class="hidden sm:block">
            di bidang teknologi, desain, dan bisnis digital.
        </p>

        <!-- Filter Buttons -->
        <div class="flex flex-wrap justify-center gap-3 mb-8">
            <button class="px-5 py-2 bg-[#b01116] text-white rounded-full text-sm font-medium hover:bg-[#8d0d11] transition-colors">
                All
            </button>
            <button class="px-5 py-2 bg-white text-gray-700 rounded-full text-sm font-medium border border-gray-200 hover:bg-gray-50 transition-colors">
                Pengembangan Website
            </button>
            <button class="px-5 py-2 bg-white text-gray-700 rounded-full text-sm font-medium border border-gray-200 hover:bg-gray-50 transition-colors">
                Design UI/UX
            </button>
        </div>

        <!-- Category Pills -->
        <div class="flex flex-wrap justify-center gap-3">
            <span class="px-4 py-2 bg-white text-gray-600 rounded-full text-sm border border-gray-200">Business Model</span>
            <span class="px-4 py-2 bg-white text-gray-600 rounded-full text-sm border border-gray-200">Analisis Data</span>
            <span class="px-4 py-2 bg-white text-gray-600 rounded-full text-sm border border-gray-200">Sistem Informasi</span>
        </div>
    </div>
</section>

<section id="gallery" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Project Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Project Card 1 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="TravelMate" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded-md">Website Dev</span>
                        <span class="text-xs px-2 py-1 bg-green-50 text-green-600 rounded-md">Keamanan</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">01 November 2024</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">TravelMate — Sistem Pemesanan Perjalanan</h3>
                    <p class="text-sm text-gray-600 mb-4">Siti Nurhaliza</p>
                    <p class="text-xs text-gray-400">XXXXXXXXXX</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Projek Lebih Lanjut
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 2 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Sarapanku" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded-md">UX/UI Design</span>
                        <span class="text-xs px-2 py-1 bg-purple-50 text-purple-600 rounded-md">Kulinerapp</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">03 November 2024</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">Sarapanku Pinter — Sistem Manajemen Pengumpulan dan Olah Ulang Sampah</h3>
                    <p class="text-sm text-gray-600 mb-4">Mindscape</p>
                    <p class="text-xs text-gray-400">441 orang Melihat (XXXXXXXXXX)</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        <span class="mr-1">Selanjutnya</span>
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 3 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Belanjain" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded-md">Website Development</span>
                        <span class="text-xs px-2 py-1 bg-purple-50 text-purple-600 rounded-md">Belanjapp</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">10 November 2024</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">Belanjain — Platform Ecommerce Terintegrasi</h3>
                    <p class="text-sm text-gray-600 mb-4">Sinta Kuprisi</p>
                    <p class="text-xs text-gray-400">XXXXXXXXXX</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Pelajari Lebih Lanjut
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 4 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Sarapanku Pinter" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded-md">UI/UX Design</span>
                        <span class="text-xs px-2 py-1 bg-purple-50 text-purple-600 rounded-md">Kebersik</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">07 November 2024</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">Sarapanku Pinter — Sistem Manajemen Pengumpulan dan Olah Ulang Sampah</h3>
                    <p class="text-sm text-gray-600 mb-4">Kalinga Refit</p>
                    <p class="text-xs text-gray-400">XXXXXXXXXX</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Pelajari Selengka
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 5 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="HealthTrack" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-red-50 text-red-600 rounded-md">Analisis Data</span>
                        <span class="text-xs px-2 py-1 bg-green-50 text-green-600 rounded-md">Kesempak</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">12 November 2024</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">HealthTrack — Pemantauan Kesehatan Berbasis AI</h3>
                    <p class="text-sm text-gray-600 mb-4">Siti Nurhaliza</p>
                    <p class="text-xs text-gray-400">XXXXXXXXXX</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Telusuri Selanjutnya
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 6 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Ikhoof" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-orange-50 text-orange-600 rounded-md">Business Model</span>
                        <span class="text-xs px-2 py-1 bg-green-50 text-green-600 rounded-md">Ketersekah</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">14 November 2024</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">Ikhoof—UI Solusi Pemasaran Digital Terbaik</h3>
                    <p class="text-sm text-gray-600 mb-4">AdVision</p>
                    <p class="text-xs text-gray-400">341 Terlihat, (XXXXXXXXXX)</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Gunting Dengan Kain
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 7 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Ikhoof Platform" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded-md">Website Development</span>
                        <span class="text-xs px-2 py-1 bg-purple-50 text-purple-600 rounded-md">Belanjak</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">17 November 2024</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">Ikhoof Platform — Solusi Pembayaran Omni</h3>
                    <p class="text-sm text-gray-600 mb-4">Akbar Setiawan</p>
                    <p class="text-xs text-gray-400">XXXXXXXXXX</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Eksplor Selengkapnya
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 8 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="AppMakers" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded-md">Website App Development</span>
                        <span class="text-xs px-2 py-1 bg-purple-50 text-purple-600 rounded-md">Aplikatorch</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">19 November 2024</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">AppMakers — Aplikasi Mobile Kreatif</h3>
                    <p class="text-sm text-gray-600 mb-4">AppVision</p>
                    <p class="text-xs text-gray-400">XXXXXXXXXX</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Lihat Dia Selengkapnya
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 9 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Fantasy Quest" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-purple-50 text-purple-600 rounded-md">Game Interaktif</span>
                        <span class="text-xs px-2 py-1 bg-purple-50 text-purple-600 rounded-md">Multitext</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">20 Januari 2018</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">Fantasy Quest — Game Petualangan Interaktif</h3>
                    <p class="text-sm text-gray-600 mb-4">Budi Santoso</p>
                    <p class="text-xs text-gray-400">XXXXXXXXX</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Mainkan Selengkapnya
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 10 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Rina Puspita" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-orange-50 text-orange-600 rounded-md">Business Model</span>
                        <span class="text-xs px-2 py-1 bg-purple-50 text-purple-600 rounded-md">Webtech</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">03 Februari 2018</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">Rina Puspita</h3>
                    <p class="text-sm text-gray-600 mb-4">XXXXXXXXX</p>
                    <p class="text-xs text-gray-400">XXXXXXXXX</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Kunjungi Toko
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 11 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="FantasyQuest" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-purple-50 text-purple-600 rounded-md">Sistem Informasi</span>
                        <span class="text-xs px-2 py-1 bg-purple-50 text-purple-600 rounded-md">Webtech</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">07 Desember 2024</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">FantasyQuest — Permainan Petualangan Interaktif</h3>
                    <p class="text-sm text-gray-600 mb-4">Siti Nurhaliza</p>
                    <p class="text-xs text-gray-400">XXXXXXXXXX</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Penjaga Selengkapnya
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Project Card 12 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="MarketBoost" class="w-full h-48 object-cover">
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">SEMESTER 5</span>
                </div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="text-xs px-2 py-1 bg-red-50 text-red-600 rounded-md">Analisis Data</span>
                        <span class="text-xs px-2 py-1 bg-green-50 text-green-600 rounded-md">Kesempak</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">07 Desember 2024</p>
                    <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">MarketBoost — Pemasaran Digital Efektif</h3>
                    <p class="text-sm text-gray-600 mb-4">PlayMakers</p>
                    <p class="text-xs text-gray-400">XXXXXXXXXX</p>
                    <a href="#" class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] mt-3">
                        Meninjau Selengkapnya
                        <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection