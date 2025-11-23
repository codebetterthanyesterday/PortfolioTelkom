@extends('layout.admin-layout')

@section('title', "Kelola Proyek")

@section('content')
<div class="p-4 lg:p-8 bg-gray-50" x-data="projectsManager()">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Kelola Proyek</h1>
                <p class="text-gray-600 mt-1">Kelola semua proyek pelajar di sistem</p>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search Box -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Proyek</label>
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        x-model="filters.search"
                        @input.debounce.500ms="loadProjects()"
                        placeholder="Cari judul proyek atau nama pelajar..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select x-model="filters.category" @change="loadProjects()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Proyek</label>
                <select x-model="filters.type" @change="loadProjects()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Semua Tipe</option>
                    <option value="individual">Individual</option>
                    <option value="team">Team</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="filters.status" @change="loadProjects()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            <!-- Show Deleted Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
                <select x-model="filters.show_deleted" @change="loadProjects()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Aktif Saja</option>
                    <option value="true">Terhapus Saja</option>
                    <option value="all">Semua</option>
                </select>
            </div>
        </div>

        <!-- Date Range Filter & Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                <input 
                    type="date" 
                    x-model="filters.date_from"
                    @change="loadProjects()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                <input 
                    type="date" 
                    x-model="filters.date_to"
                    @change="loadProjects()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
            </div>
            <div class="flex items-end">
                <button @click="resetFilters()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                    <i class="ri-refresh-line mr-2"></i>Reset Filter
                </button>
            </div>
            <div class="flex items-end">
                <div class="w-full px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-600">
                    <span class="font-semibold text-gray-900" x-text="pagination.total"></span> proyek ditemukan
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Daftar Proyek</h2>
            <div class="flex items-center gap-2">
                <select x-model="filters.per_page" @change="loadProjects()" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#b01116]">
                    <option value="10">10 per halaman</option>
                    <option value="25">25 per halaman</option>
                    <option value="50">50 per halaman</option>
                    <option value="100">100 per halaman</option>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-[#b01116]"></div>
            <p class="mt-4 text-gray-600">Memuat data...</p>
        </div>

        <!-- Table Content -->
        <div x-show="!loading" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('title')">
                            <div class="flex items-center gap-2">
                                Proyek
                                <i class="ri-arrow-up-down-line text-sm"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pelajar</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('created_at')">
                            <div class="flex items-center gap-2">
                                Tanggal
                                <i class="ri-arrow-up-down-line text-sm"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-if="projects.length === 0">
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="ri-folder-open-line text-5xl mb-3"></i>
                                <p>Tidak ada proyek ditemukan</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(project, index) in projects" :key="project.id">
                        <tr class="hover:bg-gray-50 transition-colors" :class="project.deleted_at ? 'bg-red-50/50' : ''">
                            <td class="px-6 py-4 text-sm text-gray-900" x-text="pagination.from + index"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-[#b01116] to-[#8d0d11] rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0" :class="project.deleted_at ? 'opacity-50' : ''">
                                        <template x-if="project.media && project.media.length > 0">
                                            <img :src="`/storage/${project.media[0].file_path}`" :alt="project.title" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!project.media || project.media.length === 0">
                                            <i class="ri-folder-line text-xl text-white"></i>
                                        </template>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold truncate" :class="project.deleted_at ? 'text-gray-500 line-through' : 'text-gray-900'" x-text="project.title"></p>
                                            <template x-if="project.deleted_at">
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium flex-shrink-0">Terhapus</span>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <i class="ri-message-3-line"></i>
                                                <span x-text="project.comments_count"></span>
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <i class="ri-heart-line"></i>
                                                <span x-text="project.wishlists_count"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900" x-text="project.student?.user?.full_name || project.student?.user?.username || '-'"></p>
                                <p class="text-xs text-gray-500" x-text="project.student?.user?.email || '-'"></p>
                            </td>
                            <td class="px-6 py-4">
                                <template x-if="project.categories && project.categories.length > 0">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="category in project.categories.slice(0, 2)" :key="category.id">
                                            <span class="text-xs px-2 py-1 rounded-full bg-blue-50 text-blue-600 font-medium" x-text="category.name"></span>
                                        </template>
                                        <template x-if="project.categories.length > 2">
                                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 font-medium">+<span x-text="project.categories.length - 2"></span></span>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!project.categories || project.categories.length === 0">
                                    <span class="text-xs text-gray-400">-</span>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2 py-1 rounded-full font-medium" 
                                    :class="project.type === 'team' ? 'bg-purple-50 text-purple-600' : 'bg-green-50 text-green-600'" 
                                    x-text="project.type === 'team' ? 'Team' : 'Individual'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2 py-1 rounded-full font-medium" 
                                    :class="{
                                        'bg-green-50 text-green-600': project.status === 'published',
                                        'bg-yellow-50 text-yellow-600': project.status === 'draft',
                                        'bg-red-50 text-red-600': project.status === 'archived'
                                    }" 
                                    x-text="project.status.charAt(0).toUpperCase() + project.status.slice(1)"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <p x-text="formatDate(project.created_at)"></p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="viewProject(project)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                        <i class="ri-eye-line text-lg"></i>
                                    </button>
                                    <template x-if="!project.deleted_at">
                                        <button @click="deleteProject(project)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </button>
                                    </template>
                                    <template x-if="project.deleted_at">
                                        <button @click="restoreProject(project)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Pulihkan">
                                            <i class="ri-refresh-line text-lg"></i>
                                        </button>
                                    </template>
                                    <template x-if="project.deleted_at">
                                        <button @click="permanentDeleteProject(project)" class="p-2 text-red-700 hover:bg-red-100 rounded-lg transition-colors" title="Hapus Permanen">
                                            <i class="ri-delete-bin-2-line text-lg"></i>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div x-show="!loading && pagination.last_page > 1" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-semibold" x-text="pagination.from"></span> 
                    sampai <span class="font-semibold" x-text="pagination.to"></span> 
                    dari <span class="font-semibold" x-text="pagination.total"></span> proyek
                </div>
                <div class="flex items-center gap-2">
                    <button 
                        @click="changePage(pagination.current_page - 1)"
                        :disabled="pagination.current_page === 1"
                        :class="pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                        class="px-3 py-1 border border-gray-300 rounded-lg transition-colors">
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                    
                    <template x-for="page in visiblePages" :key="page">
                        <button 
                            @click="changePage(page)"
                            :class="pagination.current_page === page ? 'bg-[#b01116] text-white' : 'bg-white hover:bg-gray-100'"
                            class="px-3 py-1 border border-gray-300 rounded-lg transition-colors"
                            x-text="page">
                        </button>
                    </template>
                    
                    <button 
                        @click="changePage(pagination.current_page + 1)"
                        :disabled="pagination.current_page === pagination.last_page"
                        :class="pagination.current_page === pagination.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                        class="px-3 py-1 border border-gray-300 rounded-lg transition-colors">
                        <i class="ri-arrow-right-s-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Detail Modal -->
    <div x-show="modalOpen" 
         x-cloak
         x-transition.opacity
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div @click.away="modalOpen = false" 
             x-transition
             class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
                <h3 class="text-xl font-bold text-gray-900">Detail Proyek</h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6" x-show="selectedProject">
                <!-- Project Image/Thumbnail -->
                <div class="mb-6">
                    <template x-if="selectedProject?.media && selectedProject.media.length > 0">
                        <div class="w-full h-64 rounded-lg overflow-hidden bg-gray-100">
                            <img :src="`/storage/${selectedProject.media[0].file_path}`" :alt="selectedProject?.title" class="w-full h-full object-cover">
                        </div>
                    </template>
                    <template x-if="!selectedProject?.media || selectedProject.media.length === 0">
                        <div class="w-full h-64 rounded-lg bg-gradient-to-br from-[#b01116] to-[#8d0d11] flex items-center justify-center">
                            <i class="ri-folder-line text-8xl text-white/50"></i>
                        </div>
                    </template>
                </div>

                <!-- Project Title -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2" x-text="selectedProject?.title"></h2>
                    <div class="flex items-center gap-2 flex-wrap">
                        <template x-if="selectedProject?.deleted_at">
                            <span class="text-xs px-3 py-1 rounded-full font-medium bg-red-100 text-red-700">
                                <i class="ri-delete-bin-line mr-1"></i>Terhapus
                            </span>
                        </template>
                        <span class="text-xs px-3 py-1 rounded-full font-medium" 
                            :class="{
                                'bg-green-50 text-green-600': selectedProject?.status === 'published',
                                'bg-yellow-50 text-yellow-600': selectedProject?.status === 'draft',
                                'bg-red-50 text-red-600': selectedProject?.status === 'archived'
                            }" 
                            x-text="selectedProject?.status ? selectedProject.status.charAt(0).toUpperCase() + selectedProject.status.slice(1) : ''"></span>
                        <span class="text-xs px-3 py-1 rounded-full font-medium" 
                            :class="selectedProject?.type === 'team' ? 'bg-purple-50 text-purple-600' : 'bg-green-50 text-green-600'" 
                            x-text="selectedProject?.type === 'team' ? 'Team' : 'Individual'"></span>
                    </div>
                </div>

                <!-- Project Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Student Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Pelajar</label>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#b01116] to-[#8d0d11] flex items-center justify-center overflow-hidden flex-shrink-0">
                                <template x-if="selectedProject?.student?.user?.avatar">
                                    <img :src="`/storage/${selectedProject.student.user.avatar}`" alt="Avatar" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!selectedProject?.student?.user?.avatar">
                                    <span class="text-white font-semibold text-lg" x-text="selectedProject?.student?.user?.username ? selectedProject.student.user.username.charAt(0).toUpperCase() : 'S'"></span>
                                </template>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate" x-text="selectedProject?.student?.user?.full_name || selectedProject?.student?.user?.username || '-'"></p>
                                <p class="text-sm text-gray-600 truncate" x-text="selectedProject?.student?.user?.email || '-'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Date Created -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Tanggal Dibuat</label>
                        <div class="flex items-center gap-2 text-gray-900">
                            <i class="ri-calendar-line text-xl text-[#b01116]"></i>
                            <span class="font-medium" x-text="selectedProject?.created_at ? formatDate(selectedProject.created_at) : '-'"></span>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Kategori</label>
                    <template x-if="selectedProject?.categories && selectedProject.categories.length > 0">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="category in selectedProject.categories" :key="category.id">
                                <span class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-sm font-medium" x-text="category.name"></span>
                            </template>
                        </div>
                    </template>
                    <template x-if="!selectedProject?.categories || selectedProject.categories.length === 0">
                        <p class="text-gray-500 text-sm">Belum ada kategori</p>
                    </template>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <i class="ri-message-3-line text-2xl text-purple-600 mb-2"></i>
                        <p class="text-2xl font-bold text-purple-900" x-text="selectedProject?.comments_count || 0"></p>
                        <p class="text-sm text-purple-600">Komentar</p>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-4 text-center">
                        <i class="ri-heart-line text-2xl text-amber-600 mb-2"></i>
                        <p class="text-2xl font-bold text-amber-900" x-text="selectedProject?.wishlists_count || 0"></p>
                        <p class="text-sm text-amber-600">Wishlist</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <i class="ri-eye-line text-2xl text-blue-600 mb-2"></i>
                        <p class="text-2xl font-bold text-blue-900" x-text="selectedProject?.view_count || 0"></p>
                        <p class="text-sm text-blue-600">Views</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <i class="ri-money-dollar-circle-line text-2xl text-green-600 mb-2"></i>
                        <p class="text-lg font-bold text-green-900" x-text="selectedProject?.price ? formatPrice(selectedProject.price) : 'Gratis'"></p>
                        <p class="text-sm text-green-600">Harga</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Deskripsi Proyek</label>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-900 leading-relaxed whitespace-pre-wrap" x-text="selectedProject?.description || 'Tidak ada deskripsi'"></p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-200">
                    <a :href="selectedProject ? `/projects/${selectedProject.slug}` : '#'" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                        <i class="ri-external-link-line"></i>
                        Lihat di Website
                    </a>
                    <div class="flex items-center gap-3">
                        <button @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                            Tutup
                        </button>
                        <template x-if="!selectedProject?.deleted_at">
                            <button @click="deleteProjectFromModal()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium">
                                <i class="ri-delete-bin-line mr-1"></i>
                                Hapus Proyek
                            </button>
                        </template>
                        <template x-if="selectedProject?.deleted_at">
                            <div class="flex items-center gap-2">
                                <button @click="modalOpen = false; restoreProject(selectedProject)" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium">
                                    <i class="ri-refresh-line mr-1"></i>
                                    Pulihkan
                                </button>
                                <button @click="deleteProjectFromModal()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium">
                                    <i class="ri-delete-bin-fill mr-1"></i>
                                    Hapus Permanen
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function projectsManager() {
        return {
            projects: [],
            loading: false,
            modalOpen: false,
            selectedProject: null,
            filters: {
                search: '',
                category: '',
                type: '',
                status: '',
                show_deleted: '',
                date_from: '',
                date_to: '',
                sort_field: 'created_at',
                sort_order: 'desc',
                per_page: 10,
                page: 1
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                from: 0,
                to: 0,
                total: 0
            },

            init() {
                this.loadProjects();
            },

            async loadProjects() {
                this.loading = true;
                try {
                    const params = new URLSearchParams();
                    Object.keys(this.filters).forEach(key => {
                        if (this.filters[key]) {
                            params.append(key, this.filters[key]);
                        }
                    });

                    const response = await fetch(`{{ route('admin.projects.filter') }}?${params.toString()}`);
                    const data = await response.json();
                    
                    this.projects = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        from: data.from || 0,
                        to: data.to || 0,
                        total: data.total
                    };
                } catch (error) {
                    console.error('Error loading projects:', error);
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal memuat data proyek',
                        icon: 'error',
                        confirmButtonColor: '#b01116'
                    });
                } finally {
                    this.loading = false;
                }
            },

            changePage(page) {
                if (page >= 1 && page <= this.pagination.last_page) {
                    this.filters.page = page;
                    this.loadProjects();
                }
            },

            sortBy(field) {
                if (this.filters.sort_field === field) {
                    this.filters.sort_order = this.filters.sort_order === 'asc' ? 'desc' : 'asc';
                } else {
                    this.filters.sort_field = field;
                    this.filters.sort_order = 'asc';
                }
                this.loadProjects();
            },

            resetFilters() {
                this.filters = {
                    search: '',
                    category: '',
                    type: '',
                    status: '',
                    show_deleted: '',
                    date_from: '',
                    date_to: '',
                    sort_field: 'created_at',
                    sort_order: 'desc',
                    per_page: 10,
                    page: 1
                };
                this.loadProjects();
            },

            async toggleStatus(project) {
                const result = await Swal.fire({
                    title: 'Ubah Status Proyek?',
                    text: `Apakah Anda yakin ingin mengubah status proyek "${project.title}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#b01116',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                });
                
                if (!result.isConfirmed) return;

                try {
                    const response = await fetch(`/admin/projects/${project.id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        this.loadProjects();
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Status proyek berhasil diubah',
                            icon: 'success',
                            confirmButtonColor: '#b01116'
                        });
                    }
                } catch (error) {
                    console.error('Error toggling status:', error);
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal mengubah status proyek',
                        icon: 'error',
                        confirmButtonColor: '#b01116'
                    });
                }
            },

            viewProject(project) {
                this.selectedProject = project;
                this.modalOpen = true;
            },

            deleteProject(project) {
                Swal.fire({
                    title: 'Hapus Proyek?',
                    html: `Apakah Anda yakin ingin menghapus proyek <strong>"${project.title}"</strong>?<br><small class="text-gray-500">Proyek akan dipindahkan ke trash dan dapat dipulihkan.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/admin/projects/${project.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                await this.loadProjects();
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Proyek berhasil dihapus',
                                    icon: 'success',
                                    confirmButtonColor: '#b01116',
                                    timer: 2000
                                });
                            } else {
                                throw new Error('Failed to delete');
                            }
                        } catch (error) {
                            console.error('Error deleting project:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus proyek',
                                icon: 'error',
                                confirmButtonColor: '#b01116'
                            });
                        }
                    }
                });
            },

            restoreProject(project) {
                Swal.fire({
                    title: 'Pulihkan Proyek?',
                    html: `Apakah Anda yakin ingin memulihkan proyek <strong>"${project.title}"</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Pulihkan!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/admin/projects/${project.id}/restore`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                await this.loadProjects();
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Proyek berhasil dipulihkan',
                                    icon: 'success',
                                    confirmButtonColor: '#16a34a',
                                    timer: 2000
                                });
                            } else {
                                throw new Error('Failed to restore');
                            }
                        } catch (error) {
                            console.error('Error restoring project:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal memulihkan proyek',
                                icon: 'error',
                                confirmButtonColor: '#b01116'
                            });
                        }
                    }
                });
            },

            permanentDeleteProject(project) {
                Swal.fire({
                    title: 'Hapus Permanen?',
                    html: `Apakah Anda yakin ingin menghapus proyek <strong>"${project.title}"</strong> secara permanen?<br><small class="text-red-600 font-semibold">PERHATIAN: Aksi ini tidak dapat dibatalkan!</small>`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus Permanen!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    input: 'text',
                    inputPlaceholder: 'Ketik "HAPUS" untuk konfirmasi',
                    inputValidator: (value) => {
                        if (value !== 'HAPUS') {
                            return 'Ketik "HAPUS" untuk konfirmasi';
                        }
                    }
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/admin/projects/${project.id}/force-delete`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                await this.loadProjects();
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Proyek berhasil dihapus secara permanen',
                                    icon: 'success',
                                    confirmButtonColor: '#b01116',
                                    timer: 2000
                                });
                            } else {
                                throw new Error('Failed to permanently delete');
                            }
                        } catch (error) {
                            console.error('Error permanently deleting project:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus proyek secara permanen',
                                icon: 'error',
                                confirmButtonColor: '#b01116'
                            });
                        }
                    }
                });
            },

            deleteProjectFromModal() {
                const project = this.selectedProject;
                this.modalOpen = false;
                
                setTimeout(() => {
                    if (project.deleted_at) {
                        this.permanentDeleteProject(project);
                    } else {
                        this.deleteProject(project);
                    }
                }, 300);
            },

            async confirmDelete() {
                try {
                    const response = await fetch(`/admin/projects/${this.selectedProject.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        this.deleteModalOpen = false;
                        this.loadProjects();
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Proyek berhasil dihapus',
                            icon: 'success',
                            confirmButtonColor: '#b01116'
                        });
                    }
                } catch (error) {
                    console.error('Error deleting project:', error);
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal menghapus proyek',
                        icon: 'error',
                        confirmButtonColor: '#b01116'
                    });
                }
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            },

            formatPrice(price) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
            },

            get visiblePages() {
                const pages = [];
                const current = this.pagination.current_page;
                const last = this.pagination.last_page;
                
                if (last <= 7) {
                    for (let i = 1; i <= last; i++) {
                        pages.push(i);
                    }
                } else {
                    if (current <= 4) {
                        for (let i = 1; i <= 5; i++) pages.push(i);
                        pages.push('...');
                        pages.push(last);
                    } else if (current >= last - 3) {
                        pages.push(1);
                        pages.push('...');
                        for (let i = last - 4; i <= last; i++) pages.push(i);
                    } else {
                        pages.push(1);
                        pages.push('...');
                        for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                        pages.push('...');
                        pages.push(last);
                    }
                }
                return pages.filter(p => p !== '...');
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection

@section('title', "Kelola Proyek")

@section('content')
<div class="p-4 lg:p-8" x-data="projectsManager()">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Kelola Proyek</h1>
                <p class="text-gray-600 mt-1">Kelola semua proyek pelajar di sistem</p>
            </div>
            <button @click="openModal('add')" class="inline-flex items-center gap-2 px-4 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg transition-colors font-medium">
                <i class="ri-add-line text-xl"></i>
                <span>Tambah Proyek</span>
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search Box -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Proyek</label>
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        x-model="search"
                        placeholder="Cari judul, author, atau ISBN..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select x-model="categoryFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Semua Kategori</option>
                    <option value="web">Web Development</option>
                    <option value="mobile">Mobile App</option>
                    <option value="desktop">Desktop App</option>
                    <option value="data">Data Science</option>
                    <option value="ai">AI/ML</option>
                    <option value="iot">IoT</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Proyek</label>
                <select x-model="typeFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Semua Tipe</option>
                    <option value="skripsi">Skripsi</option>
                    <option value="tugas-akhir">Tugas Akhir</option>
                    <option value="penelitian">Penelitian</option>
                    <option value="pengabdian">Pengabdian</option>
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                <select x-model="sortOrder" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="title-asc">Judul A-Z</option>
                    <option value="title-desc">Judul Z-A</option>
                </select>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Tanggal</label>
                <div class="grid grid-cols-2 gap-4">
                    <div class="relative">
                        <i class="ri-calendar-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="date" 
                            x-model="dateFrom"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    </div>
                    <div class="relative">
                        <i class="ri-calendar-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="date" 
                            x-model="dateTo"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="flex items-end">
                <button @click="resetFilters()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                    <i class="ri-refresh-line mr-2"></i>Reset Filter
                </button>
            </div>

            <div class="flex items-end">
                <div class="w-full px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-600">
                    <span class="font-semibold text-gray-900" x-text="filteredProjects.length"></span> proyek ditemukan
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-900">Daftar Proyek</h2>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Proyek</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(project, index) in paginatedProjects" :key="project.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900" x-text="project.title"></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-50 text-blue-600 font-medium" x-text="project.category"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2 py-1 rounded-full bg-purple-50 text-purple-600 font-medium" x-text="project.type"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600" x-text="project.date"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="viewProject(project)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                        <i class="ri-eye-line text-lg"></i>
                                    </button>
                                    <button @click="editProject(project)" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <i class="ri-edit-line text-lg"></i>
                                    </button>
                                    <button @click="deleteProject(project)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="ri-delete-bin-line text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-semibold" x-text="((currentPage - 1) * perPage) + 1"></span> 
                    sampai <span class="font-semibold" x-text="Math.min(currentPage * perPage, filteredProjects.length)"></span> 
                    dari <span class="font-semibold" x-text="filteredProjects.length"></span> proyek
                </div>
                <div class="flex items-center gap-2">
                    <button 
                        @click="currentPage > 1 && currentPage--"
                        :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                        class="px-3 py-1 border border-gray-300 rounded-lg transition-colors">
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                    <template x-for="page in totalPages" :key="page">
                        <button 
                            @click="currentPage = page"
                            :class="currentPage === page ? 'bg-[#b01116] text-white' : 'bg-white hover:bg-gray-100'"
                            class="px-3 py-1 border border-gray-300 rounded-lg transition-colors"
                            x-text="page">
                        </button>
                    </template>
                    <button 
                        @click="currentPage < totalPages && currentPage++"
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                        class="px-3 py-1 border border-gray-300 rounded-lg transition-colors">
                        <i class="ri-arrow-right-s-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View/Edit Modal -->
    <div x-show="modalOpen" 
         x-cloak
         x-transition.opacity
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div @click.away="modalOpen = false" 
             x-transition
             class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900" x-text="modalMode === 'view' ? 'Detail Proyek' : (modalMode === 'edit' ? 'Edit Proyek' : 'Tambah Proyek')"></h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <template x-if="modalMode === 'view'">
                    <div class="space-y-6">
                        <!-- Project Title -->
                        <div class="border-b border-gray-200 pb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Proyek</label>
                            <h2 class="text-xl font-bold text-gray-900" x-text="selectedProject?.title"></h2>
                        </div>

                        <!-- Author & ISBN -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Author</label>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-[#b01116] flex items-center justify-center text-white text-lg font-semibold" x-text="selectedProject?.author.charAt(0)"></div>
                                    <span class="text-gray-900 font-medium" x-text="selectedProject?.author"></span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">ISBN</label>
                                <p class="text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded-lg" x-text="selectedProject?.isbn"></p>
                            </div>
                        </div>

                        <!-- Category & Type -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                                <span class="inline-block text-sm px-3 py-2 rounded-lg bg-blue-50 text-blue-600 font-medium" x-text="selectedProject?.category"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Proyek</label>
                                <span class="inline-block text-sm px-3 py-2 rounded-lg bg-purple-50 text-purple-600 font-medium" x-text="selectedProject?.type"></span>
                            </div>
                        </div>

                        <!-- Date & Status -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Publikasi</label>
                                <p class="text-gray-900 flex items-center gap-2">
                                    <i class="ri-calendar-line text-gray-400"></i>
                                    <span x-text="selectedProject?.date"></span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                <span 
                                    class="inline-block text-sm px-3 py-2 rounded-lg font-medium"
                                    :class="selectedProject?.status === 'published' ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600'">
                                    <i :class="selectedProject?.status === 'published' ? 'ri-check-circle-line' : 'ri-draft-line'"></i>
                                    <span x-text="selectedProject?.status === 'published' ? 'Published' : 'Draft'"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Proyek</label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-900 leading-relaxed" x-text="selectedProject?.description"></p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <button @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                                Tutup
                            </button>
                            <button @click="editProject(selectedProject)" class="px-4 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg transition-colors font-medium">
                                <i class="ri-edit-line mr-1"></i>
                                Edit Proyek
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="modalMode === 'edit' || modalMode === 'add'">
                    <form @submit.prevent="saveProject()" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Proyek *</label>
                            <input 
                                type="text" 
                                x-model="formData.title"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Author *</label>
                                <input 
                                    type="text" 
                                    x-model="formData.author"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">ISBN *</label>
                                <input 
                                    type="text" 
                                    x-model="formData.isbn"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori *</label>
                                <select 
                                    x-model="formData.category"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                    <option value="">Pilih Kategori</option>
                                    <option value="Web Development">Web Development</option>
                                    <option value="Mobile App">Mobile App</option>
                                    <option value="Desktop App">Desktop App</option>
                                    <option value="Data Science">Data Science</option>
                                    <option value="AI/ML">AI/ML</option>
                                    <option value="IoT">IoT</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe *</label>
                                <select 
                                    x-model="formData.type"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                    <option value="">Pilih Tipe</option>
                                    <option value="Skripsi">Skripsi</option>
                                    <option value="Tugas Akhir">Tugas Akhir</option>
                                    <option value="Penelitian">Penelitian</option>
                                    <option value="Pengabdian">Pengabdian</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal *</label>
                                <input 
                                    type="date" 
                                    x-model="formData.date"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                                <select 
                                    x-model="formData.status"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                            <textarea 
                                x-model="formData.description"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent"></textarea>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="button" @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg transition-colors font-medium">
                                <span x-text="modalMode === 'edit' ? 'Simpan Perubahan' : 'Tambah Proyek'"></span>
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="deleteModalOpen" 
         x-cloak
         x-transition.opacity
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div @click.away="deleteModalOpen = false" 
             x-transition
             class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-error-warning-line text-2xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Hapus Proyek?</h3>
                <p class="text-gray-600 text-center mb-6">Apakah Anda yakin ingin menghapus proyek "<span x-text="selectedProject?.title" class="font-semibold"></span>"? Aksi ini tidak dapat dibatalkan.</p>
                <div class="flex items-center gap-3">
                    <button @click="deleteModalOpen = false" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                        Batal
                    </button>
                    <button @click="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function projectsManager() {
        return {
            search: '',
            categoryFilter: '',
            typeFilter: '',
            dateFrom: '',
            dateTo: '',
            sortOrder: 'latest',
            currentPage: 1,
            perPage: 10,
            modalOpen: false,
            deleteModalOpen: false,
            modalMode: 'view', // 'view', 'edit', 'add'
            selectedProject: null,
            formData: {},
            projects: [
                { id: 1, title: 'Sistem Informasi Perpustakaan Digital', author: 'Ahmad Fauzi', isbn: 'ISBN-001-2024', category: 'Web Development', type: 'Skripsi', date: '2024-11-09', status: 'published', description: 'Sistem manajemen perpustakaan dengan fitur pencarian buku, peminjaman online, dan notifikasi otomatis.' },
                { id: 2, title: 'Aplikasi E-Commerce Mobile Fashion', author: 'Siti Nurhaliza', isbn: 'ISBN-002-2024', category: 'Mobile App', type: 'Tugas Akhir', date: '2024-11-08', status: 'published', description: 'Aplikasi mobile untuk jual beli fashion dengan fitur AR try-on dan rekomendasi AI.' },
                { id: 3, title: 'Website Portfolio Interaktif 3D', author: 'Budi Santoso', isbn: 'ISBN-003-2024', category: 'Web Development', type: 'Penelitian', date: '2024-11-06', status: 'draft', description: 'Portfolio website dengan animasi 3D menggunakan Three.js dan React.' },
                { id: 4, title: 'Dashboard Analytics Real-time IoT', author: 'Dewi Lestari', isbn: 'ISBN-004-2024', category: 'IoT', type: 'Skripsi', date: '2024-11-04', status: 'published', description: 'Dashboard monitoring sensor IoT dengan visualisasi data real-time menggunakan WebSocket.' },
                { id: 5, title: 'Sistem Prediksi Cuaca Machine Learning', author: 'Joko Widodo', isbn: 'ISBN-005-2024', category: 'AI/ML', type: 'Penelitian', date: '2024-11-03', status: 'published', description: 'Model machine learning untuk prediksi cuaca dengan akurasi tinggi menggunakan TensorFlow.' },
                { id: 6, title: 'Aplikasi Manajemen Keuangan Personal', author: 'Rina Susanti', isbn: 'ISBN-006-2024', category: 'Mobile App', type: 'Tugas Akhir', date: '2024-11-02', status: 'draft', description: 'Aplikasi mobile untuk tracking pengeluaran dan pemasukan dengan analisis finansial.' },
                { id: 7, title: 'Platform E-Learning Interaktif', author: 'Agus Salim', isbn: 'ISBN-007-2024', category: 'Web Development', type: 'Skripsi', date: '2024-11-01', status: 'published', description: 'Platform pembelajaran online dengan video streaming, quiz interaktif, dan gamifikasi.' },
                { id: 8, title: 'Sistem Deteksi Penyakit Tanaman CNN', author: 'Putri Maharani', isbn: 'ISBN-008-2024', category: 'AI/ML', type: 'Penelitian', date: '2024-10-30', status: 'published', description: 'Deteksi penyakit tanaman menggunakan CNN dengan akurasi 95%.' },
                { id: 9, title: 'Aplikasi Chat Real-time WebRTC', author: 'Bambang Sutrisno', isbn: 'ISBN-009-2024', category: 'Web Development', type: 'Tugas Akhir', date: '2024-10-28', status: 'draft', description: 'Aplikasi chat dengan video call menggunakan WebRTC dan Socket.io.' },
                { id: 10, title: 'Game Edukasi VR untuk Anak', author: 'Maya Sari', isbn: 'ISBN-010-2024', category: 'Desktop App', type: 'Skripsi', date: '2024-10-25', status: 'published', description: 'Game edukasi berbasis VR untuk pembelajaran interaktif anak sekolah dasar.' },
                { id: 11, title: 'Sistem Parkir Otomatis IoT', author: 'Dedi Kurniawan', isbn: 'ISBN-011-2024', category: 'IoT', type: 'Penelitian', date: '2024-10-22', status: 'published', description: 'Sistem parkir pintar dengan sensor IoT dan pembayaran digital.' },
                { id: 12, title: 'Aplikasi Delivery Food Multi-vendor', author: 'Lina Marlina', isbn: 'ISBN-012-2024', category: 'Mobile App', type: 'Skripsi', date: '2024-10-20', status: 'draft', description: 'Platform delivery makanan dengan multiple vendors dan tracking real-time.' },
            ],

            get filteredProjects() {
                let filtered = this.projects;

                // Search filter
                if (this.search) {
                    const searchLower = this.search.toLowerCase();
                    filtered = filtered.filter(p => 
                        p.title.toLowerCase().includes(searchLower) ||
                        p.author.toLowerCase().includes(searchLower) ||
                        p.isbn.toLowerCase().includes(searchLower)
                    );
                }

                // Category filter
                if (this.categoryFilter) {
                    filtered = filtered.filter(p => p.category.toLowerCase().includes(this.categoryFilter));
                }

                // Type filter
                if (this.typeFilter) {
                    filtered = filtered.filter(p => p.type.toLowerCase().includes(this.typeFilter));
                }

                // Date range filter
                if (this.dateFrom) {
                    filtered = filtered.filter(p => p.date >= this.dateFrom);
                }
                if (this.dateTo) {
                    filtered = filtered.filter(p => p.date <= this.dateTo);
                }

                // Sort
                filtered.sort((a, b) => {
                    switch(this.sortOrder) {
                        case 'latest':
                            return new Date(b.date) - new Date(a.date);
                        case 'oldest':
                            return new Date(a.date) - new Date(b.date);
                        case 'title-asc':
                            return a.title.localeCompare(b.title);
                        case 'title-desc':
                            return b.title.localeCompare(a.title);
                        default:
                            return 0;
                    }
                });

                return filtered;
            },

            get paginatedProjects() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.filteredProjects.slice(start, end);
            },

            get totalPages() {
                return Math.ceil(this.filteredProjects.length / this.perPage);
            },

            resetFilters() {
                this.search = '';
                this.categoryFilter = '';
                this.typeFilter = '';
                this.dateFrom = '';
                this.dateTo = '';
                this.sortOrder = 'latest';
                this.currentPage = 1;
            },

            openModal(mode) {
                this.modalMode = mode;
                this.modalOpen = true;
                if (mode === 'add') {
                    this.formData = {
                        title: '',
                        author: '',
                        isbn: '',
                        category: '',
                        type: '',
                        date: '',
                        status: 'draft',
                        description: ''
                    };
                }
            },

            viewProject(project) {
                this.selectedProject = project;
                this.modalMode = 'view';
                this.modalOpen = true;
            },

            editProject(project) {
                this.selectedProject = project;
                this.formData = { ...project };
                this.modalMode = 'edit';
                this.modalOpen = true;
            },

            deleteProject(project) {
                this.selectedProject = project;
                this.deleteModalOpen = true;
            },

            saveProject() {
                if (this.modalMode === 'add') {
                    // Add new project
                    const newId = Math.max(...this.projects.map(p => p.id)) + 1;
                    this.projects.unshift({ id: newId, ...this.formData });
                    Swal.fire({
                        icon: 'success',
                        title: 'Proyek berhasil ditambahkan!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else if (this.modalMode === 'edit') {
                    // Update existing project
                    const index = this.projects.findIndex(p => p.id === this.selectedProject.id);
                    if (index !== -1) {
                        this.projects[index] = { ...this.formData };
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Perubahan berhasil disimpan!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                this.modalOpen = false;
            },

            confirmDelete() {
                const index = this.projects.findIndex(p => p.id === this.selectedProject.id);
                if (index !== -1) {
                    this.projects.splice(index, 1);
                    Swal.fire({
                        icon: 'success',
                        title: 'Proyek berhasil dihapus!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                this.deleteModalOpen = false;
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection