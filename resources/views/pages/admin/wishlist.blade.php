@extends('layout.admin-layout')

@section('title', "Kelola Wishlist")

@section('content')
<div class="p-4 lg:p-8 bg-gray-50" x-data="wishlistsManager()">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Kelola Wishlist</h1>
                <p class="text-gray-600 mt-1">Kelola semua wishlist investor pada proyek pelajar</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    x-show="(filters.show_deleted === 'true' || filters.show_deleted === 'all') && hasDeletedWishlists"
                    @click="restoreAllWishlists()"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium flex items-center gap-2"
                    title="Pulihkan Semua Wishlist">
                    <i class="ri-refresh-line"></i>
                    <span class="hidden sm:inline">Pulihkan Semua</span>
                </button>
                <button
                    x-show="hasActiveWishlists"
                    @click="deleteAllWishlists()"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium flex items-center gap-2"
                    title="Hapus Semua Wishlist"
                    :aria-hidden="!hasActiveWishlists"
                    :class="!hasActiveWishlists ? 'hidden' : ''">
                    <i class="ri-delete-bin-line"></i>
                    <span class="hidden sm:inline">Hapus Semua</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search Box -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Wishlist</label>
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        x-model="filters.search"
                        @input.debounce.500ms="loadWishlists()"
                        placeholder="Cari investor, proyek, atau perusahaan..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                </div>
            </div>

            <!-- Show Deleted Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
                <select x-model="filters.show_deleted" @change="loadWishlists()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Aktif Saja</option>
                    <option value="true">Terhapus Saja</option>
                    <option value="all">Semua</option>
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Per Halaman</label>
                <select x-model="filters.per_page" @change="loadWishlists()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                <select x-model="filters.sort_order" @change="loadWishlists()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="desc">Terbaru</option>
                    <option value="asc">Terlama</option>
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
                    @change="loadWishlists()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                <input 
                    type="date" 
                    x-model="filters.date_to"
                    @change="loadWishlists()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
            </div>
            <div class="flex items-end">
                <button @click="resetFilters()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                    <i class="ri-refresh-line mr-2"></i>Reset Filter
                </button>
            </div>
            <div class="flex items-end">
                <div class="w-full px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-600">
                    <span class="font-semibold text-gray-900" x-text="pagination.total"></span> wishlist ditemukan
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-[#b01116]"></div>
        <p class="mt-4 text-gray-600">Memuat data...</p>
    </div>

    <!-- Wishlists Cards Grid -->
    <div x-show="!loading" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <template x-if="wishlists.length === 0">
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <i class="ri-heart-line text-5xl text-gray-400 mb-3"></i>
                <p class="text-gray-500">Tidak ada wishlist ditemukan</p>
            </div>
        </template>

        <template x-for="wishlist in wishlists" :key="wishlist.id">
            <div 
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300"
                :class="wishlist.deleted_at ? 'bg-red-50/50 border-l-4 border-l-red-500' : 'hover:-translate-y-1'">
                
                <!-- Wishlist Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white text-lg font-semibold flex-shrink-0" :class="wishlist.deleted_at ? 'opacity-50' : ''">
                            <template x-if="wishlist.investor?.user?.avatar">
                                <img :src="`/storage/${wishlist.investor.user.avatar}`" :alt="wishlist.investor.user.full_name || wishlist.investor.user.username" class="w-full h-full rounded-full object-cover">
                            </template>
                            <template x-if="!wishlist.investor?.user?.avatar">
                                <span x-text="(wishlist.investor?.user?.full_name || wishlist.investor?.user?.username || 'I').charAt(0).toUpperCase()"></span>
                            </template>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900" x-text="wishlist.investor?.user?.full_name || wishlist.investor?.user?.username || 'Unknown'"></h3>
                            <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                <i class="ri-time-line"></i>
                                <span x-text="formatDate(wishlist.created_at)"></span>
                            </p>
                        </div>
                    </div>
                    <template x-if="wishlist.deleted_at">
                        <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-medium flex-shrink-0">
                            Terhapus
                        </span>
                    </template>
                </div>

                <!-- Investor Info -->
                <div class="mb-3 pb-3 border-b border-gray-200">
                    <template x-if="wishlist.investor?.company_name">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="ri-building-line text-gray-400"></i>
                            <p class="text-sm font-medium text-gray-700" x-text="wishlist.investor.company_name"></p>
                        </div>
                    </template>
                    <template x-if="wishlist.investor?.user?.email">
                        <div class="flex items-center gap-2">
                            <i class="ri-mail-line text-gray-400"></i>
                            <p class="text-xs text-gray-500" x-text="wishlist.investor.user.email"></p>
                        </div>
                    </template>
                </div>

                <!-- Project Info -->
                <div class="mb-4">
                    <p class="text-xs text-gray-500 mb-2">Proyek yang diminati:</p>
                    <div class="flex items-start gap-3">
                        <template x-if="wishlist.project?.media && wishlist.project.media.length > 0">
                            <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                <img :src="`/storage/${wishlist.project.media[0].file_path}`" :alt="wishlist.project.title" class="w-full h-full object-cover">
                            </div>
                        </template>
                        <template x-if="!wishlist.project?.media || wishlist.project.media.length === 0">
                            <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-[#b01116] to-[#8d0d11] flex items-center justify-center flex-shrink-0">
                                <i class="ri-folder-line text-2xl text-white/50"></i>
                            </div>
                        </template>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#b01116] line-clamp-2" x-text="wishlist.project?.title || 'Proyek Terhapus'"></p>
                            <template x-if="wishlist.project?.student?.user">
                                <p class="text-xs text-gray-500 mt-1">
                                    oleh <span x-text="wishlist.project.student.user.full_name || wishlist.project.student.user.username"></span>
                                </p>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200">
                    <button 
                        @click="viewWishlist(wishlist)" 
                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                        title="Lihat Detail">
                        <i class="ri-eye-line text-lg"></i>
                    </button>
                    <template x-if="wishlist.project && !wishlist.deleted_at">
                        <a :href="`/projects/${wishlist.project.slug}`" target="_blank"
                            class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" 
                            title="Lihat Proyek">
                            <i class="ri-external-link-line text-lg"></i>
                        </a>
                    </template>
                    <template x-if="!wishlist.deleted_at">
                        <button 
                            @click="deleteWishlist(wishlist)" 
                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                            title="Hapus">
                            <i class="ri-delete-bin-line text-lg"></i>
                        </button>
                    </template>
                    <template x-if="wishlist.deleted_at">
                        <button 
                            @click="restoreWishlist(wishlist)" 
                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" 
                            title="Pulihkan">
                            <i class="ri-refresh-line text-lg"></i>
                        </button>
                    </template>
                    <template x-if="wishlist.deleted_at">
                        <button 
                            @click="forceDeleteWishlist(wishlist)" 
                            class="p-2 text-red-700 hover:bg-red-100 rounded-lg transition-colors" 
                            title="Hapus Permanen">
                            <i class="ri-delete-bin-2-line text-lg"></i>
                        </button>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Pagination -->
    <div x-show="!loading && pagination.last_page > 1" class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
                Menampilkan <span class="font-semibold" x-text="pagination.from"></span> 
                sampai <span class="font-semibold" x-text="pagination.to"></span> 
                dari <span class="font-semibold" x-text="pagination.total"></span> wishlist
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

    <!-- View Modal -->
    <div x-show="modalOpen" 
         x-cloak
         x-transition.opacity
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div @click.away="modalOpen = false" 
             x-transition
             class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Detail Wishlist</h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Investor Info -->
                    <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white text-2xl font-semibold">
                            <template x-if="selectedWishlist?.investor?.user?.avatar">
                                <img :src="`/storage/${selectedWishlist.investor.user.avatar}`" :alt="selectedWishlist.investor.user.full_name || selectedWishlist.investor.user.username" class="w-full h-full rounded-full object-cover">
                            </template>
                            <template x-if="!selectedWishlist?.investor?.user?.avatar">
                                <span x-text="(selectedWishlist?.investor?.user?.full_name || selectedWishlist?.investor?.user?.username || 'I').charAt(0).toUpperCase()"></span>
                            </template>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900" x-text="selectedWishlist?.investor?.user?.full_name || selectedWishlist?.investor?.user?.username || 'Unknown'"></h3>
                            <p class="text-sm text-gray-600" x-text="selectedWishlist?.investor?.user?.email || '-'"></p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="ri-time-line"></i>
                                Ditambahkan <span x-text="formatDate(selectedWishlist?.created_at)"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Company Info -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Informasi Perusahaan</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Perusahaan</p>
                                    <p class="text-sm font-medium text-gray-900" x-text="selectedWishlist?.investor?.company_name || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Industri</p>
                                    <p class="text-sm font-medium text-gray-900" x-text="selectedWishlist?.investor?.industry || '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Info -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Proyek yang Diminati</label>
                        <div class="bg-red-50 rounded-lg p-4">
                            <template x-if="selectedWishlist?.project">
                                <div>
                                    <template x-if="selectedWishlist.project.media && selectedWishlist.project.media.length > 0">
                                        <div class="w-full h-48 rounded-lg overflow-hidden mb-3 bg-gray-100">
                                            <img :src="`/storage/${selectedWishlist.project.media[0].file_path}`" :alt="selectedWishlist.project.title" class="w-full h-full object-cover">
                                        </div>
                                    </template>
                                    <h4 class="font-bold text-[#b01116] mb-2" x-text="selectedWishlist.project.title"></h4>
                                    <p class="text-sm text-gray-700 mb-3 line-clamp-3" x-text="selectedWishlist.project.description"></p>
                                    <template x-if="selectedWishlist.project.student?.user">
                                        <p class="text-xs text-gray-600">
                                            <i class="ri-user-line"></i>
                                            Pelajar: <span class="font-semibold" x-text="selectedWishlist.project.student.user.full_name || selectedWishlist.project.student.user.username"></span>
                                        </p>
                                    </template>
                                    <a :href="`/projects/${selectedWishlist.project.slug}`" target="_blank" class="inline-flex items-center gap-2 mt-3 text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">
                                        <i class="ri-external-link-line"></i>
                                        Lihat Proyek Lengkap
                                    </a>
                                </div>
                            </template>
                            <template x-if="!selectedWishlist?.project">
                                <p class="text-gray-500 text-center py-4">Proyek telah dihapus</p>
                            </template>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <button @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                            Tutup
                        </button>
                        <template x-if="!selectedWishlist?.deleted_at">
                            <button @click="deleteWishlist(selectedWishlist); modalOpen = false" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium">
                                <i class="ri-delete-bin-line mr-1"></i>
                                Hapus Wishlist
                            </button>
                        </template>
                        <template x-if="selectedWishlist?.deleted_at">
                            <button @click="restoreWishlist(selectedWishlist); modalOpen = false" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium">
                                <i class="ri-refresh-line mr-1"></i>
                                Pulihkan
                            </button>
                        </template>
                        <template x-if="selectedWishlist?.deleted_at">
                            <button @click="forceDeleteWishlist(selectedWishlist); modalOpen = false" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg transition-colors font-medium">
                                <i class="ri-delete-bin-2-line mr-1"></i>
                                Hapus Permanen
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function wishlistsManager() {
        return {
            wishlists: [],
            loading: false,
            modalOpen: false,
            selectedWishlist: null,
            filters: {
                search: '',
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
                this.loadWishlists();
            },

            get hasDeletedWishlists() {
                return this.wishlists.some(w => w.deleted_at !== null);
            },
            get hasActiveWishlists() {
                return this.wishlists.some(w => w.deleted_at === null);
            },

            async loadWishlists() {
                this.loading = true;
                try {
                    const params = new URLSearchParams();
                    Object.keys(this.filters).forEach(key => {
                        if (this.filters[key]) {
                            params.append(key, this.filters[key]);
                        }
                    });

                    const response = await fetch(`{{ route('admin.wishlist.filter') }}?${params.toString()}`);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    this.wishlists = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        from: data.from || 0,
                        to: data.to || 0,
                        total: data.total
                    };
                } catch (error) {
                    console.error('Error loading wishlists:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal memuat data wishlist: ' + error.message,
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
                    this.loadWishlists();
                }
            },

            resetFilters() {
                this.filters = {
                    search: '',
                    show_deleted: '',
                    date_from: '',
                    date_to: '',
                    sort_field: 'created_at',
                    sort_order: 'desc',
                    per_page: 10,
                    page: 1
                };
                this.loadWishlists();
            },

            viewWishlist(wishlist) {
                this.selectedWishlist = wishlist;
                this.modalOpen = true;
            },

            deleteAllWishlists() {
                Swal.fire({
                    title: 'Hapus Semua Wishlist?',
                    html: `<div class=\"text-left\">\n                        <p class=\"mb-3\">Apakah Anda yakin ingin menghapus <strong>SEMUA WISHLIST</strong>?</p>\n                        <div class=\"bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3\">\n                            <p class=\"text-yellow-800 text-sm font-semibold mb-2\">ℹ️ INFORMASI:</p>\n                            <ul class=\"text-yellow-700 text-sm space-y-1 ml-4\">\n                                <li>• Total <strong>${this.pagination.total}</strong> wishlist akan dipindahkan ke trash</li>\n                                <li>• Anda masih bisa memulihkan wishlist yang terhapus</li>\n                            </ul>\n                        </div>\n                    </div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            html: 'Sedang menghapus semua wishlist, mohon tunggu...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });
                        try {
                            const response = await fetch('/admin/wishlist/delete-all', {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const data = await response.json();
                            if (response.ok) {
                                await this.loadWishlists();
                                Swal.fire({
                                    title: 'Berhasil!',
                                    html: `<strong>${data.deleted_count}</strong> wishlist dipindahkan ke trash`,
                                    icon: 'success',
                                    confirmButtonColor: '#b01116',
                                    timer: 3000
                                });
                            } else {
                                throw new Error(data.message || 'Failed to delete all wishlists');
                            }
                        } catch (error) {
                            console.error('Error deleting all wishlists:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus semua wishlist: ' + error.message,
                                icon: 'error',
                                confirmButtonColor: '#b01116'
                            });
                        }
                    }
                });
            },

            restoreAllWishlists() {
                Swal.fire({
                    title: 'Pulihkan Semua Wishlist?',
                    html: `<div class=\"text-left\">\n                        <p class=\"mb-3\">Apakah Anda yakin ingin memulihkan <strong>SEMUA WISHLIST</strong> yang terhapus?</p>\n                        <div class=\"bg-green-50 border border-green-200 rounded-lg p-3 mb-3\">\n                            <p class=\"text-green-800 text-sm font-semibold mb-2\">ℹ️ INFORMASI:</p>\n                            <ul class=\"text-green-700 text-sm space-y-1 ml-4\">\n                                <li>• Semua wishlist di trash akan dipulihkan</li>\n                                <li>• Wishlist akan kembali aktif</li>\n                            </ul>\n                        </div>\n                    </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Pulihkan Semua!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memulihkan...',
                            html: 'Sedang memulihkan semua wishlist, mohon tunggu...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });
                        try {
                            const response = await fetch('/admin/wishlist/restore-all', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const data = await response.json();
                            if (response.ok) {
                                await this.loadWishlists();
                                Swal.fire({
                                    title: 'Berhasil!',
                                    html: `<strong>${data.restored_count}</strong> wishlist berhasil dipulihkan`,
                                    icon: 'success',
                                    confirmButtonColor: '#16a34a',
                                    timer: 3000
                                });
                            } else {
                                throw new Error(data.message || 'Failed to restore all wishlists');
                            }
                        } catch (error) {
                            console.error('Error restoring all wishlists:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal memulihkan semua wishlist: ' + error.message,
                                icon: 'error',
                                confirmButtonColor: '#b01116'
                            });
                        }
                    }
                });
            },

            deleteWishlist(wishlist) {
                Swal.fire({
                    title: 'Hapus Wishlist?',
                    html: `Apakah Anda yakin ingin menghapus wishlist dari <strong>"${wishlist.investor?.user?.full_name || wishlist.investor?.user?.username}"</strong>?<br><small class="text-gray-500">Wishlist akan dipindahkan ke trash dan dapat dipulihkan.</small>`,
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
                            const response = await fetch(`/admin/wishlist/${wishlist.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                await this.loadWishlists();
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Wishlist berhasil dihapus',
                                    icon: 'success',
                                    confirmButtonColor: '#b01116',
                                    timer: 2000
                                });
                            } else {
                                throw new Error('Failed to delete');
                            }
                        } catch (error) {
                            console.error('Error deleting wishlist:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus wishlist',
                                icon: 'error',
                                confirmButtonColor: '#b01116'
                            });
                        }
                    }
                });
            },

            restoreWishlist(wishlist) {
                Swal.fire({
                    title: 'Pulihkan Wishlist?',
                    html: `Apakah Anda yakin ingin memulihkan wishlist dari <strong>"${wishlist.investor?.user?.full_name || wishlist.investor?.user?.username}"</strong>?`,
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
                            const response = await fetch(`/admin/wishlist/${wishlist.id}/restore`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                await this.loadWishlists();
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Wishlist berhasil dipulihkan',
                                    icon: 'success',
                                    confirmButtonColor: '#16a34a',
                                    timer: 2000
                                });
                            } else {
                                throw new Error('Failed to restore');
                            }
                        } catch (error) {
                            console.error('Error restoring wishlist:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal memulihkan wishlist',
                                icon: 'error',
                                confirmButtonColor: '#b01116'
                            });
                        }
                    }
                });
            },

            forceDeleteWishlist(wishlist) {
                Swal.fire({
                    title: 'Hapus Permanen?',
                    html: `<div class="text-left">
                        <p class="mb-3">Apakah Anda yakin ingin menghapus <strong>PERMANEN</strong> wishlist dari <strong>"${wishlist.investor?.user?.full_name || wishlist.investor?.user?.username}"</strong>?</p>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                            <p class="text-red-800 text-sm font-semibold mb-2">⚠️ PERINGATAN:</p>
                            <ul class="text-red-700 text-sm space-y-1 ml-4">
                                <li>• Data wishlist akan dihapus secara permanen</li>
                                <li>• Aksi ini TIDAK DAPAT dibatalkan</li>
                            </ul>
                        </div>
                        <p class="text-sm text-gray-600">Ketik <strong class="text-red-600">HAPUS</strong> untuk konfirmasi:</p>
                    </div>`,
                    icon: 'warning',
                    input: 'text',
                    inputPlaceholder: 'Ketik HAPUS',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus Permanen!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    preConfirm: (value) => {
                        if (value !== 'HAPUS') {
                            Swal.showValidationMessage('Ketik HAPUS untuk konfirmasi');
                            return false;
                        }
                        return true;
                    }
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/admin/wishlist/${wishlist.id}/force-delete`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                await this.loadWishlists();
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Wishlist berhasil dihapus secara permanen',
                                    icon: 'success',
                                    confirmButtonColor: '#b01116',
                                    timer: 2000
                                });
                            } else {
                                throw new Error('Failed to delete');
                            }
                        } catch (error) {
                            console.error('Error force deleting wishlist:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus wishlist secara permanen',
                                icon: 'error',
                                confirmButtonColor: '#b01116'
                            });
                        }
                    }
                });
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
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