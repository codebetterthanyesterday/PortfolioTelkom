@extends('layout.admin-layout')

@section('title', "Kelola Proyek")

@section('content')
<div class="p-4 lg:p-8" x-data="projectsManager()">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Kelola Proyek</h1>
                <p class="text-gray-600 mt-1">Kelola semua proyek mahasiswa di sistem</p>
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
                    alert('Proyek berhasil ditambahkan!');
                } else if (this.modalMode === 'edit') {
                    // Update existing project
                    const index = this.projects.findIndex(p => p.id === this.selectedProject.id);
                    if (index !== -1) {
                        this.projects[index] = { ...this.formData };
                    }
                    alert('Proyek berhasil diperbarui!');
                }
                this.modalOpen = false;
            },

            confirmDelete() {
                const index = this.projects.findIndex(p => p.id === this.selectedProject.id);
                if (index !== -1) {
                    this.projects.splice(index, 1);
                    alert('Proyek berhasil dihapus!');
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