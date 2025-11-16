@extends('layout.layout')

@section('title', "Detail Mahasiswa - " . ($student->user->full_name ?? $student->user->username))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ activeTab: 'projects' }">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Column (2 columns width) -->
        <div class="flex-1 lg:w-2/3 lg:order-1 order-2">
            <!-- Navigation Tabs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                <!-- Tab Headers -->
                <div class="flex border-b border-gray-200 overflow-x-auto">
                    <button @click="activeTab = 'projects'" 
                            :class="activeTab === 'projects' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-folder-3-line mr-2"></i>Jelajahi Proyek
                    </button>
                    <button @click="activeTab = 'about'" 
                            :class="activeTab === 'about' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-user-line mr-2"></i>Tentang Mahasiswa
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Projects Tab -->
                    <div x-show="activeTab === 'projects'" x-transition>
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Jelajahi Proyek dari Mahasiswa</h2>
                            <p class="text-gray-600 text-sm">Total {{ $projects->total() + $memberProjects->count() }} proyek telah diselesaikan</p>
                        </div>

                        <!-- Project Type Filter Pills -->
                        <div class="flex flex-wrap gap-3 mb-6">
                            <a href="{{ route('detail.student', $student->user->username) }}" 
                               class="px-5 py-2 rounded-full text-sm font-medium transition-colors {{ !request('type') ? 'bg-[#b01116] text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                                Semua
                            </a>
                            <a href="{{ route('detail.student', ['student' => $student->user->username, 'type' => 'individual']) }}" 
                               class="px-5 py-2 rounded-full text-sm font-medium transition-colors {{ request('type') === 'individual' ? 'bg-[#b01116] text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                                Individual
                            </a>
                            <a href="{{ route('detail.student', ['student' => $student->user->username, 'type' => 'team']) }}" 
                               class="px-5 py-2 rounded-full text-sm font-medium transition-colors {{ request('type') === 'team' ? 'bg-[#b01116] text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                                Tim
                            </a>
                        </div>

                        <!-- Projects Grid (3 columns) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($projects as $project)
                                @include('pages.partials.project-card', ['project' => $project, 'wishlistedProjects' => $wishlistedProjects])
                            @empty
                                @if($memberProjects->isEmpty())
                                    <div class="col-span-full text-center py-12">
                                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="ri-folder-line text-3xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada proyek</h3>
                                        <p class="text-gray-600">Mahasiswa ini belum memiliki proyek yang dipublikasikan.</p>
                                    </div>
                                @endif
                            @endforelse

                            <!-- Member Projects (if not filtering) -->
                            @if(!request('type') || request('type') === 'team')
                                @foreach($memberProjects as $project)
                                    @include('pages.partials.project-card', ['project' => $project, 'wishlistedProjects' => $wishlistedProjects])
                                @endforeach
                            @endif
                        </div>

                        <!-- Pagination -->
                        @if($projects->hasPages())
                        <div class="mt-8">
                            {{ $projects->links() }}
                        </div>
                        @endif
                    </div>

                    <!-- About Tab -->
                    <div x-show="activeTab === 'about'" x-transition>
                        <div class="prose prose-gray max-w-none">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Mahasiswa</h2>
                            
                            @if($student->user->about)
                                <p class="text-gray-600 leading-relaxed mb-6">
                                    {{ $student->user->about }}
                                </p>
                            @else
                                <p class="text-gray-500 italic mb-6">
                                    Mahasiswa ini belum menambahkan deskripsi tentang diri mereka.
                                </p>
                            @endif
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Keahlian</h3>
                            <div class="flex flex-wrap gap-2 mb-6">
                                @forelse($student->expertises as $expertise)
                                    <span class="px-4 py-2 bg-red-50 text-gray-700 text-sm font-medium rounded-full border border-red-100">
                                        {{ $expertise->name }}
                                    </span>
                                @empty
                                    <p class="text-gray-500 text-sm">Belum ada keahlian yang ditambahkan.</p>
                                @endforelse
                            </div>

                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Pendidikan</h3>
                            <div class="space-y-4">
                                @forelse($student->educationInfo->sortByDesc('is_current') as $education)
                                    <div class="border-l-4 border-[#b01116] pl-4">
                                        <h4 class="font-semibold text-gray-800">
                                            {{ $education->degree ? $education->degree . ' ' : '' }}{{ $education->field_of_study }}
                                        </h4>
                                        <p class="text-sm text-gray-600">{{ $education->institution_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $education->period }}</p>
                                        @if($education->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $education->description }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">Belum ada informasi pendidikan.</p>
                                @endforelse
                            </div>

                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Statistik</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ $student->projects()->published()->count() }}</div>
                                    <div class="text-sm text-gray-600">Proyek Pribadi</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ $memberProjects->count() }}</div>
                                    <div class="text-sm text-gray-600">Proyek Tim</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ $student->expertises->count() }}</div>
                                    <div class="text-sm text-gray-600">Keahlian</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Fixed Sidebar) -->
        <div class="lg:w-1/3 lg:order-2 order-1">
            <div class="lg:sticky lg:top-24">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <!-- Profile Picture -->
                    <div class="flex justify-center mb-6">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200">
                            @if($student->user->avatar)
                                <img src="{{ $student->user->avatar_url }}" alt="{{ $student->user->full_name ?? $student->user->username }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-4xl font-bold">
                                    {{ strtoupper(substr($student->user->username, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Student Name -->
                    <h1 class="text-2xl font-bold text-gray-800 text-center mb-2">
                        {{ $student->user->full_name ?? $student->user->username }}
                    </h1>
                    
                    <!-- Username -->
                    <p class="text-sm text-gray-500 text-center mb-4">{{ "@" . $student->user->username }}</p>
                    
                    <!-- Student ID -->
                    <p class="text-sm text-gray-500 text-center mb-4">NIM: {{ $student->student_id }}</p>

                    <!-- Short About -->
                    @if($student->user->short_about)
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-sm text-gray-600 text-center leading-relaxed">
                            {{ Str::limit($student->user->short_about, 500) }}
                        </p>
                    </div>
                    @endif

                    <!-- Contact Information -->
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-gray-800 uppercase mb-4">Kontak Mahasiswa</h3>
                        
                        <!-- Email -->
                        @if($student->user->email)
                        <a href="mailto:{{ $student->user->email }}" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-mail-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm font-medium break-all">{{ $student->user->email }}</p>
                            </div>
                        </a>
                        @endif

                        <!-- Phone -->
                        @if($student->user->phone_number)
                        <a href="tel:{{ $student->user->phone_number }}" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-phone-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Telepon</p>
                                <p class="text-sm font-medium">{{ $student->user->phone_number }}</p>
                            </div>
                        </a>
                        @endif

                        @if(!$student->user->email && !$student->user->phone_number)
                        <p class="text-sm text-gray-500 text-center py-4">
                            Tidak ada informasi kontak yang tersedia.
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Wishlist functionality for investors
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.wishlist-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const button = form.querySelector('button');
            const icon = button.querySelector('i');
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    if (data.isWishlisted) {
                        icon.classList.remove('ri-heart-line', 'text-gray-600');
                        icon.classList.add('ri-heart-fill', 'text-[#b01116]');
                    } else {
                        icon.classList.remove('ri-heart-fill', 'text-[#b01116]');
                        icon.classList.add('ri-heart-line', 'text-gray-600');
                    }
                    
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
                console.error('Error:', error);
            }
        });
    });
});
</script>
@endsection
