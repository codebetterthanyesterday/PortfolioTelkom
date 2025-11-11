@extends('layout.admin-layout')

@section('title', "Kelola Pengguna")

@section('content')
<div class="p-4 lg:p-8" x-data="usersManager()">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Kelola Pengguna</h1>
                <p class="text-gray-600 mt-1">Kelola semua pengguna yang terdaftar di sistem</p>
            </div>
            <button @click="openModal('add')" class="inline-flex items-center gap-2 px-4 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg transition-colors font-medium">
                <i class="ri-user-add-line text-xl"></i>
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search Box -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pengguna</label>
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        x-model="search"
                        placeholder="Cari nama, email, atau NIM..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select x-model="roleFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="dosen">Dosen</option>
                    <option value="mahasiswa">Mahasiswa</option>
                </select>
            </div>

            <!-- Sort -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                <select x-model="sortOrder" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="name-asc">Nama A-Z</option>
                    <option value="name-desc">Nama Z-A</option>
                </select>
            </div>
        </div>

        <!-- Stats & Reset -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <div class="flex items-end">
                <button @click="resetFilters()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                    <i class="ri-refresh-line mr-2"></i>Reset Filter
                </button>
            </div>

            <div class="lg:col-span-3 flex items-end">
                <div class="w-full px-4 py-2 bg-gray-50 rounded-lg text-sm text-gray-600 flex items-center justify-between">
                    <span>
                        <span class="font-semibold text-gray-900" x-text="filteredUsers.length"></span> pengguna ditemukan
                    </span>
                    <div class="flex gap-2 text-xs">
                        <span class="px-2 py-1 bg-purple-50 text-purple-600 rounded">
                            <i class="ri-admin-line"></i>
                            <span x-text="users.filter(u => u.role === 'admin').length"></span> Admin
                        </span>
                        <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded">
                            <i class="ri-user-line"></i>
                            <span x-text="users.filter(u => u.role === 'dosen').length"></span> Dosen
                        </span>
                        <span class="px-2 py-1 bg-green-50 text-green-600 rounded">
                            <i class="ri-graduation-cap-line"></i>
                            <span x-text="users.filter(u => u.role === 'mahasiswa').length"></span> Mahasiswa
                        </span>
                    </div>
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

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bergabung</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(user, index) in paginatedUsers" :key="user.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div 
                                        class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-semibold"
                                        :style="`background: linear-gradient(135deg, ${user.color1} 0%, ${user.color2} 100%)`"
                                        x-text="user.name.charAt(0)">
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900" x-text="user.name"></p>
                                        <p class="text-xs text-gray-500" x-text="user.nim || '-'"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600" x-text="user.email"></td>
                            <td class="px-6 py-4">
                                <span 
                                    class="text-xs px-2 py-1 rounded-full font-medium"
                                    :class="{
                                        'bg-purple-50 text-purple-600': user.role === 'admin',
                                        'bg-blue-50 text-blue-600': user.role === 'dosen',
                                        'bg-green-50 text-green-600': user.role === 'mahasiswa'
                                    }">
                                    <i :class="{
                                        'ri-admin-line': user.role === 'admin',
                                        'ri-user-line': user.role === 'dosen',
                                        'ri-graduation-cap-line': user.role === 'mahasiswa'
                                    }"></i>
                                    <span x-text="user.role.charAt(0).toUpperCase() + user.role.slice(1)"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600" x-text="user.joined_date"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="viewUser(user)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                        <i class="ri-eye-line text-lg"></i>
                                    </button>
                                    <button @click="editUser(user)" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                        <i class="ri-edit-line text-lg"></i>
                                    </button>
                                    <button @click="deleteUser(user)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
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
                    sampai <span class="font-semibold" x-text="Math.min(currentPage * perPage, filteredUsers.length)"></span> 
                    dari <span class="font-semibold" x-text="filteredUsers.length"></span> pengguna
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
                            <div 
                                class="w-24 h-24 rounded-full flex items-center justify-center text-white text-3xl font-semibold mb-4"
                                :style="`background: linear-gradient(135deg, ${selectedUser?.color1} 0%, ${selectedUser?.color2} 100%)`"
                                x-text="selectedUser?.name.charAt(0)">
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900" x-text="selectedUser?.name"></h2>
                            <span 
                                class="mt-2 text-sm px-3 py-1 rounded-full font-medium"
                                :class="{
                                    'bg-purple-50 text-purple-600': selectedUser?.role === 'admin',
                                    'bg-blue-50 text-blue-600': selectedUser?.role === 'dosen',
                                    'bg-green-50 text-green-600': selectedUser?.role === 'mahasiswa'
                                }">
                                <i :class="{
                                    'ri-admin-line': selectedUser?.role === 'admin',
                                    'ri-user-line': selectedUser?.role === 'dosen',
                                    'ri-graduation-cap-line': selectedUser?.role === 'mahasiswa'
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
                                <label class="block text-sm font-semibold text-gray-700 mb-2">No. Telepon</label>
                                <p class="text-gray-900 flex items-center gap-2">
                                    <i class="ri-phone-line text-gray-400"></i>
                                    <span x-text="selectedUser?.phone || '-'"></span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" x-show="selectedUser?.role === 'mahasiswa'">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">NIM</label>
                                <p class="text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded-lg" x-text="selectedUser?.nim || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jurusan</label>
                                <p class="text-gray-900" x-text="selectedUser?.major || '-'"></p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                            <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-lg" x-text="selectedUser?.address || '-'"></p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Bergabung</label>
                                <p class="text-gray-900 flex items-center gap-2">
                                    <i class="ri-calendar-line text-gray-400"></i>
                                    <span x-text="selectedUser?.joined_date"></span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                <span 
                                    class="inline-block text-sm px-3 py-1 rounded-lg font-medium"
                                    :class="selectedUser?.status === 'active' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'">
                                    <i :class="selectedUser?.status === 'active' ? 'ri-check-circle-line' : 'ri-close-circle-line'"></i>
                                    <span x-text="selectedUser?.status === 'active' ? 'Aktif' : 'Nonaktif'"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <button @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                                Tutup
                            </button>
                            <button @click="editUser(selectedUser)" class="px-4 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg transition-colors font-medium">
                                <i class="ri-edit-line mr-1"></i>
                                Edit Pengguna
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="modalMode === 'edit' || modalMode === 'add'">
                    <form @submit.prevent="saveUser()" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                            <input 
                                type="text" 
                                x-model="formData.name"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                                <input 
                                    type="email" 
                                    x-model="formData.email"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">No. Telepon</label>
                                <input 
                                    type="tel" 
                                    x-model="formData.phone"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Role *</label>
                                <select 
                                    x-model="formData.role"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                    <option value="">Pilih Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                                <select 
                                    x-model="formData.status"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="formData.role === 'mahasiswa'">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">NIM</label>
                                <input 
                                    type="text" 
                                    x-model="formData.nim"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jurusan</label>
                                <input 
                                    type="text" 
                                    x-model="formData.major"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                            <textarea 
                                x-model="formData.address"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#b01116] focus:border-transparent"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="button" @click="modalOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors font-medium">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg transition-colors font-medium">
                                <span x-text="modalMode === 'edit' ? 'Simpan Perubahan' : 'Tambah Pengguna'"></span>
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
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Hapus Pengguna?</h3>
                <p class="text-gray-600 text-center mb-6">Apakah Anda yakin ingin menghapus pengguna "<span x-text="selectedUser?.name" class="font-semibold"></span>"? Aksi ini tidak dapat dibatalkan.</p>
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
    function usersManager() {
        return {
            search: '',
            roleFilter: '',
            sortOrder: 'latest',
            currentPage: 1,
            perPage: 10,
            modalOpen: false,
            deleteModalOpen: false,
            modalMode: 'view', // 'view', 'edit', 'add'
            selectedUser: null,
            formData: {},
            users: [
                { id: 1, name: 'Ahmad Fauzi', email: 'ahmad.fauzi@example.com', phone: '081234567890', role: 'mahasiswa', nim: '2021001', major: 'Teknik Informatika', address: 'Jl. Sudirman No. 123, Jakarta', joined_date: '2024-01-15', status: 'active', color1: '#b01116', color2: '#8d0d11' },
                { id: 2, name: 'Dr. Budi Santoso', email: 'budi.santoso@example.com', phone: '082345678901', role: 'dosen', nim: null, major: null, address: 'Jl. Thamrin No. 45, Jakarta', joined_date: '2023-08-20', status: 'active', color1: '#3b82f6', color2: '#2563eb' },
                { id: 3, name: 'Siti Nurhaliza', email: 'siti.nur@example.com', phone: '083456789012', role: 'mahasiswa', nim: '2021002', major: 'Sistem Informasi', address: 'Jl. Gatot Subroto No. 78, Bandung', joined_date: '2024-02-10', status: 'active', color1: '#8b5cf6', color2: '#7c3aed' },
                { id: 4, name: 'Admin System', email: 'admin@example.com', phone: '084567890123', role: 'admin', nim: null, major: null, address: 'Jl. HR Rasuna Said No. 1, Jakarta', joined_date: '2023-01-01', status: 'active', color1: '#f59e0b', color2: '#d97706' },
                { id: 5, name: 'Dewi Lestari', email: 'dewi.lestari@example.com', phone: '085678901234', role: 'mahasiswa', nim: '2021003', major: 'Teknik Informatika', address: 'Jl. Ahmad Yani No. 234, Surabaya', joined_date: '2024-03-05', status: 'active', color1: '#10b981', color2: '#059669' },
                { id: 6, name: 'Prof. Joko Widodo', email: 'joko.widodo@example.com', phone: '086789012345', role: 'dosen', nim: null, major: null, address: 'Jl. Diponegoro No. 56, Semarang', joined_date: '2023-06-15', status: 'active', color1: '#ef4444', color2: '#dc2626' },
                { id: 7, name: 'Rina Susanti', email: 'rina.susanti@example.com', phone: '087890123456', role: 'mahasiswa', nim: '2021004', major: 'Sistem Informasi', address: 'Jl. Pahlawan No. 89, Yogyakarta', joined_date: '2024-04-12', status: 'active', color1: '#06b6d4', color2: '#0891b2' },
                { id: 8, name: 'Agus Salim', email: 'agus.salim@example.com', phone: '088901234567', role: 'mahasiswa', nim: '2021005', major: 'Teknik Informatika', address: 'Jl. Veteran No. 12, Malang', joined_date: '2024-05-20', status: 'inactive', color1: '#ec4899', color2: '#db2777' },
                { id: 9, name: 'Dr. Putri Maharani', email: 'putri.maharani@example.com', phone: '089012345678', role: 'dosen', nim: null, major: null, address: 'Jl. Merdeka No. 67, Medan', joined_date: '2023-09-10', status: 'active', color1: '#14b8a6', color2: '#0d9488' },
                { id: 10, name: 'Bambang Sutrisno', email: 'bambang.s@example.com', phone: '081112345678', role: 'mahasiswa', nim: '2021006', major: 'Teknik Komputer', address: 'Jl. Pemuda No. 90, Palembang', joined_date: '2024-06-08', status: 'active', color1: '#f97316', color2: '#ea580c' },
                { id: 11, name: 'Maya Sari', email: 'maya.sari@example.com', phone: '082223456789', role: 'mahasiswa', nim: '2021007', major: 'Sistem Informasi', address: 'Jl. Kartini No. 45, Makassar', joined_date: '2024-07-14', status: 'active', color1: '#a855f7', color2: '#9333ea' },
                { id: 12, name: 'Dedi Kurniawan', email: 'dedi.k@example.com', phone: '083334567890', role: 'mahasiswa', nim: '2021008', major: 'Teknik Informatika', address: 'Jl. Gajah Mada No. 23, Denpasar', joined_date: '2024-08-22', status: 'active', color1: '#84cc16', color2: '#65a30d' },
            ],

            get filteredUsers() {
                let filtered = this.users;

                // Search filter
                if (this.search) {
                    const searchLower = this.search.toLowerCase();
                    filtered = filtered.filter(u => 
                        u.name.toLowerCase().includes(searchLower) ||
                        u.email.toLowerCase().includes(searchLower) ||
                        (u.nim && u.nim.toLowerCase().includes(searchLower))
                    );
                }

                // Role filter
                if (this.roleFilter) {
                    filtered = filtered.filter(u => u.role === this.roleFilter);
                }

                // Sort
                filtered.sort((a, b) => {
                    switch(this.sortOrder) {
                        case 'latest':
                            return new Date(b.joined_date) - new Date(a.joined_date);
                        case 'oldest':
                            return new Date(a.joined_date) - new Date(b.joined_date);
                        case 'name-asc':
                            return a.name.localeCompare(b.name);
                        case 'name-desc':
                            return b.name.localeCompare(a.name);
                        default:
                            return 0;
                    }
                });

                return filtered;
            },

            get paginatedUsers() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.filteredUsers.slice(start, end);
            },

            get totalPages() {
                return Math.ceil(this.filteredUsers.length / this.perPage);
            },

            resetFilters() {
                this.search = '';
                this.roleFilter = '';
                this.sortOrder = 'latest';
                this.currentPage = 1;
            },

            openModal(mode) {
                this.modalMode = mode;
                this.modalOpen = true;
                if (mode === 'add') {
                    this.formData = {
                        name: '',
                        email: '',
                        phone: '',
                        role: '',
                        nim: '',
                        major: '',
                        address: '',
                        status: 'active'
                    };
                }
            },

            viewUser(user) {
                this.selectedUser = user;
                this.modalMode = 'view';
                this.modalOpen = true;
            },

            editUser(user) {
                this.selectedUser = user;
                this.formData = { ...user };
                this.modalMode = 'edit';
                this.modalOpen = true;
            },

            deleteUser(user) {
                this.selectedUser = user;
                this.deleteModalOpen = true;
            },

            saveUser() {
                if (this.modalMode === 'add') {
                    // Add new user with random gradient colors
                    const colors = [
                        ['#b01116', '#8d0d11'], ['#3b82f6', '#2563eb'], ['#8b5cf6', '#7c3aed'],
                        ['#f59e0b', '#d97706'], ['#10b981', '#059669'], ['#ef4444', '#dc2626']
                    ];
                    const randomColor = colors[Math.floor(Math.random() * colors.length)];
                    const newId = Math.max(...this.users.map(u => u.id)) + 1;
                    const newUser = {
                        id: newId,
                        ...this.formData,
                        joined_date: new Date().toISOString().split('T')[0],
                        color1: randomColor[0],
                        color2: randomColor[1]
                    };
                    this.users.unshift(newUser);
                    alert('Pengguna berhasil ditambahkan!');
                } else if (this.modalMode === 'edit') {
                    // Update existing user
                    const index = this.users.findIndex(u => u.id === this.selectedUser.id);
                    if (index !== -1) {
                        this.users[index] = { ...this.users[index], ...this.formData };
                    }
                    alert('Pengguna berhasil diperbarui!');
                }
                this.modalOpen = false;
            },

            confirmDelete() {
                const index = this.users.findIndex(u => u.id === this.selectedUser.id);
                if (index !== -1) {
                    this.users.splice(index, 1);
                    alert('Pengguna berhasil dihapus!');
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