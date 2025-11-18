@extends('layout.auth-layout')

@section('title', 'Reset Password')

@section('content')
    <div class="w-full" id="recovery">
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
                
                <div class="min-h-[calc(100%-4rem)] flex flex-col py-5 justify-center max-w-md mx-auto my-8 sm:my-10 md:my-0">
                    <div class="mb-6 sm:mb-8 text-center space-y-2">
                        <img src="{{ asset('storage/image.png') }}" class="w-24 sm:w-28 md:w-32 mb-4 sm:mb-5 mx-auto" alt="Logo">
                    </div>
                    
                    <form action="{{ route('password.update') }}" method="POST" class="space-y-4 sm:space-y-5">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <h3 class="font-semibold text-xl">Reset Password</h3>
                        
                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required class="border border-gray-300 text-gray-900 text-sm sm:text-base rounded-lg focus:ring-[#b01116] focus:border-[#b01116] focus:outline-0 block w-full p-2.5 sm:p-3" placeholder="Masukkan email Anda" readonly>
                        </div>
                        
                        <div class="relative">
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password Baru</label>
                            <input type="password" name="password" id="password" required class="border border-gray-300 text-gray-900 text-sm sm:text-base rounded-lg focus:ring-[#b01116] focus:border-[#b01116] focus:outline-0 block w-full p-2.5 sm:p-3 pr-12" placeholder="Masukkan password baru">
                            <i class="ri-eye-close-line absolute password-toggle right-3 top-1/2 bottom-1/2 -translate-y-1/2 text-gray-500 text-lg cursor-pointer"></i>
                        </div>
                        
                        <div class="relative">
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="border border-gray-300 text-gray-900 text-sm sm:text-base rounded-lg focus:ring-[#b01116] focus:border-[#b01116] focus:outline-0 block w-full p-2.5 sm:p-3 pr-12" placeholder="Konfirmasi password baru">
                            <i class="ri-eye-close-line absolute password-toggle right-3 top-1/2 bottom-1/2 -translate-y-1/2 text-gray-500 text-lg cursor-pointer"></i>
                        </div>
                        
                        <div class="password-requirements text-xs sm:text-sm space-y-1">
                            <p class="req-length text-gray-500 flex items-start gap-1"><i class="ri-subtract-line mt-0.5"></i> <span>Harus lebih dari 8 karakter</span></p>
                            <p class="req-uppercase text-gray-500 flex items-start gap-1"><i class="ri-subtract-line mt-0.5"></i> <span>Minimal 1 huruf besar</span></p>
                            <p class="req-lowercase text-gray-500 flex items-start gap-1"><i class="ri-subtract-line mt-0.5"></i> <span>Minimal 1 huruf kecil</span></p>
                            <p class="req-number text-gray-500 flex items-start gap-1"><i class="ri-subtract-line mt-0.5"></i> <span>Minimal 1 angka</span></p>
                            <p class="req-match text-gray-500 flex items-start gap-1"><i class="ri-subtract-line mt-0.5"></i> <span>Konfirmasi password cocok</span></p>
                        </div>
                        
                        <button type="submit" disabled class="w-full cursor-not-allowed transition-all duration-200 ease-in-out submit-button-auth px-3 py-2.5 sm:py-3 rounded-lg text-sm sm:text-base font-medium text-gray-800 bg-gray-300">Reset Password</button>
                        
                        <p class="text-center text-xs sm:text-sm text-gray-600 pt-2">
                            Ingat password anda? <a href="{{ route('login') }}" class="text-[#b01116] font-medium hover:underline">Login di sini</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
