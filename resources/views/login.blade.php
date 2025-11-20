@extends('layout.auth-layout')

@section('title', 'Login')

@section("content")
    <div class="w-full" id="login">
        <!-- Two-column scroll isolation: image static, form scrollable -->
        <div class="flex flex-col md:flex-row h-screen overflow-hidden">
            <!-- Image Section - Always visible; fixed height on small, full height on md+ -->
            <div class="w-full md:w-1/3 lg:w-2/5 relative h-44 sm:h-60 md:h-full shrink-0">
                <div class="absolute inset-0 bg-white/20"></div>
                <img class="object-cover w-full h-full" src="https://smktelkom-bdg.sch.id/wp-content/uploads/2025/07/Telkom-University-Kampus-Jakarta-scaled-1-1024x576.jpg" alt="Telkom University">
            </div>
            
            <!-- Form Section -->
            <div class="flex-1 overflow-y-auto px-5 sm:px-8 md:px-10 lg:px-16 py-8 sm:py-10 md:py-14 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-300">
                <button onclick="window.location.href='{{ route('home') }}'" class="flex items-center gap-2 cursor-pointer px-3 py-1.5 sm:py-2 border-gray-300 text-gray-600 rounded-md border hover:bg-gray-50 transition-colors">
                    <i class="ri-arrow-left-line"></i>
                    <span class="text-sm sm:text-base">Kembali</span>
                </button>
                
                <div class="min-h-[calc(100%-4rem)] flex flex-col py-5 justify-center max-w-md mx-auto my-8 sm:my-10 md:my-0">
                    <div class="mb-6 sm:mb-8 text-center space-y-2">
                        <img src="{{ asset('storage/image.png') }}" class="w-24 sm:w-28 md:w-32 mb-4 sm:mb-5 mx-auto" alt="Logo">
                        <h2 class="text-2xl sm:text-3xl md:text-3xl font-semibold leading-tight">
                            Selamat datang di <br class="hidden sm:block"> Telkom Project Gallery!
                        </h2>
                        <p class="text-sm sm:text-base text-gray-500">Jelajahi dan dapatkan inspirasi dari proyek pelajar terkini.</p>
                    </div>
                    
                    <form action="{{ route('login.post') }}" method="POST" class="space-y-4 sm:space-y-5">
                        @csrf
                        <h3 class="font-semibold text-xl">Login</h3>
                        
                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="border border-gray-300 text-gray-900 text-sm sm:text-base rounded-lg focus:ring-[#b01116] focus:border-[#b01116] focus:outline-0 block w-full p-2.5 sm:p-3" placeholder="Masukkan email Anda">
                        </div>
                        <div class="relative">
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                            <input type="password" name="password" id="password" required class="border pr-12 border-gray-300 text-gray-900 text-sm sm:text-base rounded-lg focus:ring-[#b01116] focus:border-[#b01116] focus:outline-0 block w-full p-2.5 sm:p-3" placeholder="Masukkan password Anda">
                            <i class="ri-eye-close-line absolute password-toggle right-3 top-1/2 bottom-1/2 -translate-y-1/2 text-gray-500 text-lg cursor-pointer"></i>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-[#b01116] bg-gray-100 border-gray-300 rounded focus:ring-[#b01116] focus:ring-2">
                                <label for="remember" class="ml-2 text-sm text-gray-900 cursor-pointer">Ingat saya</label>
                            </div>
                            {{-- <a href="{{ route('password.forgot') }}" class="text-sm text-[#b01116] hover:underline">Lupa password?</a> --}}
                        </div>
                        <button type="submit" disabled class="w-full cursor-not-allowed transition-all duration-200 ease-in-out submit-button-auth px-3 py-2.5 sm:py-3 rounded-lg text-sm sm:text-base font-medium text-gray-800 bg-gray-300">Masuk</button>
                        <div class="space-y-2">
                            <p class="text-center text-xs sm:text-sm text-gray-600 pt-2">
                                Belum punya akun? <a href="{{ route('register') }}" class="text-[#b01116] font-medium hover:underline">Daftar di sini</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection