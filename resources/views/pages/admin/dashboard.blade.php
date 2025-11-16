@extends('layout.admin-layout')

@section('title', 'Dashboard Admin')

@section('content')
<div class="p-4 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Selamat datang kembali, Admin! Berikut ringkasan sistem Anda.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <!-- Projects Card -->
        <div x-data="{ count: 0 }" 
             x-init="setTimeout(() => { let interval = setInterval(() => { count++; if(count >= 24) clearInterval(interval); }, 30) }, 200)"
             class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-[#b01116]/10 rounded-lg flex items-center justify-center">
                    <i class="ri-folder-line text-2xl text-[#b01116]"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+12%</span>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Total Proyek</h3>
            <p class="text-3xl font-bold text-gray-900" x-text="count"></p>
            <p class="text-xs text-gray-500 mt-2">5 proyek baru bulan ini</p>
        </div>

        <!-- Users Card -->
        <div x-data="{ count: 0 }" 
             x-init="setTimeout(() => { let interval = setInterval(() => { count += 3; if(count >= 156) { count = 156; clearInterval(interval); } }, 20) }, 200)"
             class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="ri-user-line text-2xl text-blue-600"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+8%</span>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Total Users</h3>
            <p class="text-3xl font-bold text-gray-900" x-text="count"></p>
            <p class="text-xs text-gray-500 mt-2">23 user baru minggu ini</p>
        </div>

        <!-- Comments Card -->
        <div x-data="{ count: 0 }" 
             x-init="setTimeout(() => { let interval = setInterval(() => { count++; if(count >= 342) clearInterval(interval); }, 5) }, 200)"
             class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="ri-message-3-line text-2xl text-purple-600"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+15%</span>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Total Komentar</h3>
            <p class="text-3xl font-bold text-gray-900" x-text="count"></p>
            <p class="text-xs text-gray-500 mt-2">8 komentar perlu review</p>
        </div>

        <!-- Wishlist Card -->
        <div x-data="{ count: 0 }" 
             x-init="setTimeout(() => { let interval = setInterval(() => { count++; if(count >= 89) clearInterval(interval); }, 15) }, 200)"
             class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="ri-heart-line text-2xl text-amber-600"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+20%</span>
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Total Wishlist</h3>
            <p class="text-3xl font-bold text-gray-900" x-text="count"></p>
            <p class="text-xs text-gray-500 mt-2">Dari 67 users</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Projects -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Proyek Terbaru</h2>
                <a href="#" class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Semua →</a>
            </div>
            <div class="space-y-4">
                @foreach([
                    ['title' => 'Sistem Informasi Perpustakaan', 'author' => 'Ahmad Fauzi', 'date' => '2 hari lalu', 'status' => 'published', 'views' => 124],
                    ['title' => 'Aplikasi E-Commerce Mobile', 'author' => 'Siti Nurhaliza', 'date' => '3 hari lalu', 'status' => 'published', 'views' => 89],
                    ['title' => 'Website Portfolio Interaktif', 'author' => 'Budi Santoso', 'date' => '5 hari lalu', 'status' => 'pending', 'views' => 56],
                    ['title' => 'Dashboard Analytics Real-time', 'author' => 'Dewi Lestari', 'date' => '1 minggu lalu', 'status' => 'published', 'views' => 203],
                ] as $project)
                <div class="flex items-start gap-4 p-4 rounded-lg hover:bg-gray-50 transition-colors group cursor-pointer">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#b01116] to-[#8d0d11] rounded-lg flex-shrink-0 flex items-center justify-center">
                        <i class="ri-folder-line text-2xl text-white"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 group-hover:text-[#b01116] transition-colors truncate">{{ $project['title'] }}</h3>
                        <p class="text-sm text-gray-600 mt-1">oleh {{ $project['author'] }}</p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <i class="ri-time-line"></i>
                                {{ $project['date'] }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="ri-eye-line"></i>
                                {{ $project['views'] }} views
                            </span>
                            <span class="px-2 py-1 rounded-full {{ $project['status'] === 'published' ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">
                                {{ $project['status'] === 'published' ? 'Published' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Access Menu -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Akses Cepat</h2>
            <div class="space-y-3">
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-[#b01116]/10 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-add-line text-xl text-[#b01116] group-hover:text-white"></i>
                    </div>
                    <span class="font-medium">Tambah Proyek</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-blue-50 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-user-add-line text-xl text-blue-600 group-hover:text-white"></i>
                    </div>
                    <span class="font-medium">Kelola Users</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-purple-50 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-message-3-line text-xl text-purple-600 group-hover:text-white"></i>
                    </div>
                    <div class="flex-1">
                        <span class="font-medium">Review Komentar</span>
                        <span class="ml-2 bg-[#b01116] text-white text-xs px-2 py-0.5 rounded-full">8</span>
                    </div>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-amber-50 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-heart-line text-xl text-amber-600 group-hover:text-white"></i>
                    </div>
                    <span class="font-medium">Lihat Wishlist</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-green-50 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-article-line text-xl text-green-600 group-hover:text-white"></i>
                    </div>
                    <span class="font-medium">Kelola Blog</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-indigo-50 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-question-answer-line text-xl text-indigo-600 group-hover:text-white"></i>
                    </div>
                    <span class="font-medium">Kelola Q&A</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">User Terbaru</h2>
                <a href="#" class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Semua →</a>
            </div>
            <div class="space-y-4">
                @foreach([
                    ['name' => 'Ahmad Fauzi', 'email' => 'ahmad.fauzi@example.com', 'role' => 'Mahasiswa', 'date' => '1 jam lalu', 'avatar' => 'A'],
                    ['name' => 'Siti Nurhaliza', 'email' => 'siti.nur@example.com', 'role' => 'Mahasiswa', 'date' => '3 jam lalu', 'avatar' => 'S'],
                    ['name' => 'Budi Santoso', 'email' => 'budi.s@example.com', 'role' => 'Dosen', 'date' => '5 jam lalu', 'avatar' => 'B'],
                    ['name' => 'Dewi Lestari', 'email' => 'dewi.lestari@example.com', 'role' => 'Mahasiswa', 'date' => '1 hari lalu', 'avatar' => 'D'],
                ] as $index => $user)
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold" 
                         style="background: linear-gradient(135deg, {{ ['#b01116', '#3b82f6', '#8b5cf6', '#f59e0b'][$index] }} 0%, {{ ['#8d0d11', '#2563eb', '#7c3aed', '#d97706'][$index] }} 100%)">
                        {{ $user['avatar'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900">{{ $user['name'] }}</h3>
                        <p class="text-sm text-gray-600 truncate">{{ $user['email'] }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-1 rounded-full {{ $user['role'] === 'Dosen' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600' }}">
                            {{ $user['role'] }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $user['date'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Comments & Activity Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Komentar Terbaru</h2>
                <a href="#" class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Semua →</a>
            </div>
            <div class="space-y-4">
                @foreach([
                    ['author' => 'Ahmad Fauzi', 'project' => 'Sistem Informasi Perpustakaan', 'comment' => 'Proyek yang sangat menarik! Apakah sudah ada dokumentasi lengkap?', 'time' => '30 menit lalu', 'status' => 'pending'],
                    ['author' => 'Siti Nurhaliza', 'project' => 'Aplikasi E-Commerce Mobile', 'comment' => 'Desain UI/UX-nya sangat bagus, sangat user-friendly!', 'time' => '2 jam lalu', 'status' => 'approved'],
                    ['author' => 'Budi Santoso', 'project' => 'Website Portfolio Interaktif', 'comment' => 'Animasinya smooth, performa website juga cepat.', 'time' => '4 jam lalu', 'status' => 'approved'],
                    ['author' => 'Dewi Lestari', 'project' => 'Dashboard Analytics Real-time', 'comment' => 'Fitur real-time nya impressive, bagaimana implementasinya?', 'time' => '6 jam lalu', 'status' => 'pending'],
                ] as $comment)
                <div class="p-4 rounded-lg border border-gray-200 hover:border-[#b01116] transition-colors">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $comment['author'] }}</h3>
                        <span class="text-xs px-2 py-1 rounded-full {{ $comment['status'] === 'approved' ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">
                            {{ $comment['status'] === 'approved' ? 'Approved' : 'Pending' }}
                        </span>
                    </div>
                    <p class="text-xs text-[#b01116] mb-2">pada {{ $comment['project'] }}</p>
                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $comment['comment'] }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $comment['time'] }}</span>
                        @if($comment['status'] === 'pending')
                        <div class="flex gap-2">
                            <button class="text-xs text-green-600 hover:text-green-700 font-medium">Approve</button>
                            <button class="text-xs text-red-600 hover:text-red-700 font-medium">Reject</button>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection