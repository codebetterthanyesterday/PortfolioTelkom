@extends('layout.layout')

@section('title', "Detail Mahasiswa")

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Column (2 columns width) -->
        <div class="flex-1 lg:w-2/3 lg:order-1 order-2">
            <!-- Navigation Tabs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8" x-data="{ activeTab: 'projects' }">
                <!-- Tab Headers -->
                <div class="flex border-b border-gray-200 overflow-x-auto">
                    <button @click="activeTab = 'projects'" 
                            :class="activeTab === 'projects' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-folder-3-line mr-2"></i>Jelajahi Proyek
                    </button>
                    <button @click="activeTab = 'about'" 
                            :class="activeTab === 'about' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-user-line mr-2"></i>Tentang Mahasiswa
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Projects Tab -->
                    <div x-show="activeTab === 'projects'" x-transition>
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Jelajahi Proyek dari Mahasiswa</h2>
                            <p class="text-gray-600 text-sm">Total 6 proyek telah diselesaikan</p>
                        </div>

                        <!-- Projects Grid (3 columns) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Project Card 1 -->
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <div class="relative">
                                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Project" class="w-full h-48 object-cover">
                                    <span class="absolute top-3 left-3 px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full shadow">
                                        SEMESTER 5
                                    </span>
                                </div>
                                <div class="p-4">
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">UX/UI Design</span>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Kelompok</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2">14 oktober 2024</p>
                                    <h3 class="font-bold text-gray-800 text-sm mb-2 line-clamp-2">Samarku Pintar — Sistem Manajemen Pengumpulan dan Daur Ulang Sampah</h3>
                                    <p class="text-xs text-gray-600 mb-3">Kelompok 1234 | 1020242400018</p>
                                    <a href="#" class="text-[#b01116] hover:text-[#8d0d11] text-sm font-medium flex items-center gap-1">
                                        Edit Proyek <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Project Card 2 -->
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <div class="relative">
                                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Project" class="w-full h-48 object-cover">
                                    <span class="absolute top-3 left-3 px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full shadow">
                                        SEMESTER 5
                                    </span>
                                </div>
                                <div class="p-4">
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">Aplikasi Data</span>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Kelompok</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2">12 november 2024</p>
                                    <h3 class="font-bold text-gray-800 text-sm mb-2 line-clamp-2">HealthTrack — Pemantauan Kesehatan Berbasis AI</h3>
                                    <p class="text-xs text-gray-600 mb-3">Prof Indrawti | 1020242400019</p>
                                    <a href="#" class="text-[#b01116] hover:text-[#8d0d11] text-sm font-medium flex items-center gap-1">
                                        Edit Proyek <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Project Card 3 -->
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <div class="relative">
                                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Project" class="w-full h-48 object-cover">
                                    <span class="absolute top-3 left-3 px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full shadow">
                                        SEMESTER 6
                                    </span>
                                </div>
                                <div class="p-4">
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">Business Model</span>
                                        <span class="px-2 py-1 bg-pink-100 text-pink-700 text-xs font-semibold rounded-full">Individu</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2">5 januari 2025</p>
                                    <h3 class="font-bold text-gray-800 text-sm mb-2 line-clamp-2">EduTech Platform — Solusi Pembelajaran Online</h3>
                                    <p class="text-xs text-gray-600 mb-3">Kelompok 5678 | 1020242400020</p>
                                    <a href="#" class="text-[#b01116] hover:text-[#8d0d11] text-sm font-medium flex items-center gap-1">
                                        Edit Proyek <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Project Card 4 -->
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <div class="relative">
                                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Project" class="w-full h-48 object-cover">
                                    <span class="absolute top-3 left-3 px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full shadow">
                                        SEMESTER 4
                                    </span>
                                </div>
                                <div class="p-4">
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Website Development</span>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Kelompok</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2">20 februari 2025</p>
                                    <h3 class="font-bold text-gray-800 text-sm mb-2 line-clamp-2">BrandBoost — Sebuai Permasaran Digital</h3>
                                    <p class="text-xs text-gray-600 mb-3">Kelompok 9101 | 1020242400020</p>
                                    <a href="#" class="text-[#b01116] hover:text-[#8d0d11] text-sm font-medium flex items-center gap-1">
                                        Edit Proyek <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Project Card 5 -->
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <div class="relative">
                                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Project" class="w-full h-48 object-cover">
                                    <span class="absolute top-3 left-3 px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full shadow">
                                        SEMESTER 3
                                    </span>
                                </div>
                                <div class="p-4">
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="px-2 py-1 bg-teal-100 text-teal-700 text-xs font-semibold rounded-full">Mobile Development</span>
                                        <span class="px-2 py-1 bg-pink-100 text-pink-700 text-xs font-semibold rounded-full">Individu</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2">18 september 2025</p>
                                    <h3 class="font-bold text-gray-800 text-sm mb-2 line-clamp-2">EduTech Platform — Solusi Pembelajaran Online</h3>
                                    <p class="text-xs text-gray-600 mb-3">Anhar Setiawan | 1020242400021</p>
                                    <a href="#" class="text-[#b01116] hover:text-[#8d0d11] text-sm font-medium flex items-center gap-1">
                                        Edit Proyek <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Project Card 6 -->
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <div class="relative">
                                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Project" class="w-full h-48 object-cover">
                                    <span class="absolute top-3 left-3 px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full shadow">
                                        SEMESTER 5
                                    </span>
                                </div>
                                <div class="p-4">
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Fantasy Realm</span>
                                        <span class="px-2 py-1 bg-pink-100 text-pink-700 text-xs font-semibold rounded-full">Individu</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2">25 Februari 2025</p>
                                    <h3 class="font-bold text-gray-800 text-sm mb-2 line-clamp-2">Fantasy Realm — Game Petualangan Interaktif</h3>
                                    <p class="text-xs text-gray-600 mb-3">Budi Santoso | 1020242400022</p>
                                    <a href="#" class="text-[#b01116] hover:text-[#8d0d11] text-sm font-medium flex items-center gap-1">
                                        Edit Proyek <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- About Tab -->
                    <div x-show="activeTab === 'about'" x-transition>
                        <div class="prose prose-gray max-w-none">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Mahasiswa</h2>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                            </p>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.
                            </p>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Keahlian</h3>
                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">UI/UX Design</span>
                                <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-semibold rounded-full">Graphic Design</span>
                                <span class="px-4 py-2 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full">Frontend Development</span>
                                <span class="px-4 py-2 bg-orange-100 text-orange-700 text-sm font-semibold rounded-full">Product Design</span>
                            </div>

                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Pendidikan</h3>
                            <div class="space-y-4">
                                <div class="flex gap-4">
                                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center shrink-0">
                                        <i class="ri-graduation-cap-line text-gray-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800">Universitas Indonesia</h4>
                                        <p class="text-sm text-gray-600">S1 Sistem Informasi</p>
                                        <p class="text-xs text-gray-500">2021 - Sekarang</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Fixed Sidebar) -->
        <div class="lg:w-1/3 lg:order-2 order-1">
            <div class="lg:sticky lg:top-24">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <!-- Profile Picture -->
                    <div class="flex justify-center mb-6">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200">
                            <img src="{{ asset('images/jakarta.jpg') }}" alt="Andreas samsudin budiono siregar" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <!-- Student Name -->
                    <h1 class="text-2xl font-bold text-gray-800 text-center mb-2">Andreas samsudin budiono siregar</h1>
                    
                    <!-- Student ID -->
                    <p class="text-sm text-gray-500 text-center mb-4">NIM: 9786237678492</p>

                    <!-- About (100 char limit) -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-sm text-gray-600 text-center leading-relaxed">
                            UI UX & Graphic Designer | Student at www.idn.id or www.isn.sch.id
                        </p>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-gray-800 uppercase mb-4">Kontak Mahasiswa</h3>
                        
                        <!-- Email -->
                        <a href="mailto:dewi@example.com" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-mail-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm font-medium">dewi@example.com</p>
                            </div>
                        </a>

                        <!-- Phone -->
                        <a href="tel:+628123456789" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-phone-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Telepon</p>
                                <p class="text-sm font-medium">+62 812 3456 7890</p>
                            </div>
                        </a>

                        <!-- LinkedIn -->
                        <a href="#" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-linkedin-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">LinkedIn</p>
                                <p class="text-sm font-medium">/in/dewiratnasari</p>
                            </div>
                        </a>

                        <!-- GitHub -->
                        <a href="#" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-github-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">GitHub</p>
                                <p class="text-sm font-medium">@dewiratnasari</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection