@extends('layout.layout')

@section('title', "Profile Investor")

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Session Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ri-check-circle-line text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-green-600 hover:text-green-800">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ri-error-warning-line text-xl"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-red-600 hover:text-red-800">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 7000)"
             class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="flex items-start gap-2">
                <i class="ri-error-warning-line text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-semibold mb-1">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-red-600 hover:text-red-800">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
        </div>
    @endif
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Column (Main Content) -->
        <div class="flex-1 lg:w-2/3 lg:order-1 order-2">
            <!-- Profile Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                <div class="p-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Profil Investor</h1>
                    <p class="text-gray-600">Kelola informasi profil</p>
                </div>
            </div>

            <!-- Tabs Content -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8" x-data="{ activeTab: 'wishlist' }">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200">
                    <div class="flex overflow-x-auto">
                        <button @click="activeTab = 'wishlist'" 
                                :class="{ 'border-[#b01116] text-[#b01116]': activeTab === 'wishlist', 'border-transparent text-gray-500': activeTab !== 'wishlist' }"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm hover:text-[#b01116] hover:border-[#b01116] transition-colors">
                            Wishlist Proyek
                        </button>
                        <button @click="activeTab = 'about'" 
                                :class="{ 'border-[#b01116] text-[#b01116]': activeTab === 'about', 'border-transparent text-gray-500': activeTab !== 'about' }"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm hover:text-[#b01116] hover:border-[#b01116] transition-colors">
                            Tentang Investor
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Wishlist Projects Tab -->
                    <div x-show="activeTab === 'wishlist'" x-transition>
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Proyek yang Diminati</h2>
                            <p class="text-gray-600">Daftar proyek yang telah Anda tambahkan ke wishlist</p>
                        </div>

                        @if($recentWishlists && $recentWishlists->count() > 0)
                            <!-- Wishlist Projects Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($recentWishlists as $project)
                                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                    <a href="{{ route('projects.show', $project->slug) }}" class="block">
                                        <div class="aspect-video relative overflow-hidden">
                                            @if($project->media && $project->media->isNotEmpty())
                                                <img src="{{ asset('storage/' . $project->media->first()->file_path) }}" 
                                                     alt="{{ $project->title }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                                    <div class="text-center text-white">
                                                        <i class="ri-image-line text-3xl mb-2"></i>
                                                        <p class="text-sm font-medium">Tidak ada gambar</p>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="absolute top-3 right-3">
                                                <form action="{{ route('investor.wishlist.remove', $project) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            onclick="return confirm('Hapus proyek ini dari wishlist?')"
                                                            class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                                                        <i class="ri-heart-fill text-red-400"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="p-4">
                                        <a href="{{ route('projects.show', $project->slug) }}" class="block">
                                            <h3 class="font-semibold text-gray-800 mb-1 hover:text-[#b01116] transition-colors">
                                                {{ $project->title }}
                                            </h3>
                                        </a>
                                        <p class="text-xs text-gray-500 mb-2">
                                            @if($project->categories && $project->categories->isNotEmpty())
                                                {{ $project->categories->first()->name }}
                                            @else
                                                Tidak ada kategori
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                            {{ Str::limit($project->description, 100) }}
                                        </p>
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>{{ $project->pivot->created_at->format('d M Y') }}</span>
                                            <a href="{{ route('projects.show', $project->slug) }}" 
                                               class="text-[#b01116] hover:text-[#8d0d11] font-medium">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Empty State for No Wishlist -->
                            <div class="text-center py-12">
                                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="ri-heart-line text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada proyek di wishlist</h3>
                                <p class="text-gray-600 mb-6">Mulai jelajahi proyek-proyek menarik dan tambahkan ke wishlist Anda.</p>
                                <a href="{{ route('project') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                    <i class="ri-search-line"></i>
                                    Jelajahi Proyek
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- About Tab -->
                    <div x-show="activeTab === 'about'" x-transition>
                        <div class="prose prose-gray max-w-none">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Saya</h2>
                            <p class="text-gray-600 leading-relaxed mb-4 whitespace-wrap break-words">
                                {{ auth()->user()->about ?? 'Belum ada deskripsi tentang investor. Klik tombol Edit Profil untuk menambahkan informasi tentang Anda.' }}
                            </p>
                            
                            <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">Informasi Perusahaan</h3>
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                @if(auth()->user()->investor->company_name || auth()->user()->investor->industry)
                                    @if(auth()->user()->investor->company_name)
                                        <div class="mb-3">
                                            <span class="font-semibold text-gray-700">Nama Perusahaan:</span>
                                            <span class="text-gray-600 ml-2">{{ auth()->user()->investor->company_name }}</span>
                                        </div>
                                    @endif
                                    @if(auth()->user()->investor->industry)
                                        <div>
                                            <span class="font-semibold text-gray-700">Industri:</span>
                                            <span class="text-gray-600 ml-2">{{ auth()->user()->investor->industry }}</span>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-gray-500 text-sm text-center py-2">
                                        Belum ada informasi perusahaan. Klik tombol Edit Profil untuk menambahkan.
                                    </p>
                                @endif
                            </div>

                            {{-- <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">Preferensi Investasi</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                                <span class="px-3 py-2 bg-blue-100 text-blue-800 text-sm font-medium rounded-full text-center">Teknologi</span>
                                <span class="px-3 py-2 bg-green-100 text-green-800 text-sm font-medium rounded-full text-center">Fintech</span>
                                <span class="px-3 py-2 bg-purple-100 text-purple-800 text-sm font-medium rounded-full text-center">E-commerce</span>
                                <span class="px-3 py-2 bg-orange-100 text-orange-800 text-sm font-medium rounded-full text-center">Edukasi</span>
                            </div>

                            <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">Pengalaman Investasi</h3>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Pengalaman investasi startup selama 5+ tahun</li>
                                <li>Portfolio investasi di 15+ perusahaan teknologi</li>
                                <li>Fokus pada early-stage dan growth-stage companies</li>
                                <li>Mentor aktif untuk startup dan scale-up</li>
                            </ul> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Fixed Sidebar) -->
        <div class="lg:w-1/3 lg:order-2 order-1">
            <div class="lg:sticky lg:top-24">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" 
                     x-data="{ 
                         showEditModal: false,
                         currentStep: 1,
                         totalSteps: 2,
                         avatarPreview: null,
                         fullName: '{{ old('full_name', auth()->user()->full_name) }}',
                         phoneNumber: '{{ old('phone_number', auth()->user()->phone_number) }}',
                         
                         validateStep1() {
                             return this.fullName.trim() !== '' && this.phoneNumber.trim() !== '';
                         },
                         
                         canProceedToNext() {
                             if (this.currentStep === 1) {
                                 return this.validateStep1();
                             }
                             return true;
                         },
                         
                         getValidationMessage() {
                             if (this.currentStep === 1) {
                                 return 'Harap isi Nama Lengkap dan Nomor Telepon sebelum melanjutkan.';
                             }
                             return '';
                         },
                         
                         nextStep() {
                             if (!this.canProceedToNext()) {
                                 alert(this.getValidationMessage());
                                 return;
                             }
                             if (this.currentStep < this.totalSteps) {
                                 this.currentStep++;
                             }
                         },
                         
                         prevStep() {
                             if (this.currentStep > 1) {
                                 this.currentStep--;
                             }
                         },
                         
                         handleAvatarPreview(event) {
                             const file = event.target.files[0];
                             if (file) {
                                 if (file.size > 2 * 1024 * 1024) {
                                     alert('Ukuran file terlalu besar. Maksimal 2MB.');
                                     event.target.value = '';
                                     return;
                                 }
                                 const reader = new FileReader();
                                 reader.onload = (e) => {
                                     this.avatarPreview = e.target.result;
                                 };
                                 reader.readAsDataURL(file);
                             }
                         },
                         
                         resetModal() {
                             this.currentStep = 1;
                             this.avatarPreview = null;
                             this.fullName = '{{ old('full_name', auth()->user()->full_name) }}';
                             this.phoneNumber = '{{ old('phone_number', auth()->user()->phone_number) }}';
                             const avatarInput = document.querySelector('input[name=avatar]');
                             if (avatarInput) avatarInput.value = '';
                         }
                     }" 
                     x-effect="document.documentElement.classList.toggle('overflow-hidden', showEditModal)">
                    <!-- Profile Picture -->
                    <div class="flex justify-center mb-6">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200">
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->username }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-4xl font-bold">
                                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Investor Name -->
                    <h1 class="text-2xl font-bold text-gray-800 text-center mb-2">{{ auth()->user()->full_name ?? auth()->user()->username }}</h1>
                    
                    <!-- Company Info -->
                    <div class="text-center mb-4">
                        @if(auth()->user()->investor->company_name)
                            <p class="text-sm font-semibold text-gray-600">{{ auth()->user()->investor->company_name }}</p>
                        @endif
                        @if(auth()->user()->investor->industry)
                            <p class="text-sm text-gray-500">{{ auth()->user()->investor->industry }}</p>
                        @endif
                        @if(!auth()->user()->investor->company_name && !auth()->user()->investor->industry)
                            <p class="text-sm text-gray-500">Belum ada informasi perusahaan</p>
                        @endif
                    </div>

                    <!-- About (Short) -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-sm text-gray-600 text-center leading-relaxed">
                            {{ auth()->user()->short_about ?? 'Belum ada deskripsi singkat. Klik tombol Edit Profil untuk menambahkan.' }}
                        </p>
                    </div>

                    <!-- Edit Profile Button -->
                    <button @click="showEditModal = true" class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2 mb-6">
                        <i class="ri-edit-line"></i>
                        Edit Profil
                    </button>

                    <!-- Contact Information -->
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-gray-800 uppercase mb-4">Kontak</h3>
                        
                        <!-- Email -->
                        <a href="mailto:{{ auth()->user()->email }}" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-mail-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm font-medium">{{ auth()->user()->email }}</p>
                            </div>
                        </a>

                        @if(auth()->user()->phone_number)
                        <!-- Phone -->
                        <a href="tel:{{ auth()->user()->phone_number }}" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-phone-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Telepon</p>
                                <p class="text-sm font-medium">{{ auth()->user()->phone_number }}</p>
                            </div>
                        </a>
                        @endif

                        <!-- Investment Stats -->
                        <div class="pt-4 border-t border-gray-200">
                            {{-- <h3 class="text-sm font-bold text-gray-800 uppercase mb-3">Statistik</h3> --}}
                            <div class="grid items-center gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-[#b01116]">{{ auth()->user()->investor->wishlists()->count() }}</div>
                                    <div class="text-xs text-gray-500">Proyek Wishlist</div>
                                </div>
                                {{-- <div class="text-center">
                                    <div class="text-2xl font-bold text-[#b01116]">0</div>
                                    <div class="text-xs text-gray-500">Investasi Aktif</div>
                                </div> --}}
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Modal (teleported to <body>) -->
                    <template x-teleport="body">
                        <div x-show="showEditModal"
                             x-transition
                             @keydown.escape.window="showEditModal = false; resetModal()"
                             @click.self="showEditModal = false; resetModal()"
                             class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
                             role="dialog" aria-modal="true" style="display: none;">
                            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                                <!-- Modal Header -->
                                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 z-10">
                                    <div class="flex items-center justify-between mb-4">
                                        <h2 class="text-xl font-bold text-gray-800">Edit Profil Investor</h2>
                                        <button @click="showEditModal = false; resetModal()" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class="ri-close-line text-2xl"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Progress Indicator -->
                                    <div class="flex items-center justify-center gap-2">
                                        <template x-for="step in totalSteps" :key="step">
                                            <div class="flex items-center">
                                                <div :class="step <= currentStep ? 'bg-[#b01116] text-white' : 'bg-gray-200 text-gray-500'" 
                                                     class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm transition-all duration-300">
                                                    <span x-text="step"></span>
                                                </div>
                                                <div x-show="step < totalSteps" 
                                                     :class="step < currentStep ? 'bg-[#b01116]' : 'bg-gray-200'" 
                                                     class="w-16 h-1 mx-1 transition-all duration-300"></div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <!-- Step Titles -->
                                    <div class="text-center mt-3">
                                        <p class="text-sm font-medium text-gray-600" x-show="currentStep === 1" x-transition>
                                            Informasi Pribadi
                                        </p>
                                        <p class="text-sm font-medium text-gray-600" x-show="currentStep === 2" x-transition>
                                            Informasi Profesional
                                        </p>
                                    </div>
                                </div>

                                <!-- Modal Body -->
                                <form action="{{ route('investor.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
                                    @csrf
                                    @method('PUT')

                                    <!-- Step 1: Personal Information -->
                                    <div x-show="currentStep === 1" x-transition class="space-y-6">
                                        <!-- Avatar Upload -->
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">Foto Profil</label>
                                            <div class="flex items-center gap-4">
                                                <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-gray-200 flex-shrink-0">
                                                    <template x-if="avatarPreview">
                                                        <img :src="avatarPreview" alt="Preview" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!avatarPreview">
                                                        @if(auth()->user()->avatar)
                                                            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-2xl font-bold">
                                                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </template>
                                                </div>
                                                <div class="flex-1">
                                                    <input type="file" 
                                                           name="avatar" 
                                                           accept="image/*" 
                                                           @change="handleAvatarPreview($event)"
                                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#b01116] file:text-white hover:file:bg-[#8d0d11] file:cursor-pointer cursor-pointer">
                                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG atau GIF (Maks. 2MB)</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Full Name -->
                                        <div>
                                            <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Nama Lengkap <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   name="full_name" 
                                                   id="full_name" 
                                                   x-model="fullName"
                                                   value="{{ old('full_name', auth()->user()->full_name) }}" 
                                                   required 
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                                        </div>

                                        <!-- Phone -->
                                        <div>
                                            <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Nomor Telepon <span class="text-red-500">*</span>
                                            </label>
                                            <input type="tel" 
                                                   name="phone_number" 
                                                   id="phone_number" 
                                                   x-model="phoneNumber"
                                                   value="{{ old('phone_number', auth()->user()->phone_number) }}" 
                                                   required 
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                                        </div>

                                        <!-- Email (readonly) -->
                                        <div>
                                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                            <input type="email" 
                                                   id="email" 
                                                   value="{{ auth()->user()->email }}" 
                                                   readonly 
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                                            <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah</p>
                                        </div>
                                    </div>

                                    <!-- Step 2: Professional Information -->
                                    <div x-show="currentStep === 2" x-transition class="space-y-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Company Name -->
                                            <div>
                                                <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Perusahaan</label>
                                                <input type="text" 
                                                       name="company_name" 
                                                       id="company_name" 
                                                       value="{{ old('company_name', auth()->user()->investor->company_name) }}" 
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                                            </div>

                                            <!-- Industry -->
                                            <div>
                                                <label for="industry" class="block text-sm font-semibold text-gray-700 mb-2">Industri</label>
                                                <input type="text" 
                                                       name="industry" 
                                                       id="industry" 
                                                       value="{{ old('industry', auth()->user()->investor->industry) }}" 
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                                            </div>
                                        </div>

                                        <!-- Short About -->
                                        <div>
                                            <label for="short_about" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Deskripsi Singkat
                                                <span class="text-gray-500 font-normal">(Maks. 500 karakter)</span>
                                            </label>
                                            <textarea name="short_about" 
                                                      id="short_about" 
                                                      rows="3" 
                                                      maxlength="500" 
                                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all resize-none">{{ old('short_about', auth()->user()->short_about) }}</textarea>
                                            <p class="text-xs text-gray-500 mt-1">Deskripsi singkat yang akan ditampilkan di kartu profil Anda</p>
                                        </div>

                                        <!-- About -->
                                        <div>
                                            <label for="about" class="block text-sm font-semibold text-gray-700 mb-2">Tentang Saya</label>
                                            <textarea name="about" 
                                                      id="about" 
                                                      rows="6" 
                                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all resize-none">{{ old('about', auth()->user()->about) }}</textarea>
                                            <p class="text-xs text-gray-500 mt-1">Ceritakan lebih lanjut tentang latar belakang, pengalaman, dan minat investasi Anda</p>
                                        </div>
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                                        <!-- Back/Cancel Button -->
                                        <button type="button" 
                                                @click="currentStep === 1 ? (showEditModal = false, resetModal()) : prevStep()" 
                                                class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                                            <span x-text="currentStep === 1 ? 'Batal' : 'Kembali'"></span>
                                        </button>
                                        
                                        <!-- Next/Submit Button -->
                                        <button type="button" 
                                                x-show="currentStep < totalSteps"
                                                @click="nextStep()"
                                                :disabled="!canProceedToNext()"
                                                :class="canProceedToNext() ? 'bg-[#b01116] hover:bg-[#8d0d11] cursor-pointer' : 'bg-gray-300 cursor-not-allowed opacity-60'"
                                                class="flex-1 px-4 py-2.5 text-white rounded-lg font-medium transition-colors">
                                            Selanjutnya
                                        </button>
                                        
                                        <button type="submit" 
                                                x-show="currentStep === totalSteps"
                                                class="flex-1 px-4 py-2.5 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection