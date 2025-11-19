@extends('layout.layout')

@section('title', "Detail Investor - " . ($investor->user->full_name ?? $investor->user->username))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ activeTab: 'wishlist' }">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Column (2 columns width) -->
        <div class="flex-1 lg:w-2/3 lg:order-1 order-2">
            <!-- Navigation Tabs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                <!-- Tab Headers -->
                <div class="flex border-b border-gray-200 overflow-x-auto">
                    <button @click="activeTab = 'wishlist'" 
                            :class="activeTab === 'wishlist' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-heart-line mr-2"></i>Wishlist Proyek
                    </button>
                    <button @click="activeTab = 'about'" 
                            :class="activeTab === 'about' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-user-line mr-2"></i>Tentang Investor
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Wishlist Tab -->
                    <div x-show="activeTab === 'wishlist'" x-transition>
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Proyek yang Diminati</h2>
                            <p class="text-gray-600 text-sm">Total {{ $wishlistProjects->count() }} proyek dalam wishlist</p>
                        </div>

                        @if($wishlistProjects->count() > 0)
                            <!-- Wishlist Projects Grid (3 columns) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($wishlistProjects as $project)
                                    @include('pages.partials.project-card', ['project' => $project, 'wishlistedProjects' => []])
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-12">
                                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="ri-heart-line text-4xl text-gray-300"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada wishlist</h3>
                                <p class="text-gray-600">Investor ini belum menambahkan proyek ke wishlist.</p>
                            </div>
                        @endif
                    </div>

                    <!-- About Tab -->
                    <div x-show="activeTab === 'about'" x-transition>
                        <div class="prose prose-gray max-w-none">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Investor</h2>
                            
                            @if($investor->user->about)
                                <p class="text-gray-600 leading-relaxed mb-6 whitespace-pre-wrap break-words">
                                    {{ $investor->user->about }}
                                </p>
                            @else
                                <p class="text-gray-500 italic mb-6">
                                    Investor ini belum menambahkan deskripsi tentang diri mereka.
                                </p>
                            @endif
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Informasi Perusahaan</h3>
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                @if($investor->company_name || $investor->industry)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @if($investor->company_name)
                                            <div>
                                                <p class="text-sm text-gray-500 mb-1">Nama Perusahaan</p>
                                                <p class="font-semibold text-gray-800">{{ $investor->company_name }}</p>
                                            </div>
                                        @endif
                                        @if($investor->industry)
                                            <div>
                                                <p class="text-sm text-gray-500 mb-1">Industri</p>
                                                <p class="font-semibold text-gray-800">{{ $investor->industry }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">Belum ada informasi perusahaan.</p>
                                @endif
                            </div>

                            {{-- <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Statistik Investasi</h3> --}}
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                {{-- <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ $wishlistProjects->count() }}</div>
                                    <div class="text-sm text-gray-600">Proyek Diminati</div>
                                </div> --}}
                                {{-- <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ $investor->wishlists()->count() }}</div>
                                    <div class="text-sm text-gray-600">Total Wishlist</div>
                                </div> --}}
                                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ $investor->user->created_at->format('Y') }}</div>
                                    <div class="text-sm text-gray-600">Bergabung Sejak</div>
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
                            @if($investor->user->avatar)
                                <img src="{{ $investor->user->avatar_url }}" alt="{{ $investor->user->full_name ?? $investor->user->username }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-4xl font-bold">
                                    {{ strtoupper(substr($investor->user->username, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Investor Name -->
                    <h1 class="text-2xl font-bold text-gray-800 text-center mb-2">
                        {{ $investor->user->full_name ?? $investor->user->username }}
                    </h1>
                    
                    <!-- Username -->
                    <p class="text-sm text-gray-500 text-center mb-4">{{ "@" . $investor->user->username }}</p>
                    
                    <!-- Company Info -->
                    <div class="text-center mb-4">
                        @if($investor->company_name)
                            <p class="text-sm font-semibold text-gray-600">{{ $investor->company_name }}</p>
                        @endif
                        @if($investor->industry)
                            <p class="text-sm text-gray-500">{{ $investor->industry }}</p>
                        @endif
                        @if(!$investor->company_name && !$investor->industry)
                            <p class="text-sm text-gray-500">Belum ada informasi perusahaan</p>
                        @endif
                    </div>

                    <!-- Short About -->
                    @if($investor->user->short_about)
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-sm text-gray-600 text-center leading-relaxed">
                            {{ $investor->user->short_about }}
                        </p>
                    </div>
                    @endif

                    <!-- Contact Information -->
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-gray-800 uppercase mb-4">Kontak Investor</h3>
                        
                        <!-- Email -->
                        @if($investor->user->email)
                        <div class="flex items-center gap-3 text-gray-600">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="ri-mail-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm font-medium break-all">{{ $investor->user->email }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Phone -->
                        @if($investor->user->phone_number)
                        <div class="flex items-center gap-3 text-gray-600">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="ri-phone-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Telepon</p>
                                <p class="text-sm font-medium">{{ $investor->user->phone_number }}</p>
                            </div>
                        </div>
                        @endif

                        @if(!$investor->user->email && !$investor->user->phone_number)
                        <p class="text-sm text-gray-500 text-center py-4">
                            Tidak ada informasi kontak yang tersedia.
                        </p>
                        @endif

                        <!-- Investment Stats -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="grid items-center gap-4">
                                <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Proyek Diminati</p>
                                            <p class="text-2xl font-bold text-[#b01116]">{{ $wishlistProjects->count() }}</p>
                                        </div>
                                        <i class="ri-heart-fill text-3xl text-[#b01116]"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection