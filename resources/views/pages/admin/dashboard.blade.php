@extends('layout.admin-layout')

@section('title', 'Dashboard Admin')

@section('content')
<div class="p-4 lg:p-8 bg-gray-50">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Selamat datang, {{ auth()->user()->full_name ?? 'Admin' }}! Berikut ringkasan Telkom Project Gallery.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <!-- Projects Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-[#b01116]/10 rounded-lg flex items-center justify-center">
                    <i class="ri-folder-line text-2xl text-[#b01116]"></i>
                </div>
                @if($growth['projects'] != 0)
                <span class="text-xs font-medium {{ $growth['projects'] > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} px-2 py-1 rounded-full">
                    {{ $growth['projects'] > 0 ? '+' : '' }}{{ $growth['projects'] }}%
                </span>
                @endif
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Total Proyek</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_projects'] }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $stats['published_projects'] }} proyek dipublikasikan</p>
        </div>

        <!-- Users Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="ri-user-line text-2xl text-blue-600"></i>
                </div>
                @if($growth['users'] != 0)
                <span class="text-xs font-medium {{ $growth['users'] > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} px-2 py-1 rounded-full">
                    {{ $growth['users'] > 0 ? '+' : '' }}{{ $growth['users'] }}%
                </span>
                @endif
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Total Users</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $stats['total_students'] }} siswa, {{ $stats['total_investors'] }} investor</p>
        </div>

        <!-- Comments Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="ri-message-3-line text-2xl text-purple-600"></i>
                </div>
                @if($growth['comments'] != 0)
                <span class="text-xs font-medium {{ $growth['comments'] > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} px-2 py-1 rounded-full">
                    {{ $growth['comments'] > 0 ? '+' : '' }}{{ $growth['comments'] }}%
                </span>
                @endif
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Total Komentar</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_comments'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Dari semua proyek</p>
        </div>

        <!-- Wishlist Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="ri-heart-line text-2xl text-amber-600"></i>
                </div>
                @if($growth['wishlists'] != 0)
                <span class="text-xs font-medium {{ $growth['wishlists'] > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} px-2 py-1 rounded-full">
                    {{ $growth['wishlists'] > 0 ? '+' : '' }}{{ $growth['wishlists'] }}%
                </span>
                @endif
            </div>
            <h3 class="text-gray-600 text-sm font-medium mb-1">Total Wishlist</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_wishlists'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Proyek favorit investor</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Projects -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Proyek Terbaru</h2>
                <a href="{{ route('admin.projects') }}" class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Semua →</a>
            </div>
            
            @if($recent_projects->count() > 0)
            <div class="space-y-4">
                @foreach($recent_projects as $project)
                <div class="flex items-start gap-4 p-4 rounded-lg hover:bg-gray-50 transition-colors group cursor-pointer">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#b01116] to-[#8d0d11] rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden">
                        @if($project->media->first())
                            <img src="{{ asset('storage/' . $project->media->first()->file_path) }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
                        @else
                            <i class="ri-folder-line text-2xl text-white"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 group-hover:text-[#b01116] transition-colors truncate">{{ $project->title }}</h3>
                        <p class="text-sm text-gray-600 mt-1">oleh {{ $project->student->user->full_name ?? $project->student->user->username }}</p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <i class="ri-time-line"></i>
                                {{ $project->created_at->diffForHumans() }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="ri-message-3-line"></i>
                                {{ $project->comments_count }} komentar
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="ri-heart-line"></i>
                                {{ $project->wishlists_count }} wishlist
                            </span>
                            <span class="px-2 py-1 rounded-full {{ $project->status === 'published' ? 'bg-green-50 text-green-600' : ($project->status === 'archived' ? 'bg-red-50 text-red-600' : 'bg-yellow-50 text-yellow-600') }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 text-gray-500">
                <i class="ri-folder-line text-5xl mb-3"></i>
                <p>Belum ada proyek yang dibuat</p>
            </div>
            @endif
        </div>

        <!-- Quick Access Menu -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Akses Cepat</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.projects') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-[#b01116]/10 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-folder-line text-xl text-[#b01116] group-hover:text-white"></i>
                    </div>
                    <span class="font-medium">Kelola Proyek</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-blue-50 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-user-line text-xl text-blue-600 group-hover:text-white"></i>
                    </div>
                    <span class="font-medium">Kelola Users</span>
                </a>
                <a href="{{ route('admin.comments') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-purple-50 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-message-3-line text-xl text-purple-600 group-hover:text-white"></i>
                    </div>
                    <span class="font-medium">Kelola Komentar</span>
                </a>
                <a href="{{ route('admin.wishlist') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-amber-50 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-heart-line text-xl text-amber-600 group-hover:text-white"></i>
                    </div>
                    <span class="font-medium">Lihat Wishlist</span>
                </a>
                <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg hover:bg-[#b01116] hover:text-white text-gray-700 transition-all group">
                    <div class="w-10 h-10 bg-green-50 group-hover:bg-white/20 rounded-lg flex items-center justify-center transition-colors">
                        <i class="ri-external-link-line text-xl text-green-600 group-hover:text-white"></i>
                    </div>
                    <span class="font-medium">Lihat Website</span>
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
                <a href="{{ route('admin.users') }}" class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Semua →</a>
            </div>
            
            @if($recent_users->count() > 0)
            <div class="space-y-4">
                @foreach($recent_users as $user)
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center overflow-hidden bg-gradient-to-br from-[#b01116] to-[#8d0d11]">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->username }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-white font-semibold text-lg">{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900">{{ $user->full_name ?? $user->username }}</h3>
                        <p class="text-sm text-gray-600 truncate">{{ $user->email }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-1 rounded-full {{ $user->role === 'student' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 text-gray-500">
                <i class="ri-user-line text-5xl mb-3"></i>
                <p>Belum ada user terdaftar</p>
            </div>
            @endif
        </div>

        <!-- Recent Comments -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Komentar Terbaru</h2>
                <a href="{{ route('admin.comments') }}" class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Semua →</a>
            </div>
            
            @if($recent_comments->count() > 0)
            <div class="space-y-4">
                @foreach($recent_comments as $comment)
                <div class="p-4 rounded-lg border border-gray-200 hover:border-[#b01116] transition-colors">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $comment->user->full_name ?? $comment->user->username }}</h3>
                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-[#b01116] mb-2">pada {{ $comment->project->title }}</p>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $comment->comment }}</p>
                    <div class="flex items-center gap-2 mt-3">
                        <form action="{{ route('admin.comments.delete', $comment) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus komentar ini?')" class="text-xs text-red-600 hover:text-red-700 font-medium">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 text-gray-500">
                <i class="ri-message-3-line text-5xl mb-3"></i>
                <p>Belum ada komentar</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection