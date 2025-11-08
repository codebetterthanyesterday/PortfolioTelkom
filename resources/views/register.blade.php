@extends('layout.auth-layout')

@section('title', 'Registrasi Akun')

@section("content")
    <div class="w-full" id="register">
        <!-- Two-column scroll isolation: image static, form scrollable -->
        <div class="flex flex-col md:flex-row h-screen overflow-hidden">
            <!-- Image Section - Always visible; fixed height on small, full height on md+ -->
            <div class="w-full md:w-1/3 lg:w-2/5 relative h-44 sm:h-60 md:h-full shrink-0">
                <div class="absolute inset-0 bg-white/20"></div>
                <img class="object-cover w-full h-full" src="https://smktelkom-bdg.sch.id/wp-content/uploads/2025/07/Telkom-University-Kampus-Jakarta-scaled-1-1024x576.jpg" alt="Telkom University">
            </div>
            
            <!-- Form Section -->
            <div class="flex-1 overflow-y-auto px-5 sm:px-8 md:px-10 lg:px-16 py-8 sm:py-10 md:py-14 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-300">
                <button onclick="window.location.href='{{ route('login') }}'" class="flex items-center gap-2 cursor-pointer px-3 py-1.5 sm:py-2 border-gray-300 text-gray-600 rounded-md border hover:bg-gray-50 transition-colors">
                    <i class="ri-arrow-left-line"></i>
                    <span class="text-sm sm:text-base">Ke Login</span>
                </button>
                
                <div class="min-h-[calc(100%-4rem)] flex flex-col py-5 justify-center max-w-md mx-auto my-6 sm:my-8 md:my-0">
                    <div class="mb-4 sm:mb-6 text-center space-y-2">
                        <img src="{{ asset('storage/image.png') }}" class="w-24 sm:w-28 md:w-32 mb-3 sm:mb-4 mx-auto" alt="Logo">
                        <h2 class="text-2xl sm:text-3xl md:text-3xl font-semibold leading-tight">
                            Selamat datang di <br class="hidden sm:block"> Telkom Project Gallery!
                        </h2>
                        <p class="text-sm sm:text-base text-gray-500">Jelajahi dan dapatkan inspirasi dari proyek siswa terkini.</p>
                    </div>
                    
                    <form action="" class="space-y-3 sm:space-y-4">
                        <h3 class="font-semibold text-xl">Registrasi Akun</h3>
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                            <input type="email" id="email" class="border border-gray-300 text-gray-900 text-sm sm:text-base rounded-lg focus:ring-[#b01116] focus:border-[#b01116] focus:outline-0 block w-full p-2.5 sm:p-3" placeholder="Masukkan email Anda">
                        </div>
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                            <input type="text" id="name" class="border border-gray-300 text-gray-900 text-sm sm:text-base rounded-lg focus:ring-[#b01116] focus:border-[#b01116] focus:outline-0 block w-full p-2.5 sm:p-3" placeholder="Masukkan nama Anda">
                        </div>
                        <div>
                            <label for="role" class="block mb-2 text-sm font-medium text-gray-900">Role</label>
                            <select id="role" class="border border-gray-300 text-gray-900 text-sm sm:text-base rounded-lg focus:ring-[#b01116] focus:border-[#b01116] focus:outline-0 block w-full p-2.5 sm:p-3">
                                <option selected disabled>Pilih Role</option>
                                <option value="student">Student</option>
                                <option value="investor">Investor</option>
                            </select>
                        </div>
                        <div class="relative">
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                            <input type="password" id="password" class="border border-gray-300 text-gray-900 text-sm sm:text-base rounded-lg focus:ring-[#b01116] focus:border-[#b01116] focus:outline-0 block w-full p-2.5 sm:p-3 pr-12" placeholder="Masukkan password Anda">
                            <i class="ri-eye-close-line absolute password-toggle right-3 top-1/2 bottom-1/2 -translate-y-1/2 text-gray-500 text-lg cursor-pointer"></i>
                        </div>
                        <div class="relative">
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" class="border border-gray-300 text-gray-900 text-sm sm:text-base rounded-lg focus:ring-[#b01116] focus:border-[#b01116] focus:outline-0 block w-full p-2.5 sm:p-3 pr-12" placeholder="Konfirmasi password Anda">
                            <i class="ri-eye-close-line absolute password-toggle right-3 top-1/2 bottom-1/2 -translate-y-1/2 text-gray-500 text-lg cursor-pointer"></i>
                        </div>
                        <div class="password-requirements text-xs sm:text-sm space-y-1">
                            <p class="req-length text-gray-500 flex items-start gap-1"><i class="ri-subtract-line mt-0.5"></i> <span>Harus lebih dari 8 karakter</span></p>
                            <p class="req-uppercase text-gray-500 flex items-start gap-1"><i class="ri-subtract-line mt-0.5"></i> <span>Minimal 1 huruf besar</span></p>
                            <p class="req-lowercase text-gray-500 flex items-start gap-1"><i class="ri-subtract-line mt-0.5"></i> <span>Minimal 1 huruf kecil</span></p>
                            <p class="req-number text-gray-500 flex items-start gap-1"><i class="ri-subtract-line mt-0.5"></i> <span>Minimal 1 angka</span></p>
                            <p class="req-match text-gray-500 flex items-start gap-1"><i class="ri-subtract-line mt-0.5"></i> <span>Konfirmasi password cocok</span></p>
                        </div>
                        <button type="submit" disabled class="w-full cursor-not-allowed submit-button-auth px-3 py-2.5 sm:py-3 rounded-lg text-sm sm:text-base font-medium text-gray-800 bg-gray-300">Registrasi Akun</button>
                        <p class="text-center text-xs sm:text-sm text-gray-600 pt-2">
                            Sudah punya akun? <a href="{{ route('login') }}" class="text-[#b01116] font-medium hover:underline">Login di sini</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection