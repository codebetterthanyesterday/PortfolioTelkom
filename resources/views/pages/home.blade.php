@extends('layout.layout')

@section("title", "Home")

@section("content")
<div x-data="homeFilters()">
    <!-- Hero Section -->
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

    {{-- Make state when no any data loaded --}}
    @if($thisWeekPopular->isEmpty() && $mostViewed->isEmpty() && $featured->isEmpty() && $experiencedStudents->isEmpty())
        <section class="py-24 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border-2 border-dashed border-gray-300 p-12 sm:p-16 text-center">
                <div class="mb-6">
                <i class="ri-inbox-line text-6xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">
                Belum ada proyek tersedia
                </h3>
                <p class="text-gray-600 text-base sm:text-lg mb-8 leading-relaxed">
                Kami sedang mengumpulkan proyek-proyek terbaik dari mahasiswa.
                </p>
            </div>
            </div>
        </section>
    @endif

    <!-- This Week Popular Carousel -->
    @if($thisWeekPopular->count() > 0)
        <section id="this-week-popular" class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-[#b01116] to-[#8d0d11]">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
                    <!-- Left Column - Title and Description -->
                    <div class="lg:w-1/3 space-y-6 text-center lg:text-left flex flex-col items-center lg:items-start">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full">
                            <i class="ri-fire-fill text-[#b01116] text-2xl"></i>
                        </div>
                        
                        <div class="space-y-4">
                            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-tight">
                                Populer<br>Minggu Ini
                            </h2>
                            
                            <p class="text-base sm:text-lg text-white/90 leading-relaxed">
                                Proyek dengan views terbanyak dalam 7 hari terakhir
                            </p>
                        </div>
                        
                        <a href="{{ route('project') }}" class="inline-flex items-center gap-2 text-white hover:text-white/80 font-semibold text-base transition-colors duration-200">
                            jelajahi lebih lanjut
                            <i class="ri-arrow-right-line text-lg"></i>
                        </a>
                    </div>
                    
                    <!-- Right Column - Carousel -->
                    <div class="lg:w-2/3 w-full" x-data="{currentSlide: 0, totalSlides: {{ ceil($thisWeekPopular->count() / 3) }}, isPaused: false}">
                        <div class="relative">
                            <!-- Carousel Container -->
                            <div class="overflow-hidden" @mouseenter="isPaused = true" @mouseleave="isPaused = false">
                                <div class="flex transition-transform duration-500 ease-in-out" 
                                     :style="`transform: translateX(-${currentSlide * 100}%)`" 
                                     id="thisWeekCarousel">
                                    @foreach($thisWeekPopular->chunk(3) as $chunkIndex => $chunk)
                                    <div class="min-w-full grid grid-cols-1 md:grid-cols-3 gap-6">
                                        @foreach($chunk as $project)
                                        <div class="animate-fade-in" style="animation-delay: {{ $loop->index * 100 }}ms">
                                            @include('pages.partials.project-card', ['project' => $project, 'wishlistedProjects' => $wishlistedProjects])
                                        </div>
                                        @endforeach
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Navigation Arrows -->
                            @if($thisWeekPopular->count() > 3)
                            <button @click="currentSlide = currentSlide > 0 ? currentSlide - 1 : totalSlides - 1" 
                                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 bg-white hover:bg-gray-100 text-gray-800 w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-all">
                                <i class="ri-arrow-left-s-line text-2xl"></i>
                            </button>
                            <button @click="currentSlide = currentSlide < totalSlides - 1 ? currentSlide + 1 : 0" 
                                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 bg-white hover:bg-gray-100 text-gray-800 w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-all">
                                <i class="ri-arrow-right-s-line text-2xl"></i>
                            </button>
                            
                            <!-- Dots Indicator -->
                            <div class="flex justify-center gap-2 mt-6">
                                <template x-for="i in totalSlides" :key="i">
                                    <button @click="currentSlide = i - 1" 
                                            :class="currentSlide === i - 1 ? 'bg-white w-8' : 'bg-white/50 w-2'"
                                            class="h-2 rounded-full transition-all duration-300"></button>
                                </template>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Most Viewed Section -->
    @if($mostViewed->count() > 0)
        <section id="most-viewed" class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4">Paling Banyak Dilihat</h2>
                    <p class="text-lg text-gray-600">Proyek dengan total views terbanyak</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="mostViewedGrid">
                    @foreach($mostViewed as $project)
                        @include('pages.partials.project-card', ['project' => $project, 'wishlistedProjects' => $wishlistedProjects])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Top 3 Categories Sections -->
    @foreach($topCategories as $category)
        @if(!empty($categoryProjects[$category->id]) && count($categoryProjects[$category->id]) > 0)
        <section id="category-{{ $category->id }}" class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-12">
                    <div>
                        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">{{ $category->name }}</h2>
                        <p class="text-lg text-gray-600">{{ $category->projects_count }} proyek tersedia</p>
                    </div>
                    <a href="{{ route('project.category', $category->slug) }}" 
                       class="inline-flex items-center gap-2 text-[#b01116] hover:text-[#8d0d11] font-semibold transition-colors">
                        Lihat Semua
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($categoryProjects[$category->id] as $project)
                        @include('pages.partials.project-card', ['project' => $project, 'wishlistedProjects' => $wishlistedProjects])
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    @endforeach

    <!-- Featured Projects -->
    @if($featured->count() > 0)
        <section id="featured" class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4">Proyek Terbaru</h2>
                    <p class="text-lg text-gray-600">Temukan proyek-proyek terbaru dari mahasiswa</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="featuredGrid">
                    @foreach($featured as $project)
                        @include('pages.partials.project-card', ['project' => $project, 'wishlistedProjects' => $wishlistedProjects])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Most Experienced Students -->
    @if($experiencedStudents->count() > 0)
        <section id="experienced-students" class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-4">Mahasiswa Berpengalaman</h2>
                    <p class="text-lg text-gray-600">Mahasiswa dengan proyek dan kontribusi terbanyak</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($experiencedStudents as $student)
                        @include('pages.partials.student-card', ['student' => $student])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>

<script>
function homeFilters() {
    return {
        activeCategory: 'all',
        loading: false,
        autoPlayInterval: null,
        
        init() {
            this.setupWishlistForms();
            this.startAutoPlay();
        },
        
        startAutoPlay() {
            // Auto-rotate carousel every 5 seconds
            this.autoPlayInterval = setInterval(() => {
                const carouselElement = document.querySelector('[x-data*="currentSlide"]');
                if (carouselElement) {
                    const carouselData = Alpine.$data(carouselElement);
                    if (!carouselData.isPaused && carouselData.totalSlides > 1) {
                        carouselData.currentSlide = (carouselData.currentSlide + 1) % carouselData.totalSlides;
                    }
                }
            }, 5000);
        },
        
        updateCarousel(projects, wishlistedProjects) {
            const carousel = document.getElementById('thisWeekCarousel');
            if (!carousel || projects.length === 0) return;
            
            const chunks = [];
            for (let i = 0; i < projects.length; i += 3) {
                chunks.push(projects.slice(i, i + 3));
            }
            
            carousel.innerHTML = chunks.map(chunk => `
                <div class="min-w-full grid grid-cols-1 md:grid-cols-3 gap-6">
                    ${chunk.map((project, index) => `
                        <div class="animate-fade-in" style="animation-delay: ${index * 100}ms">
                            ${this.createProjectCard(project, wishlistedProjects)}
                        </div>
                    `).join('')}
                </div>
            `).join('');
            
            // Reset carousel position
            const carouselElement = document.querySelector('[x-data*="currentSlide"]');
            if (carouselElement) {
                const carouselData = Alpine.$data(carouselElement);
                carouselData.currentSlide = 0;
                carouselData.totalSlides = chunks.length;
            }
            
            this.setupWishlistForms();
        },
        
        updateSection(sectionId, projects, wishlistedProjects) {
            const section = document.getElementById(sectionId);
            if (!section) return;
            
            section.innerHTML = projects.length > 0
                ? projects.map(project => this.createProjectCard(project, wishlistedProjects)).join('')
                : '<div class="col-span-full text-center py-12"><p class="text-gray-500">No projects found.</p></div>';
            
            this.setupWishlistForms();
        },
        
        createProjectCard(project, wishlistedProjects) {
            const isWishlisted = wishlistedProjects.includes(project.id);
            const thumbnail = project.media.length > 0 ? project.media[0].url : null;
            const isInvestor = {{ auth()->check() && auth()->user()->isInvestor() ? 'true' : 'false' }};
            const isAuth = {{ auth()->check() ? 'true' : 'false' }};
            
            return `
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="relative">
                        ${thumbnail ? `
                            <div class="absolute inset-0 -z-10 blur-2xl opacity-20">
                                <img src="${thumbnail}" alt="Backdrop" class="w-full h-full object-cover">
                            </div>
                            <img src="${thumbnail}" alt="${project.title}" class="w-full h-48 object-cover">
                        ` : `
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <i class="ri-image-line text-4xl text-gray-400"></i>
                            </div>
                        `}
                        
                        ${isAuth && isInvestor ? `
                            <form action="/investor/projects/${project.id}/wishlist" 
                                  method="POST" 
                                  class="wishlist-form absolute top-3 right-3"
                                  data-project-id="${project.id}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" 
                                        class="w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-md flex items-center justify-center transition-all">
                                    <i class="${isWishlisted ? 'ri-heart-fill text-[#b01116]' : 'ri-heart-line text-gray-600'} text-xl"></i>
                                </button>
                            </form>
                        ` : ''}
                        
                        <span class="absolute top-3 left-3 bg-[#b01116] text-white text-xs font-semibold px-3 py-1 rounded-full">
                            ${project.type.toUpperCase()}
                        </span>
                    </div>
                    
                    <div class="p-5">
                        <div class="flex flex-wrap gap-2 mb-3">
                            ${project.categories.slice(0, 2).map(cat => 
                                `<span class="text-xs px-2 py-1 bg-red-50 text-red-600 rounded-md">${cat.name}</span>`
                            ).join('')}
                        </div>
                        
                        <p class="text-xs text-gray-500 mb-2">${new Date(project.updated_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</p>
                        
                        <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">${project.title}</h3>
                        
                        <p class="text-sm text-gray-600 mb-2">${project.student.user.full_name || project.student.user.username}</p>
                        
                        <p class="text-xs text-gray-400 mb-3 flex items-center gap-1">
                            <i class="ri-eye-line"></i> ${project.view_count} views
                        </p>
                        
                        <a href="/projects/${project.slug}" 
                           class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] transition-colors">
                            View Details
                            <i class="ri-arrow-right-line ml-1"></i>
                        </a>
                    </div>
                </div>
            `;
        },
        
        setupWishlistForms() {
            document.querySelectorAll('.wishlist-form').forEach(form => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    await this.toggleWishlist(form);
                });
            });
        },
        
        async toggleWishlist(form) {
            const formData = new FormData(form);
            const button = form.querySelector('button');
            const icon = button.querySelector('i');
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    if (data.isWishlisted) {
                        icon.classList.remove('ri-heart-line', 'text-gray-600');
                        icon.classList.add('ri-heart-fill', 'text-[#b01116]');
                    } else {
                        icon.classList.remove('ri-heart-fill', 'text-[#b01116]');
                        icon.classList.add('ri-heart-line', 'text-gray-600');
                    }
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    }
}
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
    opacity: 0;
}

[x-cloak] { display: none !important; }
</style>

@endsection
