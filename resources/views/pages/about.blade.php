@extends('layout.layout')

@section("title", "Tentang Kami")

@section("content")
<div class="bg-white">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 to-white">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-red-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-pink-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-red-50 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>
        
        <div class="max-w-4xl mx-auto text-center relative z-10 py-16">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-[#b01116] rounded-full mb-6">
                <i class="ri-information-line text-white text-4xl"></i>
            </div>
            
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                Tentang <span class="text-[#b01116]">Kami</span>
            </h1>
            
            <p class="text-lg sm:text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto">
                Platform untuk menampilkan, berbagi, dan menginspirasi melalui proyek-proyek 
                kreatif dan inovatif dari pelajar Telkom University
            </p>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Vision -->
                <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-8 sm:p-10 hover:shadow-xl transition-shadow duration-300">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-[#b01116] rounded-full mb-6">
                        <i class="ri-eye-line text-white text-3xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Visi Kami</h2>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        Menjadi platform terkemuka yang menghubungkan pelajar dengan dunia industri, 
                        menampilkan karya-karya inovatif, dan menciptakan ekosistem kolaboratif untuk 
                        mendorong kreativitas dan pengembangan talenta.
                    </p>
                </div>

                <!-- Mission -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-8 sm:p-10 hover:shadow-xl transition-shadow duration-300">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-[#b01116] rounded-full mb-6">
                        <i class="ri-rocket-line text-white text-3xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Misi Kami</h2>
                    <ul class="space-y-3 text-lg text-gray-700">
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line text-[#b01116] text-xl mt-1 flex-shrink-0"></i>
                            <span>Memberikan wadah untuk menampilkan portofolio karya pelajar</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line text-[#b01116] text-xl mt-1 flex-shrink-0"></i>
                            <span>Memfasilitasi koneksi antara pelajar dengan investor dan industri</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line text-[#b01116] text-xl mt-1 flex-shrink-0"></i>
                            <span>Mendorong inovasi dan kreativitas pelajar</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Apa yang Kami <span class="text-[#b01116]">Tawarkan</span>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Platform lengkap untuk menampilkan, menemukan, dan mengembangkan proyek pelajar
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-red-100 rounded-xl mb-5">
                        <i class="ri-gallery-line text-[#b01116] text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Galeri Proyek</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Tampilkan proyek Anda dengan berbagai media: gambar, video, dan dokumen pendukung 
                        dalam satu platform yang mudah diakses.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-red-100 rounded-xl mb-5">
                        <i class="ri-search-line text-[#b01116] text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pencarian Cerdas</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Temukan proyek berdasarkan kategori, keahlian, atau kata kunci dengan 
                        sistem pencarian yang canggih dan mudah digunakan.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-red-100 rounded-xl mb-5">
                        <i class="ri-user-star-line text-[#b01116] text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Profil Pelajar</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Bangun portofolio digital Anda dengan profil yang menampilkan semua 
                        proyek, keahlian, dan pencapaian akademik Anda.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-red-100 rounded-xl mb-5">
                        <i class="ri-heart-line text-[#b01116] text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Wishlist & Bookmark</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Investor dapat menyimpan dan mengelola proyek-proyek yang menarik 
                        perhatian mereka untuk kolaborasi di masa depan.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-red-100 rounded-xl mb-5">
                        <i class="ri-chat-3-line text-[#b01116] text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Komentar & Diskusi</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Berikan feedback, ajukan pertanyaan, dan berdiskusi langsung dengan 
                        pembuat proyek untuk kolaborasi yang lebih baik.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-red-100 rounded-xl mb-5">
                        <i class="ri-bar-chart-line text-[#b01116] text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Analytics & Insights</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Lacak performa proyek Anda dengan statistik views, engagement, dan 
                        popularitas untuk memahami dampak karya Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-[#b01116] to-[#8d0d11]">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4">
                    Kami dalam Angka
                </h2>
                <p class="text-lg text-red-100 max-w-2xl mx-auto">
                    Platform yang terus berkembang dengan komunitas yang aktif
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                        <i class="ri-folder-line text-white text-3xl"></i>
                    </div>
                    <div class="text-4xl sm:text-5xl font-bold text-white mb-2">{{ $stats['total_projects'] }}</div>
                    <div class="text-red-100 text-lg">Proyek Ditampilkan</div>
                </div>

                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                        <i class="ri-user-line text-white text-3xl"></i>
                    </div>
                    <div class="text-4xl sm:text-5xl font-bold text-white mb-2">{{ $stats['total_students'] }}</div>
                    <div class="text-red-100 text-lg">Pelajar Aktif</div>
                </div>

                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                        <i class="ri-building-line text-white text-3xl"></i>
                    </div>
                    <div class="text-4xl sm:text-5xl font-bold text-white mb-2">{{ $stats['total_investors'] }}</div>
                    <div class="text-red-100 text-lg">Investor Terdaftar</div>
                </div>

                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                        <i class="ri-bookmark-line text-white text-3xl"></i>
                    </div>
                    <div class="text-4xl sm:text-5xl font-bold text-white mb-2">{{ $stats['total_categories'] }}</div>
                    <div class="text-red-100 text-lg">Kategori Proyek</div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Bagaimana Cara <span class="text-[#b01116]">Kerjanya</span>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Langkah mudah untuk mulai menampilkan atau menemukan proyek
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
                <!-- Connection Lines (hidden on mobile) -->
                <div class="hidden md:block absolute top-20 left-1/6 right-1/6 h-1 bg-gradient-to-r from-[#b01116] via-red-400 to-[#b01116]" style="transform: translateY(-50%);"></div>

                <!-- Step 1 -->
                <div class="relative">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative z-10 inline-flex items-center justify-center w-20 h-20 bg-[#b01116] rounded-full mb-6 shadow-lg">
                            <span class="text-3xl font-bold text-white">1</span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-6 w-full">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Daftar & Login</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Buat akun sebagai pelajar atau investor untuk mengakses semua fitur platform kami.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative z-10 inline-flex items-center justify-center w-20 h-20 bg-[#b01116] rounded-full mb-6 shadow-lg">
                            <span class="text-3xl font-bold text-white">2</span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-6 w-full">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Upload atau Jelajah</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Pelajar dapat upload proyek, sementara investor dapat menjelajah dan mencari proyek menarik.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative z-10 inline-flex items-center justify-center w-20 h-20 bg-[#b01116] rounded-full mb-6 shadow-lg">
                            <span class="text-3xl font-bold text-white">3</span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-6 w-full">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Kolaborasi & Berkembang</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Bangun koneksi, dapatkan feedback, dan kembangkan proyek Anda dengan bantuan komunitas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 to-white">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                Siap untuk <span class="text-[#b01116]">Memulai?</span>
            </h2>
            <p class="text-lg sm:text-xl text-gray-600 mb-10 leading-relaxed">
                Bergabunglah dengan komunitas kami dan mulai menampilkan karya Anda atau 
                temukan talenta berbakat untuk proyek Anda selanjutnya.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('project') }}" 
                   class="inline-flex items-center gap-2 bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold px-8 py-4 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Jelajahi Proyek
                    <i class="ri-arrow-right-line text-lg"></i>
                </a>
                @guest
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-[#b01116] font-semibold px-8 py-4 rounded-lg border-2 border-[#b01116] transition-all duration-300 shadow-md hover:shadow-lg">
                    Daftar Sekarang
                    <i class="ri-user-add-line text-lg"></i>
                </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    {{-- <section class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Hubungi <span class="text-[#b01116]">Kami</span>
                </h2>
                <p class="text-lg text-gray-600">
                    Punya pertanyaan atau saran? Kami siap membantu Anda
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mb-4">
                        <i class="ri-mail-line text-[#b01116] text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Email</h3>
                    <p class="text-gray-600 text-sm">info@telkomuniversity.ac.id</p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mb-4">
                        <i class="ri-phone-line text-[#b01116] text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Telepon</h3>
                    <p class="text-gray-600 text-sm">+62 22 7566456</p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mb-4">
                        <i class="ri-map-pin-line text-[#b01116] text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Lokasi</h3>
                    <p class="text-gray-600 text-sm">Bandung, Jawa Barat</p>
                </div>
            </div>
        </div>
    </section> --}}
</div>

<style>
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>

@endsection
