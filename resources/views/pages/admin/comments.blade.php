@extends('layout.admin-layout')

@section('title', "Kelola Komentar")

@section('content')
<div class="p-4 lg:p-8" x-data="commentsManager()">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Kelola Komentar</h1>
                <p class="text-gray-600 mt-1">Kelola semua komentar pada proyek mahasiswa</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600">
                    <span class="font-semibold text-yellow-600" x-text="comments.filter(c => c.status === 'pending').length"></span> menunggu review
                </span>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search Box -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Komentar</label>
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        x-model="search"
                        placeholder="Cari komentar atau nama pengguna..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                <select x-model="sortOrder" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
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
                    <span class="font-semibold text-gray-900" x-text="filteredComments.length"></span> komentar ditemukan
                </div>
            </div>
        </div>

        <!-- Status Stats -->
        <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
            <div class="text-center p-3 bg-yellow-50 rounded-lg">
                <p class="text-2xl font-bold text-yellow-600" x-text="comments.filter(c => c.status === 'pending').length"></p>
                <p class="text-xs text-gray-600 mt-1">Pending</p>
            </div>
            <div class="text-center p-3 bg-green-50 rounded-lg">
                <p class="text-2xl font-bold text-green-600" x-text="comments.filter(c => c.status === 'approved').length"></p>
                <p class="text-xs text-gray-600 mt-1">Approved</p>
            </div>
            <div class="text-center p-3 bg-red-50 rounded-lg">
                <p class="text-2xl font-bold text-red-600" x-text="comments.filter(c => c.status === 'rejected').length"></p>
                <p class="text-xs text-gray-600 mt-1">Rejected</p>
            </div>
        </div>
    </div>

    <!-- Comments Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <template x-for="comment in paginatedComments" :key="comment.id">
            <div 
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 hover:-translate-y-1"
                :class="{
                    'border-l-4 border-l-yellow-500': comment.status === 'pending',
                    'border-l-4 border-l-green-500': comment.status === 'approved',
                    'border-l-4 border-l-red-500': comment.status === 'rejected'
                }">
                
                <!-- Comment Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div 
                            class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg font-semibold flex-shrink-0"
                            :style="`background: linear-gradient(135deg, ${comment.color1} 0%, ${comment.color2} 100%)`"
                            x-text="comment.user_name.charAt(0)">
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900" x-text="comment.user_name"></h3>
                            <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                <i class="ri-time-line"></i>
                                <span x-text="comment.created_at"></span>
                            </p>
                        </div>
                    </div>
                    <span 
                        class="text-xs px-3 py-1 rounded-full font-medium flex-shrink-0"
                        :class="{
                            'bg-yellow-50 text-yellow-600': comment.status === 'pending',
                            'bg-green-50 text-green-600': comment.status === 'approved',
                            'bg-red-50 text-red-600': comment.status === 'rejected'
                        }">
                        <i :class="{
                            'ri-time-line': comment.status === 'pending',
                            'ri-check-circle-line': comment.status === 'approved',
                            'ri-close-circle-line': comment.status === 'rejected'
                        }"></i>
                        <span x-text="comment.status.charAt(0).toUpperCase() + comment.status.slice(1)"></span>
                    </span>
                </div>

                <!-- Project Info -->
                <div class="mb-3 pb-3 border-b border-gray-200">
                    <p class="text-xs text-gray-500 mb-1">Pada proyek:</p>
                    <p class="text-sm font-medium text-[#b01116]" x-text="comment.project_title"></p>
                </div>

                <!-- Comment Content -->
                <div class="mb-4">
                    <p class="text-gray-700 text-sm line-clamp-3" x-text="comment.content"></p>
                </div>

                <!-- Comment Footer -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="ri-mail-line"></i>
                        <span x-text="comment.user_email"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button 
                            @click="viewComment(comment)" 
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                            title="Lihat Detail">
                            <i class="ri-eye-line text-lg"></i>
                        </button>
                        <button 
                            @click="editComment(comment)" 
                            class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" 
                            title="Edit Status">
                            <i class="ri-edit-line text-lg"></i>
                        </button>
                        <button 
                            @click="deleteComment(comment)" 
                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                            title="Hapus">
                            <i class="ri-delete-bin-line text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Pagination -->
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
                Menampilkan <span class="font-semibold" x-text="((currentPage - 1) * perPage) + 1"></span> 
                sampai <span class="font-semibold" x-text="Math.min(currentPage * perPage, filteredComments.length)"></span> 
                dari <span class="font-semibold" x-text="filteredComments.length"></span> komentar
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

    <!-- View/Edit Modal -->
    <div x-show="modalOpen" 
         x-cloak
         x-transition.opacity
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div @click.away="modalOpen = false" 
             x-transition
             class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900" x-text="modalMode === 'view' ? 'Detail Komentar' : 'Edit Komentar'"></h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <template x-if="modalMode === 'view'">
                    <div class="space-y-6">
                        <!-- User Info -->
                        <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                            <div 
                                class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-semibold"
                                :style="`background: linear-gradient(135deg, ${selectedComment?.color1} 0%, ${selectedComment?.color2} 100%)`"
                                x-text="selectedComment?.user_name.charAt(0)">
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900" x-text="selectedComment?.user_name"></h3>
                                <p class="text-sm text-gray-600" x-text="selectedComment?.user_email"></p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="ri-time-line"></i>
                                    <span x-text="selectedComment?.created_at"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Project Info -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Proyek</label>
                            <p class="text-[#b01116] font-medium bg-red-50 px-4 py-2 rounded-lg" x-text="selectedComment?.project_title"></p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <span 
                                class="inline-block text-sm px-3 py-2 rounded-lg font-medium"
                                :class="{
                                    'bg-yellow-50 text-yellow-600': selectedComment?.status === 'pending',
                                    'bg-green-50 text-green-600': selectedComment?.status === 'approved',
                                    'bg-red-50 text-red-600': selectedComment?.status === 'rejected'
                                }">
                                <i :class="{
                                    'ri-time-line': selectedComment?.status === 'pending',
                                    'ri-check-circle-line': selectedComment?.status === 'approved',
                                    'ri-close-circle-line': selectedComment?.status === 'rejected'
                                }"></i>
                                <span x-text="selectedComment?.status.charAt(0).toUpperCase() + selectedComment?.status.slice(1)"></span>
                            </span>
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
                            <button @click="editComment(selectedComment)" class="px-4 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg transition-colors font-medium">
                                <i class="ri-edit-line mr-1"></i>
                                Edit Status
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="modalMode === 'edit'">
                    <form @submit.prevent="saveComment()" class="space-y-6">
                        <!-- User Info (Read-only) -->
                        <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                            <div 
                                class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-semibold"
                                :style="`background: linear-gradient(135deg, ${formData.color1} 0%, ${formData.color2} 100%)`"
                                x-text="formData.user_name?.charAt(0)">
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900" x-text="formData.user_name"></h3>
                                <p class="text-sm text-gray-600" x-text="formData.user_email"></p>
                            </div>
                        </div>

                        <!-- Project Info (Read-only) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Proyek</label>
                            <p class="text-[#b01116] font-medium bg-red-50 px-4 py-2 rounded-lg" x-text="formData.project_title"></p>
                        </div>

                        <!-- Comment Content (Read-only) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Komentar</label>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-gray-900 leading-relaxed whitespace-pre-wrap" x-text="formData.content"></p>
                            </div>
                        </div>

                        <!-- Status (Editable) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                            <select 
                                x-model="formData.status"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Admin Note (Optional) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Admin (Opsional)</label>
                            <textarea 
                                x-model="formData.admin_note"
                                rows="3"
                                placeholder="Tambahkan catatan jika diperlukan..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent"></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg transition-colors font-medium">
                                Simpan Perubahan
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
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Hapus Komentar?</h3>
                <p class="text-gray-600 text-center mb-6">Apakah Anda yakin ingin menghapus komentar dari "<span x-text="selectedComment?.user_name" class="font-semibold"></span>"? Aksi ini tidak dapat dibatalkan.</p>
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
    function commentsManager() {
        return {
            search: '',
            statusFilter: '',
            dateFrom: '',
            dateTo: '',
            sortOrder: 'latest',
            currentPage: 1,
            perPage: 8,
            modalOpen: false,
            deleteModalOpen: false,
            modalMode: 'view', // 'view', 'edit'
            selectedComment: null,
            formData: {},
            comments: [
                { id: 1, user_name: 'Ahmad Fauzi', user_email: 'ahmad.fauzi@example.com', project_title: 'Sistem Informasi Perpustakaan Digital', content: 'Proyek yang sangat menarik! Apakah sudah ada dokumentasi lengkap untuk implementasinya? Saya tertarik untuk mempelajari lebih lanjut tentang fitur pencarian buku.', created_at: '2024-11-10 14:30', status: 'pending', admin_note: '', color1: '#b01116', color2: '#8d0d11' },
                { id: 2, user_name: 'Siti Nurhaliza', user_email: 'siti.nur@example.com', project_title: 'Aplikasi E-Commerce Mobile Fashion', content: 'Desain UI/UX-nya sangat bagus dan user-friendly! Pengalaman berbelanja jadi lebih menyenangkan.', created_at: '2024-11-10 10:15', status: 'approved', admin_note: 'Komentar positif', color1: '#3b82f6', color2: '#2563eb' },
                { id: 3, user_name: 'Budi Santoso', user_email: 'budi.s@example.com', project_title: 'Website Portfolio Interaktif 3D', content: 'Animasinya smooth dan performa website sangat cepat. Bagaimana cara optimasinya?', created_at: '2024-11-09 16:45', status: 'approved', admin_note: '', color1: '#8b5cf6', color2: '#7c3aed' },
                { id: 4, user_name: 'Dewi Lestari', user_email: 'dewi.lestari@example.com', project_title: 'Dashboard Analytics Real-time IoT', content: 'Fitur real-time-nya sangat impressive! Bagaimana cara implementasi WebSocket-nya? Apakah menggunakan library tertentu?', created_at: '2024-11-09 09:20', status: 'pending', admin_note: '', color1: '#f59e0b', color2: '#d97706' },
                { id: 5, user_name: 'Joko Widodo', user_email: 'joko.widodo@example.com', project_title: 'Sistem Prediksi Cuaca Machine Learning', content: 'Model ML-nya akurat. Dataset apa yang digunakan untuk training?', created_at: '2024-11-08 13:55', status: 'approved', admin_note: 'Pertanyaan teknis bagus', color1: '#10b981', color2: '#059669' },
                { id: 6, user_name: 'Rina Susanti', user_email: 'rina.susanti@example.com', project_title: 'Aplikasi Manajemen Keuangan Personal', content: 'Spam content here. Check this link for free money!', created_at: '2024-11-08 08:30', status: 'rejected', admin_note: 'Konten spam', color1: '#ef4444', color2: '#dc2626' },
                { id: 7, user_name: 'Agus Salim', user_email: 'agus.salim@example.com', project_title: 'Platform E-Learning Interaktif', content: 'Fitur gamifikasi-nya menarik. Apakah bisa ditambahkan leaderboard global?', created_at: '2024-11-07 15:10', status: 'approved', admin_note: '', color1: '#06b6d4', color2: '#0891b2' },
                { id: 8, user_name: 'Putri Maharani', user_email: 'putri.maharani@example.com', project_title: 'Sistem Deteksi Penyakit Tanaman CNN', content: 'Akurasi 95% sangat bagus! Apakah model bisa digunakan untuk tanaman lain juga?', created_at: '2024-11-07 11:40', status: 'pending', admin_note: '', color1: '#ec4899', color2: '#db2777' },
                { id: 9, user_name: 'Bambang Sutrisno', user_email: 'bambang.s@example.com', project_title: 'Aplikasi Chat Real-time WebRTC', content: 'Video call-nya lancar tanpa lag. Infrastruktur server seperti apa yang digunakan?', created_at: '2024-11-06 14:25', status: 'approved', admin_note: '', color1: '#14b8a6', color2: '#0d9488' },
                { id: 10, user_name: 'Maya Sari', user_email: 'maya.sari@example.com', project_title: 'Game Edukasi VR untuk Anak', content: 'Konsepnya sangat inovatif untuk pembelajaran anak! Apakah sudah ditest dengan user sebenarnya?', created_at: '2024-11-06 09:50', status: 'pending', admin_note: '', color1: '#f97316', color2: '#ea580c' },
                { id: 11, user_name: 'Dedi Kurniawan', user_email: 'dedi.k@example.com', project_title: 'Sistem Parkir Otomatis IoT', content: 'Integrasi sensor dan payment gateway-nya seamless. Berapa biaya development hardware-nya?', created_at: '2024-11-05 16:15', status: 'approved', admin_note: '', color1: '#a855f7', color2: '#9333ea' },
                { id: 12, user_name: 'Lina Marlina', user_email: 'lina.marlina@example.com', project_title: 'Aplikasi Delivery Food Multi-vendor', content: 'Tracking real-time-nya akurat. Menggunakan Google Maps API atau alternatif lain?', created_at: '2024-11-05 12:30', status: 'pending', admin_note: '', color1: '#84cc16', color2: '#65a30d' },
            ],

            get filteredComments() {
                let filtered = this.comments;

                // Search filter
                if (this.search) {
                    const searchLower = this.search.toLowerCase();
                    filtered = filtered.filter(c => 
                        c.content.toLowerCase().includes(searchLower) ||
                        c.user_name.toLowerCase().includes(searchLower) ||
                        c.project_title.toLowerCase().includes(searchLower)
                    );
                }

                // Status filter
                if (this.statusFilter) {
                    filtered = filtered.filter(c => c.status === this.statusFilter);
                }

                // Date range filter
                if (this.dateFrom) {
                    filtered = filtered.filter(c => c.created_at.split(' ')[0] >= this.dateFrom);
                }
                if (this.dateTo) {
                    filtered = filtered.filter(c => c.created_at.split(' ')[0] <= this.dateTo);
                }

                // Sort
                filtered.sort((a, b) => {
                    const dateA = new Date(a.created_at);
                    const dateB = new Date(b.created_at);
                    return this.sortOrder === 'latest' ? dateB - dateA : dateA - dateB;
                });

                return filtered;
            },

            get paginatedComments() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.filteredComments.slice(start, end);
            },

            get totalPages() {
                return Math.ceil(this.filteredComments.length / this.perPage);
            },

            resetFilters() {
                this.search = '';
                this.statusFilter = '';
                this.dateFrom = '';
                this.dateTo = '';
                this.sortOrder = 'latest';
                this.currentPage = 1;
            },

            viewComment(comment) {
                this.selectedComment = comment;
                this.modalMode = 'view';
                this.modalOpen = true;
            },

            editComment(comment) {
                this.selectedComment = comment;
                this.formData = { ...comment };
                this.modalMode = 'edit';
                this.modalOpen = true;
            },

            deleteComment(comment) {
                this.selectedComment = comment;
                this.deleteModalOpen = true;
            },

            saveComment() {
                const index = this.comments.findIndex(c => c.id === this.selectedComment.id);
                if (index !== -1) {
                    this.comments[index] = { ...this.formData };
                    alert('Status komentar berhasil diperbarui!');
                }
                this.modalOpen = false;
            },

            confirmDelete() {
                const index = this.comments.findIndex(c => c.id === this.selectedComment.id);
                if (index !== -1) {
                    this.comments.splice(index, 1);
                    alert('Komentar berhasil dihapus!');
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