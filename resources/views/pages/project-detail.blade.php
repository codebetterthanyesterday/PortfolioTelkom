@extends('layout.layout')

@section('title', 'Project Detail')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Column (2 columns width) -->
        <div class="flex-1 lg:w-2/3">
            <!-- Product Preview with Carousel -->
            <div class="relative mb-8" x-data="{ currentSlide: 0, slides: 3 }">
                <!-- Blur Backdrop -->
                <div class="absolute inset-0 -z-10 blur-3xl opacity-30">
                    <img src="{{ asset('images/jakarta.jpg') }}" alt="Backdrop" class="w-full h-full object-cover rounded-2xl">
                </div>

                <!-- Main Carousel -->
                <div class="relative bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="relative h-[400px] sm:h-[500px] md:h-[600px]">
                        <!-- Slide 1 -->
                        <div x-show="currentSlide === 0" x-transition class="absolute inset-0">
                            <img src="{{ asset('images/jakarta.jpg') }}" alt="Product Image 1" class="w-full h-full object-contain p-4">
                        </div>
                        <!-- Slide 2 -->
                        <div x-show="currentSlide === 1" x-transition class="absolute inset-0">
                            <img src="{{ asset('images/jakarta.jpg') }}" alt="Product Image 2" class="w-full h-full object-contain p-4">
                        </div>
                        <!-- Slide 3 -->
                        <div x-show="currentSlide === 2" x-transition class="absolute inset-0">
                            <img src="{{ asset('images/jakarta.jpg') }}" alt="Product Image 3" class="w-full h-full object-contain p-4">
                        </div>

                        <!-- Navigation Arrows -->
                        <button @click="currentSlide = currentSlide > 0 ? currentSlide - 1 : slides - 1" 
                                class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 w-10 h-10 rounded-full shadow-lg flex items-center justify-center transition-all">
                            <i class="ri-arrow-left-s-line text-xl"></i>
                        </button>
                        <button @click="currentSlide = currentSlide < slides - 1 ? currentSlide + 1 : 0" 
                                class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 w-10 h-10 rounded-full shadow-lg flex items-center justify-center transition-all">
                            <i class="ri-arrow-right-s-line text-xl"></i>
                        </button>
                    </div>

                    <!-- Dots Indicator -->
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                        <template x-for="i in slides" :key="i">
                            <button @click="currentSlide = i - 1" 
                                    :class="currentSlide === i - 1 ? 'bg-[#b01116] w-8' : 'bg-gray-300 w-2'"
                                    class="h-2 rounded-full transition-all duration-300"></button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right Column (Mobile Only - appears after product preview) -->
            <div class="lg:hidden mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <!-- Badges -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Website Development</span>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Kelompok</span>
                    </div>

                    <!-- Project Title -->
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Panduan Pendidikan Antikorupsi - Kelas VI</h1>
                    
                    <!-- ISBN -->
                    <p class="text-sm text-gray-500 mb-4">ISBN: 9786237678492</p>

                    <!-- Price -->
                    <div class="mb-6">
                        <p class="text-3xl font-bold text-[#b01116]">Rp68.000</p>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="space-y-3">
                        <button class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                            <i class="ri-message-3-line"></i>
                            Chat Dengan Mahasiswa
                        </button>
                        <button class="w-full bg-pink-50 hover:bg-pink-100 text-[#b01116] font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2 border border-pink-200">
                            <i class="ri-bookmark-line"></i>
                            Simpan Proyek
                        </button>
                    </div>
                </div>
            </div>

            <!-- Project Description -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Deskripsi Proyek</h2>
                <div class="prose prose-gray max-w-none">
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Buku Belajar ini adalah buku panduan untuk belajar Bahasa Korea secara praktis! Buku ini memuat berbagai ungkapan Bahasa Korea yang sering digunakan dalam percakapan sehari-hari. Materi belajar disajikan dengan menarik dan kontekstual. Buku ini juga dilengkapi dengan unggapan Bahasa Korea Selatan, mau memperdalam Bahasa Korea atau yang baru belajar Bahasa Korea di tingkat awal, mau memperdalam Bahasa Korea atau yang baru belajar Bahasa Korea di tingkat awal.
                    </p>
                    <ol class="list-decimal list-inside space-y-2 text-gray-600">
                        <li>Tujuan belajar buku dan tata cara unggapan bahasa Korea Selatan.</li>
                        <li>Cara baca unggapan-unggapan bahasa Korea Selatan.</li>
                        <li>Terjemahan setiap kata demi kata belajar buku ini.</li>
                        <li>Audio sebagai model pencakapan yang dapat didengarkan melalui scan QR yang mudah diakses.</li>
                        <li>Pembelajaran disertakan dengan mudah dan sangat menarik sehingga-oleh peminat di Bahasa & Korea Selatan.</li>
                    </ol>
                </div>

                <!-- Course Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">MATAKULIAH</p>
                            <p class="font-semibold text-gray-800">Manajemen Proses Bisnis</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">DOSEN PENGAMPU</p>
                            <p class="font-semibold text-gray-800">Yumna Zahran Ramadhan, S.Kom., M.Kom.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Anggota Tim</h2>
                <div class="space-y-4 sm:space-y-6">
                    <!-- Member 1 -->
                    <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 pb-4 sm:pb-6 border-b border-gray-200">
                        <img src="{{ asset('images/jakarta.jpg') }}" alt="Dewi Ratna Sari" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover shrink-0">
                        <div class="flex-1 w-full">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-800 text-base sm:text-lg">Dewi Ratna Sari</h3>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">NIM: 102042400018 • Studi: SI Manajemen</p>
                                </div>
                                <a href="#" class="text-[#b01116] hover:text-[#8d0d11] font-medium text-xs sm:text-sm flex items-center gap-1 whitespace-nowrap">
                                    Lihat Profil <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3"><strong>Job Desc:</strong> UI/UX Designer</p>
                            <ul class="text-xs sm:text-sm text-gray-600 space-y-1">
                                <li>• Membuat prototype dan alur penggunaan Figma dan plugin-plugin pendukung lainnya</li>
                                <li>• Mengorganisasikan desain antarmuka pengguna dengan prinsip-prinsip desain yang baik</li>
                                <li>• Mengintegrasikan umpan balik pengguna untuk meningkatkan pengalaman pengguna</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Member 2 -->
                    <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 pb-4 sm:pb-6 border-b border-gray-200">
                        <img src="{{ asset('images/jakarta.jpg') }}" alt="Arif Setiawan" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover shrink-0">
                        <div class="flex-1 w-full">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-800 text-base sm:text-lg">Arif Setiawan</h3>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">NIM: 102042400019 • Studi: SI Informatika</p>
                                </div>
                                <a href="#" class="text-[#b01116] hover:text-[#8d0d11] font-medium text-xs sm:text-sm flex items-center gap-1 whitespace-nowrap">
                                    Lihat Profil <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3"><strong>Job Desc:</strong> Frontend Developer</p>
                            <ul class="text-xs sm:text-sm text-gray-600 space-y-1">
                                <li>• Mengembangkan aplikasi web responsif menggunakan HTML, CSS, dan JavaScript</li>
                                <li>• Berkolaborasi dengan desainer untuk mengubahkan antarmuka yang menarik</li>
                                <li>• Melakukan testing untuk memastikan fungsionalitas dan performa yang optimal</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Member 3 -->
                    <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 pb-4 sm:pb-6 border-b border-gray-200">
                        <img src="{{ asset('images/jakarta.jpg') }}" alt="Tina Mardiyah" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover shrink-0">
                        <div class="flex-1 w-full">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-800 text-base sm:text-lg">Tina Mardiyah</h3>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">NIM: 102042400020 • Studi: SI Sistem Informasi</p>
                                </div>
                                <a href="#" class="text-[#b01116] hover:text-[#8d0d11] font-medium text-xs sm:text-sm flex items-center gap-1 whitespace-nowrap">
                                    Lihat Profil <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3"><strong>Job Desc:</strong> Data Analyst</p>
                            <ul class="text-xs sm:text-sm text-gray-600 space-y-1">
                                <li>• Menganalisis data untuk mendukung keputusan bisnis</li>
                                <li>• Membuat dashboard dan visualisasi data menggunakan tools analitik</li>
                                <li>• Berkomunikasi dengan tim untuk memahami kebutuhan data</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Member 4 -->
                    <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4">
                        <img src="{{ asset('images/jakarta.jpg') }}" alt="Budi Santoso" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover shrink-0">
                        <div class="flex-1 w-full">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-800 text-base sm:text-lg">Budi Santoso</h3>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">NIM: 102042400021 • Studi: SI Teknik Komputer</p>
                                </div>
                                <a href="#" class="text-[#b01116] hover:text-[#8d0d11] font-medium text-xs sm:text-sm flex items-center gap-1 whitespace-nowrap">
                                    Lihat Profil <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3"><strong>Job Desc:</strong> Full Stack Developer</p>
                            <ul class="text-xs sm:text-sm text-gray-600 space-y-1">
                                <li>• Mengembangkan aplikasi dari sisi frontend dan backend</li>
                                <li>• Menerapkan best practices dalam pengembangan perangkat lunak</li>
                                <li>• Bekerja dengan tim untuk managing arsitektur aplikasi yang efisien</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Desktop Only - Fixed Sidebar) -->
        <div class="hidden lg:block lg:w-1/3">
            <div class="lg:sticky lg:top-24">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <!-- Badges -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Website Development</span>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Kelompok</span>
                    </div>

                    <!-- Project Title -->
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Panduan Pendidikan Antikorupsi - Kelas VI</h1>
                    
                    <!-- ISBN -->
                    <p class="text-sm text-gray-500 mb-4">ISBN: 9786237678492</p>

                    <!-- Price -->
                    <div class="mb-6">
                        <p class="text-3xl font-bold text-[#b01116]">Rp68.000</p>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="space-y-3">
                        <button class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                            <i class="ri-message-3-line"></i>
                            Chat Dengan Mahasiswa
                        </button>
                        <button class="w-full bg-pink-50 hover:bg-pink-100 text-[#b01116] font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2 border border-pink-200">
                            <i class="ri-bookmark-line"></i>
                            Simpan Proyek
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection