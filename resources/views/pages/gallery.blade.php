@extends('layout.layout')

@section('title', "Galeri Projek")

@section("content")
<div x-data="galleryFilters('{{ $category->slug ?? 'all' }}', {{ isset($category) ? 'true' : 'false' }})">
    <!-- Hero Section -->
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
                <button @click="filterByCategory('all')" 
                        :class="activeCategory === 'all' ? 'bg-[#b01116] text-white' : 'bg-white text-gray-700 border border-gray-200'"
                        class="px-5 py-2 rounded-full text-sm font-medium hover:bg-[#8d0d11] hover:text-white transition-colors">
                    All
                </button>
                @foreach($categories as $category)
                <button @click="filterByCategory('{{ $category->slug }}')" 
                        :class="activeCategory === '{{ $category->slug }}' ? 'bg-[#b01116] text-white' : 'bg-white text-gray-700 border border-gray-200'"
                        class="px-5 py-2 rounded-full text-sm font-medium hover:bg-[#8d0d11] hover:text-white transition-colors">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Show category header when filtered -->
    @if(isset($category))
    <section class="py-8 bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h2>
            <p class="text-gray-600 mt-2">{{ $featuredProjects->count() + $mostViewedProjects->count() }} proyek ditemukan</p>
        </div>
    </section>
    @endif

    <!-- Featured/Recent Projects Section -->
    <section id="featured" class="py-16 bg-white" @if(isset($category)) style="display: block;" @else x-show="!showFilteredProjects" @endif>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">
                    @if(isset($category))
                        Proyek Terbaru - {{ $category->name }}
                    @else
                        Featured Projects
                    @endif
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="featuredGrid">
                @foreach($featuredProjects as $project)
                    @include('pages.partials.project-card', ['project' => $project, 'wishlistedProjects' => $wishlistedProjects])
                @endforeach
            </div>
        </div>
    </section>

    <!-- Most Viewed Section -->
    <section id="most-viewed" class="py-16 bg-gray-50" @if(isset($category)) style="display: block;" @else x-show="!showFilteredProjects" @endif>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">
                @if(isset($category))
                    Paling Banyak Dilihat - {{ $category->name }}
                @else
                    Most Viewed Projects
                @endif
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($mostViewedProjects as $project)
                    @include('pages.partials.project-card', ['project' => $project, 'wishlistedProjects' => $wishlistedProjects])
                @endforeach
            </div>
        </div>
    </section>

    <!-- Filtered Projects Section with Pagination (Only show when NOT coming from category URL) -->
    @if(!isset($category))
    <section id="filtered-projects" class="py-16 bg-white" x-show="showFilteredProjects" x-cloak style="display: none;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Loading State -->
            <div x-show="loading" class="flex justify-center items-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#b01116]"></div>
            </div>

            <!-- Projects Grid -->
            <div x-show="!loading">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Filtered Projects</h2>
                <div id="filteredProjectsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"></div>
                <div id="paginationContainer" class="mt-8"></div>
            </div>
        </div>
    </section>
    @endif
</div>

<script>
function galleryFilters(initialCategory = 'all', isFiltered = false) {
    return {
        activeCategory: initialCategory,
        showFilteredProjects: false, // Always false since we show content directly from server
        loading: false,
        currentPage: 1,
        
        init() {
            // Setup wishlist form handlers
            this.setupWishlistForms();
        },
        
        async filterByCategory(categorySlug) {
            this.activeCategory = categorySlug;
            
            // If "All" is clicked, redirect to main gallery
            if (categorySlug === 'all') {
                window.location.href = '{{ route("project") }}';
                return;
            }
            
            // Redirect to pretty URL
            window.location.href = `/project/${categorySlug}`;
        },
        
        async fetchProjects(categorySlug, page = 1) {
            try {
                const response = await fetch(`{{ route('projects.filter') }}?category_slug=${categorySlug}&page=${page}`, {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.renderProjects(data.projects.data, data.wishlistedProjects);
                    this.renderPagination(data.projects);
                    
                    // Scroll to filtered section
                    setTimeout(() => {
                        document.getElementById('filtered-projects').scrollIntoView({ behavior: 'smooth' });
                    }, 100);
                }
            } catch (error) {
                console.error('Error fetching projects:', error);
            } finally {
                this.loading = false;
            }
        },
        
        renderProjects(projects, wishlistedProjects) {
            const grid = document.getElementById('filteredProjectsGrid');
            
            if (projects.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center py-12"><i class="ri-inbox-line text-5xl text-gray-300 mb-3"></i><p class="text-gray-500">No projects found.</p></div>';
                return;
            }
            
            grid.innerHTML = projects.map(project => this.createProjectCard(project, wishlistedProjects)).join('');
            
            // Re-setup wishlist forms
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
        
        renderPagination(paginationData) {
            const container = document.getElementById('paginationContainer');
            
            if (paginationData.last_page <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let paginationHTML = '<div class="flex justify-center items-center gap-2">';
            
            // Previous button
            if (paginationData.current_page > 1) {
                paginationHTML += `
                    <button @click="fetchProjects(activeCategory, ${paginationData.current_page - 1})" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                `;
            }
            
            // Page numbers
            for (let i = 1; i <= paginationData.last_page; i++) {
                if (
                    i === 1 ||
                    i === paginationData.last_page ||
                    (i >= paginationData.current_page - 1 && i <= paginationData.current_page + 1)
                ) {
                    paginationHTML += `
                        <button @click="fetchProjects(activeCategory, ${i})" 
                                class="px-4 py-2 border rounded-lg transition-colors ${
                                    i === paginationData.current_page 
                                        ? 'bg-[#b01116] text-white border-[#b01116]' 
                                        : 'border-gray-300 hover:bg-gray-50'
                                }">
                            ${i}
                        </button>
                    `;
                } else if (i === paginationData.current_page - 2 || i === paginationData.current_page + 2) {
                    paginationHTML += '<span class="px-2">...</span>';
                }
            }
            
            // Next button
            if (paginationData.current_page < paginationData.last_page) {
                paginationHTML += `
                    <button @click="fetchProjects(activeCategory, ${paginationData.current_page + 1})" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="ri-arrow-right-s-line"></i>
                    </button>
                `;
            }
            
            paginationHTML += '</div>';
            container.innerHTML = paginationHTML;
        },
        
        setupWishlistForms() {
            document.querySelectorAll('.wishlist-form').forEach(form => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const projectId = form.dataset.projectId;
                    await this.toggleWishlist(form, projectId);
                });
            });
        },
        
        async toggleWishlist(form, projectId) {
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
                    // Toggle icon classes
                    if (data.isWishlisted) {
                        icon.classList.remove('ri-heart-line', 'text-gray-600');
                        icon.classList.add('ri-heart-fill', 'text-[#b01116]');
                    } else {
                        icon.classList.remove('ri-heart-fill', 'text-[#b01116]');
                        icon.classList.add('ri-heart-line', 'text-gray-600');
                }
                
                // Show toast notification
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
                console.error('Error toggling wishlist:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    confirmButtonColor: '#b01116'
                });
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>

@endsection
