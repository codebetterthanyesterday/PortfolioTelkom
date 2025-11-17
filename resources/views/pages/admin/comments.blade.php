@extends('layout.admin-layout')

@section('title', "Kelola Komentar")

@section('content')
<div class="p-4 lg:p-8 bg-gray-50" x-data="commentsManager()">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Kelola Komentar</h1>
                <p class="text-gray-600 mt-1">Kelola semua komentar pada proyek mahasiswa</p>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search Box -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Komentar</label>
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        x-model="filters.search"
                        @input.debounce.500ms="loadComments()"
                        placeholder="Cari komentar, pengguna, atau proyek..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                </div>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select x-model="filters.type" @change="loadComments()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Semua Tipe</option>
                    <option value="parent">Komentar Utama</option>
                    <option value="reply">Balasan</option>
                </select>
            </div>

            <!-- Show Deleted Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
                <select x-model="filters.show_deleted" @change="loadComments()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Aktif Saja</option>
                    <option value="true">Terhapus Saja</option>
                    <option value="all">Semua (Aktif & Terhapus)</option>
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Per Halaman</label>
                <select x-model="filters.per_page" @change="loadComments()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                <select x-model="filters.sort_order" @change="loadComments()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
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
                    @change="loadComments()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                <input 
                    type="date" 
                    x-model="filters.date_to"
                    @change="loadComments()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
            </div>
            <div class="flex items-end">
                <button @click="resetFilters()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                    <i class="ri-refresh-line mr-2"></i>Reset Filter
                </button>
            </div>
            <div class="flex items-end">
                <div class="w-full px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-600">
                    <span class="font-semibold text-gray-900" x-text="pagination.total"></span> komentar ditemukan
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-[#b01116]"></div>
        <p class="mt-4 text-gray-600">Memuat data...</p>
    </div>

    <!-- Comments Cards Grid -->
    <div x-show="!loading" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <template x-if="comments.length === 0">
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <i class="ri-message-3-line text-5xl text-gray-400 mb-3"></i>
                <p class="text-gray-500">Tidak ada komentar ditemukan</p>
            </div>
        </template>

        <template x-for="comment in comments" :key="comment.id">
            <div 
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300"
                :class="comment.deleted_at ? 'bg-red-50/50 border-l-4 border-l-red-500' : 'hover:-translate-y-1'">
                
                <!-- Comment Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#b01116] to-[#8d0d11] flex items-center justify-center text-white text-lg font-semibold flex-shrink-0" :class="comment.deleted_at ? 'opacity-50' : ''">
                            <template x-if="comment.user?.avatar">
                                <img :src="`/storage/${comment.user.avatar}`" :alt="comment.user.full_name || comment.user.username" class="w-full h-full rounded-full object-cover">
                            </template>
                            <template x-if="!comment.user?.avatar">
                                <span x-text="(comment.user?.full_name || comment.user?.username || '?').charAt(0).toUpperCase()"></span>
                            </template>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900" x-text="comment.user?.full_name || comment.user?.username"></h3>
                            <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                <i class="ri-time-line"></i>
                                <span x-text="formatDate(comment.created_at)"></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <template x-if="comment.parent_id">
                            <span class="text-xs px-2 py-1 rounded-full bg-purple-50 text-purple-600 font-medium flex-shrink-0">
                                <i class="ri-reply-line"></i> Balasan
                            </span>
                        </template>
                        <template x-if="comment.deleted_at">
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-medium flex-shrink-0">
                                Terhapus
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Project Info -->
                <div class="mb-3 pb-3 border-b border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Pada proyek:</p>
                    <p class="text-sm font-medium text-[#b01116]" x-text="comment.project?.title || 'Unknown Project'"></p>
                    <template x-if="comment.project?.student?.user">
                        <p class="text-xs text-gray-500 mt-1">
                            oleh <span x-text="comment.project.student.user.full_name || comment.project.student.user.username"></span>
                        </p>
                    </template>
                </div>

                <!-- Comment Content -->
                <div class="mb-4">
                    <p class="text-gray-700 text-sm line-clamp-3" :class="comment.deleted_at ? 'opacity-60' : ''" x-text="comment.content"></p>
                </div>

                <!-- Comment Footer -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-3 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <i class="ri-reply-line"></i>
                            <span x-text="comment.replies_count || 0"></span> balasan
                        </span>
                        <template x-if="comment.user?.email">
                            <span class="flex items-center gap-1">
                                <i class="ri-mail-line"></i>
                                <span x-text="comment.user.email"></span>
                            </span>
                        </template>
                    </div>
                    <div class="flex items-center gap-2">
                        <button 
                            @click="viewComment(comment)" 
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                            title="Lihat Detail">
                            <i class="ri-eye-line text-lg"></i>
                        </button>
                        <template x-if="!comment.deleted_at">
                            <button 
                                @click="deleteComment(comment)" 
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                title="Hapus">
                                <i class="ri-delete-bin-line text-lg"></i>
                            </button>
                        </template>
                        <template x-if="comment.deleted_at">
                            <button 
                                @click="restoreComment(comment)" 
                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" 
                                title="Pulihkan">
                                <i class="ri-refresh-line text-lg"></i>
                            </button>
                        </template>
                        <template x-if="comment.deleted_at">
                            <button 
                                @click="forceDeleteComment(comment)" 
                                class="p-2 text-red-700 hover:bg-red-100 rounded-lg transition-colors" 
                                title="Hapus Permanen">
                                <i class="ri-delete-bin-2-line text-lg"></i>
                            </button>
                        </template>
                    </div>
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
                dari <span class="font-semibold" x-text="pagination.total"></span> komentar
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
                <h3 class="text-xl font-bold text-gray-900">Detail Komentar</h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <div class="space-y-6">
                    <!-- User Info -->
                    <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#b01116] to-[#8d0d11] flex items-center justify-center text-white text-2xl font-semibold">
                            <template x-if="selectedComment?.user?.avatar">
                                <img :src="`/storage/${selectedComment.user.avatar}`" :alt="selectedComment.user.full_name || selectedComment.user.username" class="w-full h-full rounded-full object-cover">
                            </template>
                            <template x-if="!selectedComment?.user?.avatar">
                                <span x-text="(selectedComment?.user?.full_name || selectedComment?.user?.username || '?').charAt(0).toUpperCase()"></span>
                            </template>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900" x-text="selectedComment?.user?.full_name || selectedComment?.user?.username"></h3>
                            <p class="text-sm text-gray-600" x-text="selectedComment?.user?.email"></p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="ri-time-line"></i>
                                <span x-text="formatDate(selectedComment?.created_at)"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Project Info -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Proyek</label>
                        <p class="text-[#b01116] font-medium bg-red-50 px-4 py-2 rounded-lg" x-text="selectedComment?.project?.title || 'Unknown Project'"></p>
                        <template x-if="selectedComment?.project?.student?.user">
                            <p class="text-xs text-gray-500 mt-2">
                                oleh <span class="font-semibold" x-text="selectedComment.project.student.user.full_name || selectedComment.project.student.user.username"></span>
                            </p>
                        </template>
                    </div>

                    <!-- Comment Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Komentar</label>
                        <span class="inline-block text-sm px-3 py-2 rounded-lg font-medium"
                            :class="selectedComment?.parent_id ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600'">
                            <i :class="selectedComment?.parent_id ? 'ri-reply-line' : 'ri-message-3-line'"></i>
                            <span x-text="selectedComment?.parent_id ? 'Balasan' : 'Komentar Utama'"></span>
                        </span>
                        <template x-if="selectedComment?.replies_count > 0">
                            <p class="text-sm text-gray-600 mt-2">
                                <i class="ri-reply-line"></i>
                                <span x-text="selectedComment.replies_count"></span> balasan
                            </p>
                        </template>
                    </div>

                    <!-- Comment Content -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Komentar</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900 leading-relaxed whitespace-pre-wrap" x-text="selectedComment?.content"></p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <button @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                            Tutup
                        </button>
                        <template x-if="!selectedComment?.deleted_at">
                            <button @click="deleteComment(selectedComment); modalOpen = false" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium">
                                <i class="ri-delete-bin-line mr-1"></i>
                                Hapus Komentar
                            </button>
                        </template>
                        <template x-if="selectedComment?.deleted_at">
                            <button @click="restoreComment(selectedComment); modalOpen = false" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium">
                                <i class="ri-refresh-line mr-1"></i>
                                Pulihkan
                            </button>
                        </template>
                        <template x-if="selectedComment?.deleted_at">
                            <button @click="forceDeleteComment(selectedComment); modalOpen = false" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg transition-colors font-medium">
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
    function commentsManager() {
        return {
            comments: [],
            loading: false,
            modalOpen: false,
            selectedComment: null,
            filters: {
                search: '',
                type: '',
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
                this.loadComments();
            },

            async loadComments() {
                this.loading = true;
                try {
                    const params = new URLSearchParams();
                    Object.keys(this.filters).forEach(key => {
                        if (this.filters[key]) {
                            params.append(key, this.filters[key]);
                        }
                    });

                    const response = await fetch(`{{ route('admin.comments.filter') }}?${params.toString()}`);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    this.comments = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        from: data.from || 0,
                        to: data.to || 0,
                        total: data.total
                    };
                } catch (error) {
                    console.error('Error loading comments:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal memuat data komentar: ' + error.message,
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
                    this.loadComments();
                }
            },

            resetFilters() {
                this.filters = {
                    search: '',
                    type: '',
                    show_deleted: '',
                    date_from: '',
                    date_to: '',
                    sort_field: 'created_at',
                    sort_order: 'desc',
                    per_page: 10,
                    page: 1
                };
                this.loadComments();
            },

            viewComment(comment) {
                this.selectedComment = comment;
                this.modalOpen = true;
            },

            deleteComment(comment) {
                Swal.fire({
                    title: 'Hapus Komentar?',
                    html: `Apakah Anda yakin ingin menghapus komentar dari <strong>"${comment.user?.full_name || comment.user?.username}"</strong>?<br><small class="text-gray-500">Komentar akan dipindahkan ke trash dan dapat dipulihkan.</small>`,
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
                            const response = await fetch(`/admin/comments/${comment.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                await this.loadComments();
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Komentar berhasil dihapus',
                                    icon: 'success',
                                    confirmButtonColor: '#b01116',
                                    timer: 2000
                                });
                            } else {
                                throw new Error('Failed to delete');
                            }
                        } catch (error) {
                            console.error('Error deleting comment:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus komentar',
                                icon: 'error',
                                confirmButtonColor: '#b01116'
                            });
                        }
                    }
                });
            },

            restoreComment(comment) {
                Swal.fire({
                    title: 'Pulihkan Komentar?',
                    html: `Apakah Anda yakin ingin memulihkan komentar dari <strong>"${comment.user?.full_name || comment.user?.username}"</strong>?`,
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
                            const response = await fetch(`/admin/comments/${comment.id}/restore`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                await this.loadComments();
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Komentar berhasil dipulihkan',
                                    icon: 'success',
                                    confirmButtonColor: '#16a34a',
                                    timer: 2000
                                });
                            } else {
                                throw new Error('Failed to restore');
                            }
                        } catch (error) {
                            console.error('Error restoring comment:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal memulihkan komentar',
                                icon: 'error',
                                confirmButtonColor: '#b01116'
                            });
                        }
                    }
                });
            },

            forceDeleteComment(comment) {
                Swal.fire({
                    title: 'Hapus Permanen?',
                    html: `<div class="text-left">
                        <p class="mb-3">Apakah Anda yakin ingin menghapus <strong>PERMANEN</strong> komentar dari <strong>"${comment.user?.full_name || comment.user?.username}"</strong>?</p>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                            <p class="text-red-800 text-sm font-semibold mb-2">⚠️ PERINGATAN:</p>
                            <ul class="text-red-700 text-sm space-y-1 ml-4">
                                <li>• Data komentar akan dihapus secara permanen</li>
                                <li>• Semua balasan akan ikut terhapus</li>
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
                            const response = await fetch(`/admin/comments/${comment.id}/force-delete`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            if (response.ok) {
                                await this.loadComments();
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Komentar berhasil dihapus secara permanen',
                                    icon: 'success',
                                    confirmButtonColor: '#b01116',
                                    timer: 2000
                                });
                            } else {
                                throw new Error('Failed to delete');
                            }
                        } catch (error) {
                            console.error('Error force deleting comment:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus komentar secara permanen',
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
