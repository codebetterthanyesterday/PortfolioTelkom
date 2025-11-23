@extends('layout.admin-layout')

@section('title', "Kelola Pengguna")

@section('content')
<div class="p-4 lg:p-8 bg-gray-50" x-data="usersManager()">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Kelola Pengguna</h1>
                    <p class="text-gray-600 mt-1">Kelola semua pengguna yang terdaftar di sistem</p>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Search Box -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pengguna</label>
                    <div class="relative">
                        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            x-model="filters.search"
                            @input.debounce.500ms="loadUsers()"
                            placeholder="Cari nama, email, atau username..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    </div>
                </div>

                <!-- Role Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select x-model="filters.role" @change="loadUsers()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                        <option value="">Semua Role</option>
                        <option value="student">Student</option>
                        <option value="investor">Investor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select x-model="filters.status" @change="loadUsers()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="verified">Terverifikasi</option>
                        <option value="unverified">Belum Verifikasi</option>
                    </select>
                </div>

                <!-- Show Deleted Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan</label>
                    <select x-model="filters.show_deleted" @change="loadUsers()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                        <option value="">Aktif Saja</option>
                        <option value="true">Terhapus Saja</option>
                        <option value="all">Semua</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Per Halaman</label>
                    <select x-model="filters.per_page" @change="loadUsers()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
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
                        @change="loadUsers()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input 
                        type="date" 
                        x-model="filters.date_to"
                        @change="loadUsers()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                </div>
                <div class="flex items-end">
                    <button @click="resetFilters()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                        <i class="ri-refresh-line mr-2"></i>Reset Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <div class="w-full px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-600">
                        <span class="font-semibold text-gray-900" x-text="pagination.total"></span> pengguna ditemukan
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900">Daftar Pengguna</h2>
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('full_name')">
                                <div class="flex items-center gap-2">
                                    Pengguna
                                    <i class="ri-arrow-up-down-line text-sm"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="sortBy('created_at')">
                                <div class="flex items-center gap-2">
                                    Bergabung
                                    <i class="ri-arrow-up-down-line text-sm"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-if="users.length === 0">
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="ri-user-line text-5xl mb-3"></i>
                                    <p>Tidak ada pengguna ditemukan</p>
                                </td>
                            </tr>
                        </template>
                        <template x-for="(user, index) in users" :key="user.id">
                            <tr class="hover:bg-gray-50 transition-colors" :class="user.deleted_at ? 'bg-red-50/50' : ''">
                                <td class="px-6 py-4 text-sm text-gray-900" x-text="pagination.from + index"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#b01116] to-[#8d0d11] flex items-center justify-center text-white text-sm font-semibold flex-shrink-0" :class="user.deleted_at ? 'opacity-50' : ''">
                                            <template x-if="user.avatar">
                                                <img :src="`/storage/${user.avatar}`" :alt="user.full_name || user.username" class="w-full h-full rounded-full object-cover">
                                            </template>
                                            <template x-if="!user.avatar">
                                                <span x-text="(user.full_name || user.username || '?').charAt(0).toUpperCase()"></span>
                                            </template>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-semibold truncate" :class="user.deleted_at ? 'text-gray-500 line-through' : 'text-gray-900'" x-text="user.full_name || user.username"></p>
                                                <template x-if="user.deleted_at">
                                                    <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium flex-shrink-0">Terhapus</span>
                                                </template>
                                            </div>
                                            <p class="text-xs text-gray-500" x-text="'@' + user.username"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900" x-text="user.email"></p>
                                    <template x-if="user.student?.nim">
                                        <p class="text-xs text-gray-500">NIM: <span x-text="user.student.nim"></span></p>
                                    </template>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs px-2 py-1 rounded-full font-medium"
                                        :class="{
                                            'bg-blue-50 text-blue-600': user.role === 'student',
                                            'bg-purple-50 text-purple-600': user.role === 'investor',
                                            'bg-amber-50 text-amber-600': user.role === 'admin'
                                        }">
                                        <i :class="{
                                            'ri-graduation-cap-line': user.role === 'student',
                                            'ri-briefcase-line': user.role === 'investor',
                                            'ri-admin-line': user.role === 'admin'
                                        }"></i>
                                        <span x-text="user.role.charAt(0).toUpperCase() + user.role.slice(1)"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs px-2 py-1 rounded-full font-medium"
                                        :class="user.email_verified_at ? 'bg-green-50 text-green-600' : 'bg-gray-50 text-gray-600'">
                                        <i :class="user.email_verified_at ? 'ri-checkbox-circle-line' : 'ri-error-warning-line'"></i>
                                        <span x-text="user.email_verified_at ? 'Verified' : 'Unverified'"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <p x-text="formatDate(user.created_at)"></p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="viewUser(user)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                            <i class="ri-eye-line text-lg"></i>
                                        </button>
                                        <template x-if="!user.deleted_at">
                                            <button @click="deleteUser(user)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                <i class="ri-delete-bin-line text-lg"></i>
                                            </button>
                                        </template>
                                        <template x-if="user.deleted_at">
                                            <button @click="restoreUser(user)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Pulihkan">
                                                <i class="ri-refresh-line text-lg"></i>
                                            </button>
                                        </template>
                                        <template x-if="user.deleted_at">
                                            <button @click="forceDeleteUser(user)" class="p-2 text-red-700 hover:bg-red-100 rounded-lg transition-colors" title="Hapus Permanen">
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
                        dari <span class="font-semibold" x-text="pagination.total"></span> pengguna
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
                    <h3 class="text-xl font-bold text-gray-900" x-text="modalMode === 'view' ? 'Detail Pengguna' : (modalMode === 'edit' ? 'Edit Pengguna' : 'Tambah Pengguna')"></h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <template x-if="modalMode === 'view'">
                        <div class="space-y-6">
                            <!-- User Avatar & Name -->
                            <div class="flex flex-col items-center pb-4 border-b border-gray-200">
                                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-[#b01116] to-[#8d0d11] flex items-center justify-center text-white text-3xl font-semibold mb-4">
                                    <template x-if="selectedUser?.avatar">
                                        <img :src="`/storage/${selectedUser.avatar}`" :alt="selectedUser.full_name || selectedUser.username" class="w-full h-full rounded-full object-cover">
                                    </template>
                                    <template x-if="!selectedUser?.avatar">
                                        <span x-text="(selectedUser?.full_name || selectedUser?.username || '?').charAt(0).toUpperCase()"></span>
                                    </template>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900" x-text="selectedUser?.full_name || selectedUser?.username"></h2>
                                <p class="text-sm text-gray-500 mt-1" x-text="'@' + selectedUser?.username"></p>
                                <template x-if="selectedUser?.deleted_at">
                                    <span class="mt-2 text-xs px-3 py-1 rounded-full font-medium bg-red-100 text-red-700">
                                        <i class="ri-delete-bin-line"></i> Terhapus
                                    </span>
                                </template>
                                <span 
                                    class="mt-2 text-sm px-3 py-1 rounded-full font-medium"
                                    :class="{
                                        'bg-blue-50 text-blue-600': selectedUser?.role === 'student',
                                        'bg-purple-50 text-purple-600': selectedUser?.role === 'investor',
                                        'bg-amber-50 text-amber-600': selectedUser?.role === 'admin'
                                    }">
                                    <i :class="{
                                        'ri-graduation-cap-line': selectedUser?.role === 'student',
                                        'ri-briefcase-line': selectedUser?.role === 'investor',
                                        'ri-admin-line': selectedUser?.role === 'admin'
                                    }"></i>
                                    <span x-text="selectedUser?.role.charAt(0).toUpperCase() + selectedUser?.role.slice(1)"></span>
                                </span>
                            </div>

                            <!-- User Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                    <p class="text-gray-900 flex items-center gap-2">
                                        <i class="ri-mail-line text-gray-400"></i>
                                        <span x-text="selectedUser?.email"></span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Email</label>
                                    <span class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-lg font-medium"
                                        :class="selectedUser?.email_verified_at ? 'bg-green-50 text-green-600' : 'bg-gray-50 text-gray-600'">
                                        <i :class="selectedUser?.email_verified_at ? 'ri-checkbox-circle-line' : 'ri-error-warning-line'"></i>
                                        <span x-text="selectedUser?.email_verified_at ? 'Terverifikasi' : 'Belum Verifikasi'"></span>
                                    </span>
                                </div>
                            </div>

                            <template x-if="selectedUser?.student">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">NIM</label>
                                        <p class="text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded-lg" x-text="selectedUser.student.nim || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Program Studi</label>
                                        <p class="text-gray-900" x-text="selectedUser.student.program_studi || '-'"></p>
                                    </div>
                                </div>
                            </template>

                            <template x-if="selectedUser?.investor">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Perusahaan</label>
                                        <p class="text-gray-900" x-text="selectedUser.investor.company_name || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Posisi</label>
                                        <p class="text-gray-900" x-text="selectedUser.investor.position || '-'"></p>
                                    </div>
                                </div>
                            </template>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bio</label>
                                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-lg" x-text="selectedUser?.bio || 'Belum ada bio'"></p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Bergabung</label>
                                    <p class="text-gray-900 flex items-center gap-2">
                                        <i class="ri-calendar-line text-gray-400"></i>
                                        <span x-text="formatDate(selectedUser?.created_at)"></span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Komentar</label>
                                    <p class="text-gray-900 flex items-center gap-2">
                                        <i class="ri-message-3-line text-gray-400"></i>
                                        <span x-text="selectedUser?.comments_count || 0"></span> komentar
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                                <button @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                                    Tutup
                                </button>
                                <template x-if="!selectedUser?.deleted_at">
                                    <button @click="deleteUser(selectedUser); modalOpen = false" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium">
                                        <i class="ri-delete-bin-line mr-1"></i>
                                        Hapus Pengguna
                                    </button>
                                </template>
                                <template x-if="selectedUser?.deleted_at">
                                    <button @click="restoreUser(selectedUser); modalOpen = false" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium">
                                        <i class="ri-refresh-line mr-1"></i>
                                        Pulihkan
                                    </button>
                                </template>
                                <template x-if="selectedUser?.deleted_at">
                                    <button @click="forceDeleteUser(selectedUser); modalOpen = false" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg transition-colors font-medium">
                                        <i class="ri-delete-bin-2-line mr-1"></i>
                                        Hapus Permanen
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function usersManager() {
            return {
                users: [],
                loading: false,
                modalOpen: false,
                modalMode: 'view',
                selectedUser: null,
                filters: {
                    search: '',
                    role: '',
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
                    this.loadUsers();
                },

                async loadUsers() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        Object.keys(this.filters).forEach(key => {
                            if (this.filters[key]) {
                                params.append(key, this.filters[key]);
                            }
                        });

                        const response = await fetch(`{{ route('admin.users.filter') }}?${params.toString()}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        
                        this.users = data.data;
                        this.pagination = {
                            current_page: data.current_page,
                            last_page: data.last_page,
                            from: data.from || 0,
                            to: data.to || 0,
                            total: data.total
                        };
                    } catch (error) {
                        console.error('Error loading users:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal memuat data pengguna: ' + error.message,
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
                        this.loadUsers();
                    }
                },

                sortBy(field) {
                    if (this.filters.sort_field === field) {
                        this.filters.sort_order = this.filters.sort_order === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.filters.sort_field = field;
                        this.filters.sort_order = 'asc';
                    }
                    this.loadUsers();
                },

                resetFilters() {
                    this.filters = {
                        search: '',
                        role: '',
                        status: '',
                        show_deleted: '',
                        date_from: '',
                        date_to: '',
                        sort_field: 'created_at',
                        sort_order: 'desc',
                        per_page: 10,
                        page: 1
                    };
                    this.loadUsers();
                },

                viewUser(user) {
                    this.selectedUser = user;
                    this.modalMode = 'view';
                    this.modalOpen = true;
                },

                deleteUser(user) {
                    Swal.fire({
                        title: 'Hapus Pengguna?',
                        html: `Apakah Anda yakin ingin menghapus pengguna <strong>"${user.full_name || user.username}"</strong>?<br><small class="text-gray-500">Pengguna akan dipindahkan ke trash dan dapat dipulihkan.</small>`,
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
                                const response = await fetch(`/admin/users/${user.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                                if (response.ok) {
                                    await this.loadUsers();
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Pengguna berhasil dihapus',
                                        icon: 'success',
                                        confirmButtonColor: '#b01116',
                                        timer: 2000
                                    });
                                } else {
                                    throw new Error('Failed to delete');
                                }
                            } catch (error) {
                                console.error('Error deleting user:', error);
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus pengguna',
                                    icon: 'error',
                                    confirmButtonColor: '#b01116'
                                });
                            }
                        }
                    });
                },

                restoreUser(user) {
                    Swal.fire({
                        title: 'Pulihkan Pengguna?',
                        html: `Apakah Anda yakin ingin memulihkan pengguna <strong>"${user.full_name || user.username}"</strong>?`,
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
                                const response = await fetch(`/admin/users/${user.id}/restore`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                                if (response.ok) {
                                    await this.loadUsers();
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Pengguna berhasil dipulihkan',
                                        icon: 'success',
                                        confirmButtonColor: '#16a34a',
                                        timer: 2000
                                    });
                                } else {
                                    throw new Error('Failed to restore');
                                }
                            } catch (error) {
                                console.error('Error restoring user:', error);
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal memulihkan pengguna',
                                    icon: 'error',
                                    confirmButtonColor: '#b01116'
                                });
                            }
                        }
                    });
                },

                forceDeleteUser(user) {
                    Swal.fire({
                        title: 'Hapus Permanen?',
                        html: `<div class="text-left">
                            <p class="mb-3">Apakah Anda yakin ingin menghapus <strong>PERMANEN</strong> pengguna <strong>"${user.full_name || user.username}"</strong>?</p>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                                <p class="text-red-800 text-sm font-semibold mb-2">⚠️ PERINGATAN:</p>
                                <ul class="text-red-700 text-sm space-y-1 ml-4">
                                    <li>• Data pengguna akan dihapus secara permanen</li>
                                    <li>• Semua data terkait akan hilang</li>
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
                                const response = await fetch(`/admin/users/${user.id}/force-delete`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                                if (response.ok) {
                                    await this.loadUsers();
                                    Swal.fire({
                                        title: 'Terhapus!',
                                        text: 'Pengguna berhasil dihapus secara permanen',
                                        icon: 'success',
                                        confirmButtonColor: '#b01116',
                                        timer: 2000
                                    });
                                } else {
                                    throw new Error('Failed to delete');
                                }
                            } catch (error) {
                                console.error('Error force deleting user:', error);
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus pengguna secara permanen',
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
                        day: 'numeric' 
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