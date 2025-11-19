@extends('layout.layout')

@section('title', "Tempat Sampah Proyek")

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 xl:px-8 py-4 sm:py-6 lg:py-8">
        <!-- Header Section -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-start lg:items-center lg:justify-between gap-4 sm:gap-6">
                <div class="flex-1">
                    <div class="flex items-start sm:items-center gap-3 mb-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-[#b01116] to-[#8d0d11] rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="ri-delete-bin-line text-xl sm:text-2xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tempat Sampah</h1>
                            <p class="text-base sm:text-lg text-gray-600">Kelola proyek yang dihapus</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="bg-gray-50 px-3 sm:px-4 py-2 rounded-lg">
                        <span class="text-xs sm:text-sm font-medium text-gray-600">Total: {{ $allTrashedProjects->count() }} proyek</span>
                    </div>
                    <a href="{{ route('student.profile') }}" 
                       class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#b01116] to-[#8d0d11] hover:from-[#8d0d11] hover:to-[#6b0a0e] text-white font-semibold px-4 sm:px-6 py-3 rounded-xl transition-all duration-200 hover:scale-105 shadow-lg text-sm sm:text-base">
                        <i class="ri-arrow-left-line text-base sm:text-lg"></i>
                        <span class="hidden sm:inline">Kembali</span>
                        <span class="sm:hidden">Kembali ke Profil</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Session Messages -->
        @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition
                 x-init="setTimeout(() => show = false, 5000)"
                 class="mb-4 sm:mb-6 bg-white border-l-4 border-green-500 shadow-sm rounded-lg p-4 sm:p-6">
                <div class="flex items-start sm:items-center justify-between gap-3">
                    <div class="flex items-start sm:items-center gap-3 flex-1">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="ri-checkbox-circle-line text-lg sm:text-xl text-green-600"></i>
                        </div>
                        <span class="text-gray-800 font-medium text-sm sm:text-base">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                        <i class="ri-close-line text-lg sm:text-xl"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition
                 x-init="setTimeout(() => show = false, 5000)"
                 class="mb-4 sm:mb-6 bg-white border-l-4 border-red-500 shadow-sm rounded-lg p-4 sm:p-6">
                <div class="flex items-start sm:items-center justify-between gap-3">
                    <div class="flex items-start sm:items-center gap-3 flex-1">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="ri-error-warning-line text-lg sm:text-xl text-red-600"></i>
                        </div>
                        <span class="text-gray-800 font-medium text-sm sm:text-base">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                        <i class="ri-close-line text-lg sm:text-xl"></i>
                    </button>
                </div>
            </div>
        @endif

        @if($allTrashedProjects->count() > 0)

            <!-- Projects List -->
            <div class="space-y-4 sm:space-y-6">
                @foreach($allTrashedProjects as $project)
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-100">
                    <div class="flex flex-col lg:flex-row">
                        <!-- Project Image -->
                        <div class="w-full lg:w-80 h-48 sm:h-56 lg:h-auto bg-gray-100 relative overflow-hidden">
                            @if($project->media && $project->media->isNotEmpty())
                                <img src="{{ asset('storage/' . $project->media->first()->file_path) }}" 
                                     alt="{{ $project->title }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="ri-image-line text-4xl sm:text-6xl text-gray-300"></i>
                                </div>
                            @endif
                            
                            <!-- Overlay with badges -->
                            <div class="absolute inset-0 bg-black/20">
                                <div class="absolute top-3 sm:top-4 left-3 sm:left-4">
                                    <span class="bg-gradient-to-r from-[#b01116] to-[#8d0d11] text-white/80 text-xs sm:text-sm font-bold px-2 sm:px-3 py-1 sm:py-1.5 rounded-full flex items-center gap-1 sm:gap-2">
                                        <i class="ri-delete-bin-line text-sm"></i>
                                        <span class="hidden xs:inline">DIHAPUS</span>
                                    </span>
                                </div>
                                <div class="absolute top-3 sm:top-4 right-3 sm:right-4">
                                    <span class="bg-gradient-to-r from-[#b01116] text-white/80 to-[#8d0d11] text-xs sm:text-sm font-semibold px-2 sm:px-3 py-1 sm:py-1.5 rounded-full">
                                        {{ $project->type === 'team' ? 'TIM' : 'INDIVIDU' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Project Content -->
                        <div class="flex-1 p-4 sm:p-6 lg:p-8">
                            <div class="flex flex-col h-full">
                                <!-- Categories -->
                                <div class="flex flex-wrap gap-1.5 sm:gap-2 mb-3 sm:mb-4">
                                    @foreach($project->categories->take(3) as $category)
                                    <span class="text-xs sm:text-sm px-2 sm:px-3 py-1 bg-gray-100 text-gray-700 rounded-md sm:rounded-lg font-medium">
                                        {{ $category->name }}
                                    </span>
                                    @endforeach
                                </div>

                                <!-- Title and Description -->
                                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $project->title }}</h3>
                                
                                <!-- Info Grid -->
                                <div class="grid grid-cols-1 gap-2 sm:gap-3 lg:gap-4 mb-4 sm:mb-6">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <i class="ri-time-line text-[#b01116] text-sm sm:text-base"></i>
                                        <span class="text-xs sm:text-sm">Dihapus {{ $project->deleted_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <i class="ri-calendar-line text-sm sm:text-base"></i>
                                        <span class="text-xs sm:text-sm">Dibuat {{ $project->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>

                                <!-- Team Leader Info -->
                                @if($project->type === 'team')
                                    @php
                                        $leader = $project->members->where('role', 'leader')->first();
                                    @endphp
                                    @if($leader)
                                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-purple-50 rounded-lg sm:rounded-xl border border-purple-200">
                                        <div class="flex items-center gap-2">
                                            <i class="ri-shield-star-line text-purple-600 text-sm sm:text-base"></i>
                                            <span class="text-xs sm:text-sm font-medium text-purple-800">
                                                Leader: {{ $leader->student->user->full_name ?? $leader->student->user->username }}
                                            </span>
                                        </div>
                                    </div>
                                    @endif
                                @endif

                                <!-- Action Buttons -->
                                <div class="mt-auto">
                                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                                        <!-- Restore Button -->
                                        <form action="{{ route('student.projects.restore', $project->id) }}" 
                                              method="POST" 
                                              class="flex-1"
                                              onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan proyek ini?')">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg sm:rounded-xl transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 shadow-lg text-sm sm:text-base">
                                                <i class="ri-arrow-go-back-line text-sm sm:text-lg"></i>
                                                <span class="hidden xs:inline">Kembalikan</span>
                                                <span class="xs:hidden">Pulihkan</span>
                                                <span class="hidden sm:inline">Proyek</span>
                                            </button>
                                        </form>

                                        <!-- Force Delete Button -->
                                        <form action="{{ route('student.projects.force-delete', $project->id) }}" 
                                              method="POST" 
                                              class="flex-1"
                                              onsubmit="return confirmPermanentDelete(event, '{{ $project->title }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-full bg-gradient-to-r from-[#b01116] to-[#8d0d11] hover:from-[#8d0d11] hover:to-[#6b0a0e] text-white font-semibold py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg sm:rounded-xl transition-all duration-200 flex items-center justify-center gap-2 hover:scale-105 shadow-lg text-sm sm:text-base">
                                                <i class="ri-delete-bin-line text-sm sm:text-lg"></i>
                                                <span class="hidden xs:inline">Hapus</span>
                                                <span class="xs:hidden">Hapus</span>
                                                <span class="hidden sm:inline">Permanen</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm p-8 sm:p-12 lg:p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-20 h-20 sm:w-28 sm:h-28 lg:w-32 lg:h-32 mx-auto mb-6 sm:mb-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="ri-delete-bin-line text-4xl sm:text-5xl lg:text-6xl text-gray-300"></i>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">Tempat Sampah Kosong</h3>
                    <p class="text-base sm:text-lg text-gray-600 mb-6 sm:mb-8">Tidak ada proyek yang dihapus.</p>
                    <a href="{{ route('student.profile') }}" 
                       class="inline-flex items-center gap-2 sm:gap-3 bg-gradient-to-r from-[#b01116] to-[#8d0d11] hover:from-[#8d0d11] hover:to-[#6b0a0e] text-white font-semibold px-6 sm:px-8 py-3 sm:py-4 rounded-xl transition-all duration-200 hover:scale-105 shadow-lg text-sm sm:text-base">
                        <i class="ri-arrow-left-line text-base sm:text-lg"></i>
                        <span class="hidden sm:inline">Kembali ke Profil</span>
                        <span class="sm:hidden">Kembali</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function confirmPermanentDelete(event, projectTitle) {
    event.preventDefault();
    
    Swal.fire({
        title: 'Hapus Permanen?',
        html: `<p class="mb-2">Apakah Anda yakin ingin menghapus <strong>"${projectTitle}"</strong> secara permanen?</p>
               <p class="text-red-600 font-medium">Tindakan ini tidak dapat dibatalkan!</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Permanen!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl',
            cancelButton: 'rounded-xl'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.submit();
        }
    });
    
    return false;
}
</script>
@endsection
