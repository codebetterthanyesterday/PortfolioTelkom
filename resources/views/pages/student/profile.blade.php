@extends('layout.layout')

@section('title', "Profil Saya")

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="profileEditor()">
    <div class="flex flex-col lg:flex-row gap-8" >
        {{-- <!-- Left Column (2 columns width) --> --}}
        <div class="flex-1 lg:w-2/3 lg:order-1 order-2">
            <!-- Quick Actions Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="mb-4 sm:mb-0">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Aksi Cepat</h3>
                        <p class="text-sm text-gray-600">Buat proyek baru atau inisiasi proyek tim</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button @click="showIndividualProjectModal = true; projectType = 'individual'; resetProjectModal()" 
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="ri-user-line"></i>
                            <span>Proyek Individual</span>
                        </button>
                        <button @click="showTeamProjectModal = true; projectType = 'team'; resetProjectModal()" 
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-[#b01116] to-[#8d0d11] hover:from-[#8d0d11] hover:to-[#b01116] text-white rounded-lg font-medium transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="ri-team-line"></i>
                            <span>Proyek Tim</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8" x-data="{ activeTab: 'all' }">
                <!-- Tab Headers -->
                <div class="flex border-b border-gray-200 overflow-x-auto">
                    <button @click="activeTab = 'all'" 
                            :class="activeTab === 'all' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-folder-3-line mr-2"></i>Semua Proyek
                    </button>
                    <button @click="activeTab = 'team'" 
                            :class="activeTab === 'team' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-team-line mr-2"></i>Proyek Tim
                    </button>
                    <button @click="activeTab = 'personal'" 
                            :class="activeTab === 'personal' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-user-line mr-2"></i>Proyek Pribadi
                    </button>
                    <button @click="activeTab = 'about'" 
                            :class="activeTab === 'about' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 min-w-max px-6 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                        <i class="ri-information-line mr-2"></i>Tentang Saya
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- All Projects Tab -->
                    <div x-show="activeTab === 'all'" x-transition>
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Semua Proyek</h2>
                            <p class="text-gray-600">Koleksi lengkap proyek yang telah dikerjakan, baik secara individu maupun tim.</p>
                        </div>

                        <!-- Projects Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @php
                                $allProjects = collect();
                                $existingProjectIds = [];

                                // Add personal projects
                                if(auth()->user()->student && auth()->user()->student->projects) {
                                    foreach(auth()->user()->student->projects as $project) {
                                        $project->project_type = 'individual';
                                        $allProjects->push($project);
                                        $existingProjectIds[] = $project->id;
                                    }
                                }
                                // Add team projects, skipping if already present
                                if(auth()->user()->student && auth()->user()->student->memberProjects) {
                                    foreach(auth()->user()->student->memberProjects as $project) {
                                        if (!in_array($project->id, $existingProjectIds)) {
                                            $project->project_type = 'team';
                                            $allProjects->push($project);
                                            $existingProjectIds[] = $project->id;
                                        }
                                    }
                                }
                                $allProjects = $allProjects->sortByDesc('created_at');
                            @endphp

                            @if($allProjects->count() > 0)
                                @foreach($allProjects as $project)
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                        <div class="aspect-video bg-gradient-to-br from-[#b01116] to-[#8d0d11] relative overflow-hidden">
                                            @if($project->media->where('type', 'image')->first())
                                                <img src="{{ $project->media->where('type', 'image')->first()->url }}" 
                                                     alt="{{ $project->title }}" 
                                                     class="w-full h-full object-cover">
                                            @endif
                                            @if($project->categories && $project->categories->first())
                                                <div class="absolute top-3 left-3 z-10">
                                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-pink-100 hover:bg-pink-100 text-[#b01116] border border-pink-200 rounded-full">
                                                        {{ $project->categories->first()->name }}
                                                    </span>
                                                </div>  
                                            @endif
                                            <div class="absolute top-3 right-3 z-10">
                                                <span class="inline-block px-2 py-1 text-xs font-medium {{ $project->type === 'individual' ? 'bg-[#b01116]' : 'bg-[#8d0d11]' }} text-white rounded-full">
                                                    {{ $project->type === 'individual' ? 'Pribadi' : 'Tim' }}
                                                </span>
                                            </div>
                                            @if(!$project->media->where('type', 'image')->first())
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="text-center text-white">
                                                    <i class="ri-code-box-line text-3xl mb-2"></i>
                                                    <p class="text-sm font-medium">{{ $project->type === 'team' ? 'Tim Project' : 'Individual Project' }}</p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="font-semibold text-gray-800">{{ $project->title }}</h3>
                                                <span class="px-2 py-1 text-xs {{ $project->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">{{ $project->created_at->format('d F Y') }}</p>
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($project->description, 100) }}</p>
                                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                                <div class="flex items-center gap-1">
                                                    <i class="ri-eye-line"></i>
                                                    <span>{{ number_format($project->view_count) }}</span>
                                                </div>
                                                <a href="{{ route('projects.show', $project->slug) }}" class="text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Detail</a>
                                            </div>
                                            {{-- <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    @can('update', $project)
                                                        <button @click="loadProjectForEdit({{ $project->id }})" class="flex-1 text-xs text-center px-3 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                                                            <i class="ri-edit-line mr-1"></i>Edit
                                                        </button>
                                                    @endcan
                                                    @can('delete', $project)
                                                        <button @click="deleteProject({{ $project->id }}, '{{ $project->title }}')" class="flex-1 min-w-[110px] text-xs text-center px-3 py-2 border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                            <i class="ri-delete-bin-line mr-1"></i>Hapus
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-12">
                                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="ri-folder-line text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada proyek</h3>
                                    <p class="text-gray-600 mb-6">Mulai buat proyek pertama Anda atau bergabung dengan tim proyek lainnya.</p>
                                    <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                                        <button @click="showIndividualProjectModal = true; projectType = 'individual'; resetProjectModal()" class="self-center inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                            <i class="ri-add-line"></i>
                                            Buat Proyek Baru
                                        </button>
                                        {{-- flex gap-2 rounded-md font-medium bg-pink-50 hover:bg-pink-100 text-[#b01116] border border-pink-200 px-3 py-1 transition-colors ease-in-out duration-300 --}}
                                        <button @click="showTeamProjectModal = true; projectType = 'team'; resetProjectModal()" class="self-center inline-flex items-center justify-center gap-2 px-6 py-3 bg-pink-50 hover:bg-pink-100 text-[#b01116] border border-pink-200 rounded-lg font-medium transition-colors">
                                            <i class="ri-add-line"></i>
                                            Inisiasi Proyek Tim
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>                    <!-- Team Projects Tab -->
                    <div x-show="activeTab === 'team'" x-transition>
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Proyek Tim</h2>
                            <p class="text-gray-600">Proyek yang dikerjakan bersama tim dengan kontribusi berbagai keahlian.</p>
                        </div>

                        <!-- Projects Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @if(auth()->user()->student && auth()->user()->student->memberProjects->count() > 0)
                                @foreach(auth()->user()->student->memberProjects as $project)
                                    @php
                                        $membership = auth()->user()->student->projectMemberships->where('project_id', $project->id)->first();
                                    @endphp
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                        <div class="aspect-video bg-gradient-to-br from-[#b01116] to-[#8d0d11] relative overflow-hidden">
                                            @if($project->media->where('type', 'image')->first())
                                                <img src="{{ $project->media->where('type', 'image')->first()->url }}" 
                                                     alt="{{ $project->title }}" 
                                                     class="w-full h-full object-cover">
                                            @endif
                                            @if($project->categories && $project->categories->first())
                                                <div class="absolute top-3 left-3 z-10">
                                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-pink-100 hover:bg-pink-100 text-[#b01116] border border-pink-200 rounded-full">
                                                        {{ $project->categories->first()->name }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="absolute top-3 right-3 z-10">
                                                <span class="inline-block px-2 py-1 text-xs font-medium bg-[#8d0d11] text-white rounded-full">
                                                    Tim
                                                </span>
                                            </div>
                                            @if(!$project->media->where('type', 'image')->first())
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="text-center text-white">
                                                    <i class="ri-team-line text-3xl mb-2"></i>
                                                    <p class="text-sm font-medium">Team Project</p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="font-semibold text-gray-800">{{ $project->title }}</h3>
                                                <span class="px-2 py-1 text-xs {{ $project->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">
                                                {{ $project->created_at->format('d F Y') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mb-2">
                                                As a <span class="text-[#b01116] font-semibold">{{ ucfirst($membership->role) }}</span>
                                            </p>
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($project->description, 100) }}</p>
                                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                                <div class="flex items-center gap-1">
                                                    <i class="ri-eye-line"></i>
                                                    <span>{{ number_format($project->view_count) }}</span>
                                                </div>
                                                <a href="{{ route('projects.show', $project->slug) }}" class="text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Detail</a>
                                            </div>
                                            {{-- <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="flex items-center gap-2">
                                                    @can('update', $project)
                                                        <button @click="loadProjectForEdit({{ $project->id }})" class="flex-1 text-xs text-center px-3 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                                                            <i class="ri-edit-line mr-1"></i>Edit
                                                        </button>
                                                    @endcan
                                                    @can('delete', $project)
                                                        <button @click="deleteProject({{ $project->id }}, '{{ $project->title }}')" class="flex-1 text-xs text-center px-3 py-2 border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                            <i class="ri-delete-bin-line mr-1"></i>Hapus
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-12">
                                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="ri-team-line text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-800 mb-2">Belum bergabung dengan proyek tim</h3>
                                    <p class="text-gray-600 mb-6">Cari proyek tim yang sesuai dengan keahlian Anda dan bergabunglah untuk berkolaborasi.</p>
                                    <button @click="showTeamProjectModal = true; projectType = 'team'; resetProjectModal()" class="inline-flex items-center text-center justify-center gap-2 px-6 py-3 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                        <i class="ri-add-line"></i>
                                        Inisiasi Proyek Tim
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>                    <!-- Personal Projects Tab -->
                    <!-- Personal Projects Tab -->
                    <div x-show="activeTab === 'personal'" x-transition>
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Proyek Pribadi</h2>
                            <p class="text-gray-600">Proyek yang dikerjakan secara mandiri untuk mengasah skill dan kreativitas.</p>
                        </div>

                        <!-- Projects Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @if(auth()->user()->student && auth()->user()->student->projects->where('type', 'individual')->count() > 0)
                                @foreach(auth()->user()->student->projects->where('type', 'individual') as $project)
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                        <div class="aspect-video bg-gradient-to-br from-[#b01116] to-[#8d0d11] relative overflow-hidden">
                                            @if($project->media->where('type', 'image')->first())
                                                <img src="{{ $project->media->where('type', 'image')->first()->url }}" 
                                                     alt="{{ $project->title }}" 
                                                     class="w-full h-full object-cover">
                                            @endif
                                            @if($project->categories && $project->categories->first())
                                                <div class="absolute top-3 left-3 z-10">
                                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-pink-100 hover:bg-pink-100 text-[#b01116] border border-pink-200 rounded-full">
                                                        {{ $project->categories->first()->name }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="absolute top-3 right-3 z-10">
                                                <span class="inline-block px-2 py-1 text-xs font-medium bg-[#b01116] text-white rounded-full">
                                                    {{ ucfirst($project->type) }}
                                                </span>
                                            </div>
                                            @if(!$project->media->where('type', 'image')->first())
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="text-center text-white">
                                                    <i class="ri-user-line text-3xl mb-2"></i>
                                                    <p class="text-sm font-medium">Personal Project</p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="font-semibold text-gray-800">{{ $project->title }}</h3>
                                                <span class="px-2 py-1 text-xs {{ $project->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">{{ $project->created_at->format('d F Y') }}</p>
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($project->description, 100) }}</p>
                                            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                                <div class="flex items-center gap-1">
                                                    <i class="ri-eye-line"></i>
                                                    <span>{{ number_format($project->view_count) }}</span>
                                                </div>
                                                <a href="{{ route('projects.show', $project->slug) }}" class="text-[#b01116] hover:text-[#8d0d11] font-medium">Lihat Detail</a>
                                            </div>
                                            {{-- <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="flex items-center gap-2">
                                                    @can('update', $project)
                                                        <button @click="loadProjectForEdit({{ $project->id }})" class="flex-1 text-xs text-center px-3 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                                                            <i class="ri-edit-line mr-1"></i>Edit
                                                        </button>
                                                    @endcan
                                                    @can('delete', $project)
                                                        <button @click="deleteProject({{ $project->id }}, '{{ $project->title }}')" class="flex-1 text-xs text-center px-3 py-2 border border-red-300 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                            <i class="ri-delete-bin-line mr-1"></i>Hapus
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-12">
                                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="ri-user-line text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada proyek pribadi</h3>
                                    <p class="text-gray-600 mb-6">Buat proyek pribadi pertama Anda untuk menampilkan keahlian dan kreativitas.</p>
                                    <button @click="showIndividualProjectModal = true; projectType = 'individual'; resetProjectModal()" class="inline-flex items-center text-center justify-center gap-2 px-6 py-3 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                        <i class="ri-add-line"></i>
                                        Buat Proyek Baru
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>                    <!-- About Tab -->
                    <div x-show="activeTab === 'about'" x-transition>
                        <div class="prose prose-gray max-w-none">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Saya</h2>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                {{ auth()->user()->about ?? 'Belum ada deskripsi tentang diri. Klik tombol Edit Profil untuk menambahkan informasi tentang Anda.' }}
                            </p>
                            
                            <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">Keahlian</h3>
                            <div class="flex flex-wrap gap-2 mb-6">
                                @if(auth()->user()->student && auth()->user()->student->expertises->count() > 0)
                                    @foreach(auth()->user()->student->expertises as $expertise)
                                        <span class="px-3 py-2 bg-red-100 text-gray-700 text-sm font-medium rounded-full">
                                            {{ $expertise->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <p class="text-gray-500 text-sm">Belum ada keahlian yang ditambahkan. Klik tombol Edit Profil untuk menambahkan keahlian Anda.</p>
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">Pendidikan</h3>
                            <div class="space-y-4">
                                @if(auth()->user()->student && auth()->user()->student->educationInfo->count() > 0)
                                    @foreach(auth()->user()->student->educationInfo->sortByDesc('is_current') as $education)
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
                                    @endforeach
                                @else
                                    <p class="text-gray-500 text-sm">Belum ada informasi pendidikan. Klik tombol Edit Profil untuk menambahkan riwayat pendidikan Anda.</p>
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">Statistik</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ auth()->user()->student->projects->count() ?? 0 }}</div>
                                    <div class="text-sm text-gray-600">Proyek Pribadi</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ auth()->user()->student->projectMemberships->count() ?? 0 }}</div>
                                    <div class="text-sm text-gray-600">Proyek Tim</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                                    <div class="text-2xl font-bold text-[#b01116] mb-1">{{ auth()->user()->student->expertises->count() ?? 0 }}</div>
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
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->username }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-4xl font-bold">
                                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Student Name -->
                    <h1 class="text-2xl font-bold text-gray-800 text-center mb-2">{{ auth()->user()->full_name ?? auth()->user()->username }}</h1>

                    <!-- Username -->
                    <p class="text-sm text-gray-500 text-center mb-4">{{ "@" . auth()->user()->username }}</p>
                    
                    <!-- Student ID -->
                    <p class="text-sm text-gray-500 text-center mb-4">NIM: {{ auth()->user()->student->student_id ?? 'Belum diisi' }}</p>

                    <!-- About (100 char limit) -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-sm text-gray-600 text-center leading-relaxed">
                            {{ auth()->user()->short_about ?? 'Belum ada deskripsi singkat. Klik tombol Edit Profil untuk menambahkan.' }}
                        </p>
                    </div>

                    <!-- Edit Profile Button -->
                    <button @click="showEditModal = true; resetModal()" class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2 mb-6">
                        <i class="ri-edit-line"></i>
                        Edit Profil
                    </button>

                    <!-- Contact Information -->
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-gray-800 uppercase mb-4">Kontak</h3>
                        
                        <!-- Email -->
                        <a href="mailto:{{ auth()->user()->email }}" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-mail-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm font-medium">{{ auth()->user()->email }}</p>
                            </div>
                        </a>

                        @if(auth()->user()->phone_number)
                        <!-- Phone -->
                        <a href="tel:{{ auth()->user()->phone_number }}" class="flex items-center gap-3 text-gray-600 hover:text-[#b01116] transition-colors group">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-[#b01116]/10 transition-colors">
                                <i class="ri-phone-line text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Telepon</p>
                                <p class="text-sm font-medium">{{ auth()->user()->phone_number }}</p>
                            </div>
                        </a>
                        @endif
                    </div>

                    <!-- Multi-Step Edit Profile Modal (teleported to <body>) -->
                    <template x-teleport="body">
                        <div x-show="showEditModal"
                             x-transition
                             @keydown.escape.window="showEditModal = false; resetModal()"
                             @click.self="showEditModal = false; resetModal()"
                             class="fixed inset-0 z-[200] bg-black/50 flex items-center justify-center p-4"
                             role="dialog" aria-modal="true" style="display: none;">
                            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                                
                                <!-- Modal Header with Progress -->
                                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h2 class="text-xl font-bold text-gray-800">Edit Profil</h2>
                                        <button @click="showEditModal = false; resetModal()" class="text-gray-400 hover:text-gray-600">
                                            <i class="ri-close-line text-2xl"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Progress Steps -->
                                    <div class="flex items-center justify-center">
                                        <template x-for="step in totalSteps" :key="step">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium transition-colors"
                                                     :class="step <= currentStep ? 'bg-[#b01116] text-white' : 'bg-gray-200 text-gray-500'">
                                                    <span x-text="step"></span>
                                                </div>
                                                <div class="w-16 h-1 mx-2 transition-colors" 
                                                     x-show="step < totalSteps"
                                                     :class="step < currentStep ? 'bg-[#b01116]' : 'bg-gray-200'"></div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div class="text-center mt-2 text-sm text-gray-600">
                                        <span x-show="currentStep === 1">Langkah 1: Informasi Dasar</span>
                                        <span x-show="currentStep === 2">Langkah 2: Keahlian</span>
                                        <span x-show="currentStep === 3">Langkah 3: Pendidikan</span>
                                    </div>
                                </div>

                                <!-- Modal Body -->
                                <form action="{{ route('student.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    
                                    <!-- Hidden field for expertises -->
                                    <template x-for="(expertiseId, index) in selectedExpertises" :key="expertiseId">
                                        <input type="hidden" :name="'expertises[' + index + ']'" :value="expertiseId">
                                    </template>
                                    
                                    <!-- Hidden field for education -->
                                    <template x-for="(edu, index) in education" :key="index">
                                        <div>
                                            <input type="hidden" :name="'education[' + index + '][institution_name]'" :value="edu.institution_name">
                                            <input type="hidden" :name="'education[' + index + '][degree]'" :value="edu.degree">
                                            <input type="hidden" :name="'education[' + index + '][field_of_study]'" :value="edu.field_of_study">
                                            <input type="hidden" :name="'education[' + index + '][start_date]'" :value="edu.start_date">
                                            <input type="hidden" :name="'education[' + index + '][end_date]'" :value="edu.end_date">
                                            <input type="hidden" :name="'education[' + index + '][is_current]'" :value="edu.is_current ? 1 : 0">
                                            <input type="hidden" :name="'education[' + index + '][description]'" :value="edu.description">
                                            <input type="hidden" x-show="!String(edu.id).startsWith('new_')" :name="'education[' + index + '][id]'" :value="edu.id">
                                        </div>
                                    </template>

                                    <!-- Step 1: Basic Information -->
                                    <div x-show="currentStep === 1" x-transition class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
                                        
                                        <!-- Avatar Upload -->
                                        <div class="mb-6">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Profil</label>
                                            <div class="flex items-center gap-4">
                                                <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-gray-200 shrink-0 relative">
                                                    <template x-if="removeAvatar">
                                                        <div class="w-full h-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white text-2xl font-bold">
                                                            {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                                        </div>
                                                    </template>
                                                    <template x-if="!removeAvatar">
                                                        <div>
                                                            <!-- Preview Image -->
                                                            <img x-show="avatarPreview" 
                                                                 :src="avatarPreview" 
                                                                 alt="Avatar Preview" 
                                                                 class="w-full h-full object-cover absolute inset-0">
                                                            
                                                            <!-- Current Avatar or Placeholder -->
                                                            <div x-show="!avatarPreview" class="w-full h-full absolute inset-0">
                                                                @if(auth()->user()->avatar)
                                                                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                                                                @else
                                                                    <div class="w-full h-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-2xl font-bold">
                                                                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex gap-2 mb-2">
                                                        <label for="avatar" class="cursor-pointer inline-block px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                                                            <i class="ri-image-add-line mr-2"></i>Pilih Foto
                                                        </label>
                                                        @if(auth()->user()->avatar)
                                                            <button type="button" 
                                                                    @click="handleRemoveAvatar()"
                                                                    x-show="!removeAvatar"
                                                                    class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors text-sm font-medium">
                                                                <i class="ri-delete-bin-line mr-2"></i>Hapus
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <input type="file" 
                                                           id="avatar"
                                                           name="avatar" 
                                                           accept="image/*" 
                                                           @change="handleAvatarPreview($event)"
                                                           class="hidden">
                                                    <input type="hidden" name="remove_avatar" :value="removeAvatar ? '1' : '0'">
                                                    <p class="text-xs text-gray-500">JPG, PNG atau GIF (Maks. 2MB)</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Full Name -->
                                            <div>
                                                <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                                                <input type="text" name="full_name" id="full_name" x-model="fullName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                            </div>

                                            <!-- Student ID -->
                                            <div>
                                                <label for="student_id" class="block text-sm font-semibold text-gray-700 mb-2">NIM *</label>
                                                <input type="text" name="student_id" id="student_id" x-model="studentId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                            </div>

                                            <!-- Phone -->
                                            <div>
                                                <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon *</label>
                                                <input type="tel" name="phone_number" id="phone_number" x-model="phoneNumber" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                            </div>

                                            <!-- Email (readonly) -->
                                            <div>
                                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                                <input type="email" id="email" value="{{ auth()->user()->email }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                                            </div>
                                        </div>

                                        <!-- Short About -->
                                        <div class="mt-4">
                                            <label for="short_about" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat (Maks. 500 karakter)</label>
                                            <textarea name="short_about" id="short_about" x-model="shortAbout" rows="3" maxlength="500" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent"></textarea>
                                        </div>

                                        <!-- About -->
                                        <div class="mt-4">
                                            <label for="about" class="block text-sm font-semibold text-gray-700 mb-2">Tentang Saya</label>
                                            <textarea name="about" id="about" x-model="about" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent"></textarea>
                                        </div>
                                    </div>

                                    <!-- Step 2: Expertise Selection -->
                                    <div x-show="currentStep === 2" x-transition class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                            <i class="ri-lightbulb-line text-[#b01116]"></i>
                                            Pilih Keahlian Anda
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-4">Pilih keahlian yang Anda kuasai. Anda dapat memilih lebih dari satu keahlian.</p>
                                        
                                        <!-- Search Box -->
                                        <div class="relative mb-4">
                                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                                            <input 
                                                x-model="searchExpertise" 
                                                type="text" 
                                                placeholder="Cari keahlian..." 
                                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all">
                                        </div>
                                        
                                        <!-- Selection Grid -->
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-72 overflow-y-auto p-1 mb-4">
                                            <template x-for="expertise in filteredExpertises" :key="'modal-exp-'+expertise.id">
                                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all"
                                                       :class="selectedExpertises.includes(expertise.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                                    <input type="checkbox" 
                                                           class="sr-only" 
                                                           :checked="selectedExpertises.includes(expertise.id)"
                                                           @change="toggleExpertise(expertise.id)">
                                                    <div class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all"
                                                         :class="selectedExpertises.includes(expertise.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300'">
                                                        <i class="ri-check-line text-white text-sm font-bold" x-show="selectedExpertises.includes(expertise.id)"></i>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700 leading-tight" x-text="expertise.name"></span>
                                                </label>
                                            </template>
                                        </div>
                                        
                                        <!-- No Results -->
                                        <div x-show="filteredExpertises.length === 0 && searchExpertise !== ''" class="text-center py-12 text-gray-500">
                                            <i class="ri-search-line text-5xl mb-3 opacity-50"></i>
                                            <p class="font-medium">Tidak ada keahlian yang sesuai</p>
                                            <p class="text-sm mt-1">Coba kata kunci lain atau tambahkan keahlian baru</p>
                                        </div>
                                        
                                        <!-- Add New Expertise Button -->
                                        <button 
                                            type="button"
                                            @click="showAddExpertise = !showAddExpertise"
                                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                                            <i class="ri-add-circle-line text-xl" :class="showAddExpertise ? 'rotate-45 transition-transform' : ''"></i>
                                            <span x-text="showAddExpertise ? 'Batal Tambah' : 'Tambah Keahlian Baru'"></span>
                                        </button>
                                        
                                        <!-- Inline Create Form -->
                                        <div x-show="showAddExpertise" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                                <i class="ri-lightbulb-line text-blue-600"></i>
                                                Tambah Keahlian Baru
                                            </h5>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Keahlian</label>
                                                    <input type="text" x-model="newExpertiseName" placeholder="Contoh: JavaScript, UI/UX Design, Data Analysis" 
                                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <button type="button" @click="addExpertise()" 
                                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg text-sm hover:bg-blue-700 transition-colors font-medium">
                                                    <i class="ri-save-line mr-2"></i>Simpan Keahlian
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Selection Counter -->
                                        <div class="mt-4 p-3 rounded-lg border"
                                             :class="selectedExpertises.length === 0 ? 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-200' : 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-300'">
                                            <div class="flex items-center gap-3"
                                                 :class="selectedExpertises.length === 0 ? 'text-gray-600' : 'text-blue-700'">
                                                <i :class="selectedExpertises.length === 0 ? 'ri-information-line' : 'ri-checkbox-circle-line'" class="text-xl"></i>
                                                <div class="flex-1">
                                                    <div class="font-semibold">
                                                        <span x-show="selectedExpertises.length === 0">Belum ada keahlian yang dipilih</span>
                                                        <span x-show="selectedExpertises.length > 0" x-text="selectedExpertises.length + ' keahlian dipilih'"></span>
                                                    </div>
                                                    <div class="text-xs opacity-75" x-show="selectedExpertises.length > 0">
                                                        Keahlian membantu menunjukkan kompetensi Anda
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 3: Education History -->
                                    <div x-show="currentStep === 3" x-transition class="p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-semibold text-gray-800">Riwayat Pendidikan</h3>
                                            <button type="button" @click="addEducation()" class="bg-[#b01116] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#8d0d11] transition-colors">
                                                <i class="ri-add-line"></i> Tambah Pendidikan
                                            </button>
                                        </div>
                                        
                                        <!-- Education List -->
                                        <div class="space-y-4 mb-6">
                                            <template x-for="(edu, index) in education" :key="index">
                                                <div class="border border-gray-200 rounded-lg p-4">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <h4 class="font-medium text-gray-800" x-text="edu.institution_name || 'Institusi Baru'"></h4>
                                                        <button type="button" @click="removeEducation(index)" class="text-red-500 hover:text-red-700">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Nama Institusi</label>
                                                            <input type="text" x-model="edu.institution_name" placeholder="Universitas Telkom" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Jenjang</label>
                                                            <select x-model="edu.degree" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                                                <option value="">Pilih Jenjang</option>
                                                                <option value="SD">SD</option>
                                                                <option value="SMP">SMP</option>
                                                                <option value="SMA">SMA</option>
                                                                <option value="SMK">SMK</option>
                                                                <option value="D1">D1</option>
                                                                <option value="D2">D2</option>
                                                                <option value="D3">D3</option>
                                                                <option value="D4">D4</option>
                                                                <option value="S1">S1</option>
                                                                <option value="S2">S2</option>
                                                                <option value="S3">S3</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Bidang Studi</label>
                                                            <input type="text" x-model="edu.field_of_study" placeholder="Teknik Informatika" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                                        </div>
                                                        <div class="flex items-center gap-3">
                                                            <div class="flex-1">
                                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tahun Mulai</label>
                                                                <input type="date" x-model="edu.start_date" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                                            </div>
                                                            <div class="flex-1">
                                                                <label class="block text-xs font-medium text-gray-700 mb-1">Tahun Selesai</label>
                                                                <input type="date" x-model="edu.end_date" :disabled="edu.is_current" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent disabled:bg-gray-50">
                                                            </div>
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <label class="flex items-center gap-2 text-sm">
                                                                <input type="checkbox" x-model="edu.is_current" class="w-4 h-4 text-[#b01116] border-gray-300 rounded focus:ring-[#b01116]">
                                                                <span class="text-gray-700">Saat ini masih bersekolah/kuliah di sini</span>
                                                            </label>
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                                                            <textarea x-model="edu.description" rows="2" placeholder="Prestasi, aktivitas, atau deskripsi lainnya" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            
                                            <div x-show="education.length === 0" class="text-center py-8 text-gray-500">
                                                <i class="ri-graduation-cap-line text-4xl mb-2"></i>
                                                <p>Belum ada riwayat pendidikan</p>
                                                <p class="text-sm">Klik tombol "Tambah Pendidikan" untuk menambahkan</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Navigation Footer -->
                                    <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4">
                                        <div class="flex justify-between gap-4">
                                            <button type="button" 
                                                    @click="prevStep()" 
                                                    x-show="currentStep > 1"
                                                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                                                <i class="ri-arrow-left-line mr-2"></i>Kembali
                                            </button>
                                            
                                            <button type="button" 
                                                    @click="showEditModal = false; resetModal()" 
                                                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                                                Batal
                                            </button>
                                            
                                            <button type="button" 
                                                    @click="nextStep()" 
                                                    x-show="currentStep < totalSteps"
                                                    :disabled="!canProceedToNext()"
                                                    :class="canProceedToNext() ? 'bg-[#b01116] hover:bg-[#8d0d11] cursor-pointer' : 'bg-gray-300 cursor-not-allowed opacity-60'"
                                                    class="px-6 py-2 text-white rounded-lg font-medium transition-colors">
                                                Selanjutnya<i class="ri-arrow-right-line ml-2"></i>
                                            </button>
                                            
                                            <button type="submit" 
                                                    x-show="currentStep === totalSteps"
                                                    class="px-6 py-2 bg-[#b01116] hover:bg-[#8d0d11] text-white rounded-lg font-medium transition-colors">
                                                <i class="ri-save-line mr-2"></i>Simpan Semua Perubahan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        
<template x-teleport="body">
    <div x-show="showIndividualProjectModal"
         x-transition
         @keydown.escape.window="showIndividualProjectModal = false; resetProjectModal()"
         @click.self="showIndividualProjectModal = false; resetProjectModal()"
         class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         role="dialog" aria-modal="true" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
            
            <!-- Modal Header with Enhanced Progress -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-5 z-10">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="ri-file-add-line text-[#b01116]"></i>
                        Buat Proyek Individu
                    </h2>
                    <button @click="showIndividualProjectModal = false; resetProjectModal()" 
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>
                
                <!-- Enhanced Progress Steps -->
                <div class="flex items-center justify-center gap-2">
                    <template x-for="step in totalSteps" :key="step">
                        <div class="flex items-center gap-2">
                            <div class="relative w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-sm"
                                 :class="step < currentStep ? 'bg-[#8d0d11] text-white' : step === currentStep ? 'bg-[#b01116] text-white ring-4 ring-red-100 scale-110' : 'bg-gray-200 text-gray-500'">
                                <span x-show="step < currentStep">
                                    <i class="ri-check-line text-lg"></i>
                                </span>
                                <span x-show="step >= currentStep" x-text="step"></span>
                            </div>
                            <div x-show="step < totalSteps"
                                 class="w-20 h-1.5 transition-all duration-300 rounded-full" 
                                 :class="step < currentStep ? 'bg-[#8d0d11]' : 'bg-gray-200'"></div>
                        </div>
                    </template>
                </div>
                
                <div class="text-center mt-3 text-sm font-medium text-gray-700">
                    <span x-show="currentStep === 1" class="flex items-center justify-center gap-2">
                        <i class="ri-information-line"></i>
                        Langkah 1: Informasi Proyek
                    </span>
                    <span x-show="currentStep === 2" class="flex items-center justify-center gap-2">
                        <i class="ri-price-tag-3-line"></i>
                        Langkah 2: Kategori, Mata Kuliah & Pembimbing
                    </span>
                    <span x-show="currentStep === 3" class="flex items-center justify-center gap-2">
                        <i class="ri-upload-cloud-2-line"></i>
                        Langkah 3: Media & Review
                    </span>
                </div>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('student.projects.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="type" value="individual">
                
                <!-- Hidden fields for form data -->
                <template x-for="(categoryId, index) in projectData.categories" :key="'cat-'+categoryId">
                    <input type="hidden" :name="'categories[' + index + ']'" :value="categoryId">
                </template>
                <template x-for="(subjectId, index) in projectData.subjects" :key="'sub-'+subjectId">
                    <input type="hidden" :name="'subjects[' + index + ']'" :value="subjectId">
                </template>
                <template x-for="(teacherId, index) in projectData.teachers" :key="'teach-'+teacherId">
                    <input type="hidden" :name="'teachers[' + index + ']'" :value="teacherId">
                </template>

                <!-- Step 1: Project Information -->
                <div x-show="currentStep === 1" x-transition class="p-6 space-y-5">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                        <i class="ri-file-text-line text-[#b01116]"></i>
                        Informasi Dasar Proyek
                    </h3>
                    
                    <!-- Project Title -->
                    <div>
                        <label for="ind_title" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Judul Proyek <span class="text-[#b01116]">*</span>
                        </label>
                        <input type="text" name="title" id="ind_title" x-model="projectData.title" required 
                               :class="projectData.title.trim() === '' ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-[#b01116] focus:border-[#b01116]'"
                               class="w-full px-4 py-3 border-2 rounded-lg transition-all"
                               placeholder="Masukkan judul proyek yang menarik...">
                        <p x-show="projectData.title.trim() === ''" class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <i class="ri-error-warning-line"></i>
                            Judul proyek wajib diisi
                        </p>
                    </div>

                    <!-- Project Description -->
                    <div>
                        <label for="ind_description" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Deskripsi Proyek
                        </label>
                        <textarea name="description" id="ind_description" x-model="projectData.description" rows="6" 
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all resize-none" 
                                  placeholder="Jelaskan detail proyek Anda, tujuan, fitur utama, dan hal menarik lainnya..."></textarea>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <i class="ri-lightbulb-line"></i>
                            Tips: Deskripsi yang detail akan menarik lebih banyak investor
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Project Price -->
                        <div>
                            <label for="ind_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                Estimasi Harga (Rp)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="number" name="price" id="ind_price" x-model="projectData.price" min="0" step="1000"
                                       class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all"
                                       placeholder="0">
                            </div>
                        </div>

                        <!-- Project Status -->
                        <div>
                            <label for="ind_status" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                Status Publikasi <span class="text-[#b01116]">*</span>
                            </label>
                            <select name="status" id="ind_status" x-model="projectData.status" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Categories, Subjects & Teachers -->
                <div x-show="currentStep === 2" x-transition class="p-6 space-y-8">
                    <!-- CATEGORIES SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-price-tag-3-line text-[#b01116]"></i>
                            Kategori Proyek <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih minimal 1 kategori yang sesuai dengan proyek Anda</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchCategory"
                                placeholder="Cari kategori..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="category in filteredCategories" :key="'modal-cat-'+category.id">
                                <label 
                                    class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.categories.includes(category.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.categories.includes(category.id)"
                                        @change="toggleCategory(category.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.categories.includes(category.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.categories.includes(category.id)"></i>
                                    </div>
                                    <span 
                                        class="text-sm font-medium transition-colors"
                                        :class="projectData.categories.includes(category.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                        x-text="category.name"></span>
                                </label>
                            </template>
                        </div>
                        
                        <!-- No Results -->
                        <div x-show="filteredCategories.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-search-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada kategori yang sesuai</p>
                            <p class="text-sm mt-1">Coba kata kunci lain atau tambahkan kategori baru</p>
                        </div>
                        
                        <!-- Add New Button -->
                        <button 
                            type="button"
                            @click="showAddCategory = !showAddCategory"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl" :class="showAddCategory ? 'rotate-45 transition-transform' : ''"></i>
                            <span x-text="showAddCategory ? 'Batal Tambah' : 'Tambah Kategori Baru'"></span>
                        </button>
                        
                        <!-- Inline Create Form -->
                        <div x-show="showAddCategory" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Kategori Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newCategory.name"
                                    placeholder="Nama Kategori *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                <textarea 
                                    x-model="newCategory.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createCategory()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                    <i class="ri-save-line"></i>
                                    Simpan Kategori
                                </button>
                            </div>
                        </div>
                        
                        <!-- Selection Counter -->
                        <div class="mt-4 p-3 rounded-lg border"
                             :class="projectData.categories.length === 0 ? 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200' : 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200'">
                            <div class="flex items-center gap-3"
                                 :class="projectData.categories.length === 0 ? 'text-red-700' : 'text-gray-700'">
                                <i :class="projectData.categories.length === 0 ? 'ri-error-warning-line' : 'ri-checkbox-circle-line'" class="text-xl"></i>
                                <div class="flex-1">
                                    <div class="font-semibold">
                                        <span x-show="projectData.categories.length === 0">Pilih minimal 1 kategori untuk melanjutkan</span>
                                        <span x-show="projectData.categories.length > 0" x-text="projectData.categories.length + ' kategori dipilih'"></span>
                                    </div>
                                    <div class="text-xs opacity-75" x-show="projectData.categories.length > 0">
                                        Kategori membantu investor menemukan proyek Anda
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBJECTS SECTION (Similar structure) -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-book-open-line text-[#b01116]"></i>
                            Mata Kuliah (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih mata kuliah yang berkaitan dengan proyek ini</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchSubject"
                                placeholder="Cari mata kuliah atau kode..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="subject in filteredSubjects" :key="'modal-sub-'+subject.id">
                                <label 
                                    class="flex flex-col p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.subjects.includes(subject.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only" 
                                            :checked="projectData.subjects.includes(subject.id)"
                                            @change="toggleSubject(subject.id)">
                                        <div 
                                            class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                            :class="projectData.subjects.includes(subject.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                            <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.subjects.includes(subject.id)"></i>
                                        </div>
                                        <span 
                                            class="text-sm font-medium transition-colors flex-1"
                                            :class="projectData.subjects.includes(subject.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                            x-text="subject.name"></span>
                                    </div>
                                    <template x-if="subject.code">
                                        <span class="text-xs text-gray-500 ml-8 mt-1" x-text="'Kode: ' + subject.code"></span>
                                    </template>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredSubjects.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-book-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada mata kuliah yang sesuai</p>
                        </div>
                        
                        <!-- Add New Button & Form (Similar to categories) -->
                        <button 
                            type="button"
                            @click="showAddSubject = !showAddSubject"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddSubject ? 'Batal Tambah' : 'Tambah Mata Kuliah Baru'"></span>
                        </button>
                        
                        <div x-show="showAddSubject" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Mata Kuliah Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newSubject.name"
                                    placeholder="Nama Mata Kuliah *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newSubject.code"
                                    placeholder="Kode Mata Kuliah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <textarea 
                                    x-model="newSubject.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createSubject()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Mata Kuliah
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#b01116] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.subjects.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Mata Kuliah Dipilih</span>
                            </div>
                        </div>
                    </div>

                    <!-- TEACHERS SECTION (Similar structure) -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-user-star-line text-[#b01116]"></i>
                            Dosen/Guru Pembimbing (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih dosen atau guru yang membimbing proyek ini</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchTeacher"
                                placeholder="Cari nama atau NIP..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection List -->
                        <div class="space-y-2 max-h-72 overflow-y-auto p-1">
                            <template x-for="teacher in filteredTeachers" :key="'modal-teach-'+teacher.id">
                                <label 
                                    class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.teachers.includes(teacher.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.teachers.includes(teacher.id)"
                                        @change="toggleTeacher(teacher.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 mt-0.5 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.teachers.includes(teacher.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.teachers.includes(teacher.id)"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div 
                                            class="font-semibold transition-colors"
                                            :class="projectData.teachers.includes(teacher.id) ? 'text-[#b01116]' : 'text-gray-800'"
                                            x-text="teacher.name"></div>
                                        <template x-if="teacher.nip">
                                            <div class="text-sm text-gray-600 mt-0.5">NIP: <span x-text="teacher.nip"></span></div>
                                        </template>
                                        <template x-if="teacher.institution">
                                            <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <i class="ri-building-line"></i>
                                                <span x-text="teacher.institution"></span>
                                            </div>
                                        </template>
                                    </div>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredTeachers.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-user-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada dosen/guru yang sesuai</p>
                        </div>
                        
                        <!-- Add New Button & Form -->
                        <button 
                            type="button"
                            @click="showAddTeacher = !showAddTeacher"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddTeacher ? 'Batal Tambah' : 'Tambah Dosen/Guru Baru'"></span>
                        </button>
                        
                        <div x-show="showAddTeacher" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Dosen/Guru Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.name"
                                    placeholder="Nama Lengkap *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.nip"
                                    placeholder="NIP/NIDN (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <div class="grid grid-cols-2 gap-3">
                                    <input 
                                        type="email" 
                                        x-model="newTeacher.email"
                                        placeholder="Email (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                    <input 
                                        type="text" 
                                        x-model="newTeacher.phone_number"
                                        placeholder="No. Telepon (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                </div>
                                <input 
                                    type="text" 
                                    x-model="newTeacher.institution"
                                    placeholder="Institusi/Sekolah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <button 
                                    type="button"
                                    @click="createTeacher()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Dosen/Guru
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#8d0d11] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.teachers.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Pembimbing Dipilih</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Media Upload & Review -->
                <div x-show="currentStep === 3" x-transition class="p-6 space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                        <i class="ri-upload-cloud-2-line text-[#b01116]"></i>
                        Upload Media & Review
                    </h3>
                    
                    <!-- Media Upload -->
                    <div x-data="mediaPreview('ind_media')">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Upload Media (Opsional)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-[#b01116] hover:bg-red-50 transition-all">
                            <i class="ri-upload-cloud-2-line text-6xl text-gray-400 mb-3"></i>
                            <div class="text-sm text-gray-600 mb-3 font-medium">
                                Drop files here or click to upload
                            </div>
                            <input type="file" 
                                   name="media[]" 
                                   multiple 
                                   accept="image/*,video/*" 
                                   class="hidden" 
                                   id="ind_media"
                                   @change="handleFiles($event.target.files)">
                            <label for="ind_media" class="inline-flex items-center gap-2 cursor-pointer bg-[#b01116] text-white px-6 py-3 rounded-lg hover:bg-[#8d0d11] transition-colors font-medium shadow-md hover:shadow-lg">
                                <i class="ri-folder-open-line"></i>
                                Choose Files
                            </label>
                            <div class="text-xs text-gray-500 mt-3">
                                Max 10 files • Each up to 10MB • JPG, PNG, MP4, MOV
                            </div>
                        </div>
                        
                        <!-- Image Previews -->
                        <div x-show="previews.length > 0" class="mt-4">
                            <p class="text-sm font-medium text-gray-700 mb-3">
                                Selected Files (<span x-text="previews.length"></span>)
                                <span class="text-xs text-gray-500 ml-2">• First image will be the main image</span>
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <template x-for="(preview, index) in previews" :key="index">
                                    <div class="relative group">
                                        <div class="aspect-square rounded-lg overflow-hidden border-2"
                                             :class="index === 0 ? 'border-[#b01116]' : 'border-gray-300'">
                                            <img :src="preview.url" 
                                                 :alt="preview.name" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <button type="button"
                                                @click="removeFile(index)"
                                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700">
                                            <i class="ri-close-line text-sm"></i>
                                        </button>
                                        <div x-show="index === 0" 
                                             class="absolute bottom-0 left-0 right-0 bg-[#b01116] text-white text-xs py-1 text-center font-medium">
                                            Main Image
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Review Project Data -->
                    <div class="bg-gradient-to-br from-red-50 via-pink-50 to-white rounded-2xl p-6 border-2 border-red-200 shadow-lg">
                        <div class="bg-gradient-to-r from-[#b01116] to-pink-600 text-white rounded-xl p-4 mb-6 shadow-md">
                            <h4 class="font-bold flex items-center gap-3 text-xl">
                                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                    <i class="ri-file-list-3-line text-2xl"></i>
                                </div>
                                <span>Review Proyek Anda</span>
                            </h4>
                            <p class="text-red-100 text-sm mt-2 ml-13">Periksa kembali semua informasi sebelum membuat proyek</p>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Title -->
                            <div class="p-4 bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#b01116] to-pink-600 rounded-lg flex items-center justify-center text-white">
                                        <i class="ri-file-text-line text-xl"></i>
                                    </div>
                                    <span class="font-bold text-gray-800">Judul Proyek</span>
                                </div>
                                <p class="text-gray-700 ml-13 font-medium" x-text="projectData.title || 'Belum diisi'"></p>
                            </div>
                            
                            <!-- Description -->
                            <div class="p-4 bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white">
                                        <i class="ri-align-left text-xl"></i>
                                    </div>
                                    <span class="font-bold text-gray-800">Deskripsi</span>
                                </div>
                                <p class="text-gray-600 ml-13 text-sm" x-text="projectData.description || 'Belum diisi'"></p>
                            </div>
                            
                            <!-- Categories -->
                            <div class="p-4 bg-white rounded-xl border-2 shadow-sm hover:shadow-md transition-all"
                                 :class="projectData.categories.length === 0 ? 'border-red-300 bg-red-50/30' : 'border-gray-200'">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white"
                                         :class="projectData.categories.length === 0 ? 'bg-red-500' : 'bg-gradient-to-br from-purple-500 to-purple-600'">
                                        <i class="ri-price-tag-3-line text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-bold text-gray-800">Kategori</span>
                                        <span x-show="projectData.categories.length === 0" class="ml-2 text-xs bg-red-500 text-white px-2 py-1 rounded-full font-semibold">Wajib!</span>
                                    </div>
                                </div>
                                <p class="ml-13" :class="projectData.categories.length === 0 ? 'text-red-600 font-semibold' : 'text-gray-700'">
                                    <span x-text="projectData.categories.length"></span> kategori terpilih
                                </p>
                            </div>
                            
                            <!-- Subjects -->
                            <div class="p-4 bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white">
                                        <i class="ri-book-open-line text-xl"></i>
                                    </div>
                                    <span class="font-bold text-gray-800">Mata Kuliah</span>
                                </div>
                                <p class="text-gray-700 ml-13" x-text="projectData.subjects.length + ' terpilih'"></p>
                            </div>
                            
                            <!-- Teachers -->
                            <div class="p-4 bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center text-white">
                                        <i class="ri-user-star-line text-xl"></i>
                                    </div>
                                    <span class="font-bold text-gray-800">Pembimbing</span>
                                </div>
                                <p class="text-gray-700 ml-13" x-text="projectData.teachers.length + ' terpilih'"></p>
                            </div>
                            
                            <!-- Price -->
                            <div class="p-4 bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center text-white">
                                        <i class="ri-money-dollar-circle-line text-xl"></i>
                                    </div>
                                    <span class="font-bold text-gray-800">Estimasi Harga</span>
                                </div>
                                <p class="text-gray-700 ml-13 font-semibold" x-text="projectData.price ? 'Rp ' + new Intl.NumberFormat('id-ID').format(projectData.price) : 'Tidak diisi'"></p>
                            </div>
                            
                            <!-- Status -->
                            <div class="p-4 bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md transition-all">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg flex items-center justify-center text-white">
                                        <i class="ri-eye-line text-xl"></i>
                                    </div>
                                    <span class="font-bold text-gray-800">Status Publikasi</span>
                                </div>
                                <div class="ml-13">
                                    <span x-show="projectData.status === 'draft'" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold">
                                        <i class="ri-draft-line"></i>Draft
                                    </span>
                                    <span x-show="projectData.status === 'published'" class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-sm font-semibold">
                                        <i class="ri-eye-line"></i>Published
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Footer -->
                <div class="sticky bottom-0 bg-white border-t-2 border-gray-200 px-6 py-4 shadow-lg">
                    <div class="flex justify-between items-center gap-4">
                        <button type="button" 
                                @click="prevProjectStep()" 
                                x-show="currentStep > 1"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all flex items-center gap-2">
                            <i class="ri-arrow-left-line"></i>Kembali
                        </button>
                        
                        <button type="button" 
                                @click="showIndividualProjectModal = false; resetProjectModal()" 
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all">
                            Batal
                        </button>
                        
                        <button type="button" 
                                @click="nextProjectStep()" 
                                x-show="currentStep < totalSteps"
                                :disabled="!canProceedToNextStep()"
                                :class="!canProceedToNextStep() ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-[#b01116] hover:bg-[#8d0d11] shadow-md hover:shadow-lg'"
                                class="px-8 py-3 text-white rounded-lg font-semibold transition-all flex items-center gap-2">
                            <span x-show="canProceedToNextStep()">Selanjutnya</span>
                            <span x-show="!canProceedToNextStep()" x-text="getValidationMessage()"></span>
                            <i class="ri-arrow-right-line" x-show="canProceedToNextStep()"></i>
                        </button>
                        
                        <button type="submit" 
                                x-show="currentStep === totalSteps"
                                :class="'bg-gradient-to-r from-[#b01116] to-[#8d0d11] hover:from-[#8d0d11] hover:to-[#b01116] shadow-lg hover:shadow-xl'"
                                class="px-8 py-3 text-white rounded-lg font-semibold transition-all flex items-center gap-2">
                            <i class="ri-save-line"></i>
                            <span>Buat Proyek</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<!-- Team Project Modal -->
<template x-teleport="body">
    <div x-show="showTeamProjectModal"
         x-transition
         @keydown.escape.window="showTeamProjectModal = false; resetProjectModal()"
         @click.self="showTeamProjectModal = false; resetProjectModal()"
         class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         role="dialog" aria-modal="true" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
            
            <!-- Modal Header with Enhanced Progress -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-5 z-10">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="ri-team-line text-[#b01116]"></i>
                        Inisiasi Proyek Tim
                    </h2>
                    <button @click="showTeamProjectModal = false; resetProjectModal()" 
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>
                
                <!-- Enhanced Progress Steps -->
                <div class="flex items-center justify-center gap-2">
                    <template x-for="step in totalSteps" :key="step">
                        <div class="flex items-center gap-2">
                            <div class="relative w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-sm"
                                 :class="step < currentStep ? 'bg-[#8d0d11] text-white' : step === currentStep ? 'bg-[#b01116] text-white ring-4 ring-red-100 scale-110' : 'bg-gray-200 text-gray-500'">
                                <span x-show="step < currentStep">
                                    <i class="ri-check-line text-lg"></i>
                                </span>
                                <span x-show="step >= currentStep" x-text="step"></span>
                            </div>
                            <div x-show="step < totalSteps"
                                 class="w-20 h-1.5 transition-all duration-300 rounded-full" 
                                 :class="step < currentStep ? 'bg-[#8d0d11]' : 'bg-gray-200'"></div>
                        </div>
                    </template>
                </div>
                
                <div class="text-center mt-3 text-sm font-medium text-gray-700">
                    <span x-show="currentStep === 1" class="flex items-center justify-center gap-2">
                        <i class="ri-information-line"></i>
                        Langkah 1: Informasi Proyek Tim
                    </span>
                    <span x-show="currentStep === 2" class="flex items-center justify-center gap-2">
                        <i class="ri-price-tag-3-line"></i>
                        Langkah 2: Kategori, Mata Kuliah & Pembimbing
                    </span>
                    <span x-show="currentStep === 3" class="flex items-center justify-center gap-2">
                        <i class="ri-team-line"></i>
                        Langkah 3: Anggota Tim, Media & Review
                    </span>
                </div>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('student.projects.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="type" value="team">
                
                <!-- Hidden fields for form data -->
                <template x-for="(categoryId, index) in projectData.categories" :key="'team-cat-'+categoryId">
                    <input type="hidden" :name="'categories[' + index + ']'" :value="categoryId">
                </template>
                <template x-for="(subjectId, index) in projectData.subjects" :key="'team-sub-'+subjectId">
                    <input type="hidden" :name="'subjects[' + index + ']'" :value="subjectId">
                </template>
                <template x-for="(teacherId, index) in projectData.teachers" :key="'team-teach-'+teacherId">
                    <input type="hidden" :name="'teachers[' + index + ']'" :value="teacherId">
                </template>
                <template x-for="(memberId, index) in projectData.team_members" :key="'team-mem-'+memberId">
                    <input type="hidden" :name="'team_members[' + index + ']'" :value="memberId">
                </template>
                <template x-for="(position, memberId) in projectData.team_positions" :key="'team-pos-'+memberId">
                    <input type="hidden" :name="'team_positions[' + memberId + ']'" :value="position">
                </template>

                <!-- Step 1: Project Information -->
                <div x-show="currentStep === 1" x-transition class="p-6 space-y-5">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                        <i class="ri-file-text-line text-[#b01116]"></i>
                        Informasi Dasar Proyek Tim
                    </h3>
                    
                    <!-- Project Title -->
                    <div>
                        <label for="team_title" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Judul Proyek <span class="text-[#b01116]">*</span>
                        </label>
                        <input type="text" name="title" id="team_title" x-model="projectData.title" required 
                               :class="projectData.title.trim() === '' ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-[#b01116] focus:border-[#b01116]'"
                               class="w-full px-4 py-3 border-2 rounded-lg transition-all"
                               placeholder="Masukkan judul proyek tim yang menarik...">
                        <p x-show="projectData.title.trim() === ''" class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <i class="ri-error-warning-line"></i>
                            Judul proyek wajib diisi
                        </p>
                    </div>

                    <!-- Project Description -->
                    <div>
                        <label for="team_description" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Deskripsi Proyek
                        </label>
                        <textarea name="description" id="team_description" x-model="projectData.description" rows="6" 
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all resize-none" 
                                  placeholder="Jelaskan detail proyek tim Anda, peran masing-masing anggota, tujuan, dan fitur utama..."></textarea>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <i class="ri-lightbulb-line"></i>
                            Tips: Jelaskan bagaimana tim Anda bekerja sama untuk menciptakan proyek ini
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Project Price -->
                        <div>
                            <label for="team_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                Estimasi Harga Proyek
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="number" name="price" id="team_price" x-model="projectData.price" min="0" step="1000"
                                       class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all"
                                       placeholder="0">
                            </div>
                        </div>

                        <!-- Project Status -->
                        <div>
                            <label for="team_status" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                Status Publikasi <span class="text-[#b01116]">*</span>
                            </label>
                            <select name="status" id="team_status" x-model="projectData.status" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Categories, Subjects & Teachers (Same as Individual) -->
                <div x-show="currentStep === 2" x-transition class="p-6 space-y-8">
                    <!-- CATEGORIES SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-price-tag-3-line text-[#b01116]"></i>
                            Kategori Proyek <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih minimal 1 kategori yang sesuai dengan proyek tim Anda</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchCategory"
                                placeholder="Cari kategori..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="category in filteredCategories" :key="'team-modal-cat-'+category.id">
                                <label 
                                    class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.categories.includes(category.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.categories.includes(category.id)"
                                        @change="toggleCategory(category.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.categories.includes(category.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.categories.includes(category.id)"></i>
                                    </div>
                                    <span 
                                        class="text-sm font-medium transition-colors"
                                        :class="projectData.categories.includes(category.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                        x-text="category.name"></span>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredCategories.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-search-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada kategori yang sesuai</p>
                            <p class="text-sm mt-1">Coba kata kunci lain atau tambahkan kategori baru</p>
                        </div>
                        
                        <!-- Add New Button -->
                        <button 
                            type="button"
                            @click="showAddCategory = !showAddCategory"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl" :class="showAddCategory ? 'rotate-45 transition-transform' : ''"></i>
                            <span x-text="showAddCategory ? 'Batal Tambah' : 'Tambah Kategori Baru'"></span>
                        </button>
                        
                        <!-- Inline Create Form -->
                        <div x-show="showAddCategory" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Kategori Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newCategory.name"
                                    placeholder="Nama Kategori *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <textarea 
                                    x-model="newCategory.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createCategory()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                    <i class="ri-save-line"></i>
                                    Simpan Kategori
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 rounded-lg border"
                             :class="projectData.categories.length === 0 ? 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200' : 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200'">
                            <div class="flex items-center gap-3"
                                 :class="projectData.categories.length === 0 ? 'text-red-700' : 'text-gray-700'">
                                <i :class="projectData.categories.length === 0 ? 'ri-error-warning-line' : 'ri-checkbox-circle-line'" class="text-xl"></i>
                                <div class="flex-1">
                                    <div class="font-semibold">
                                        <span x-show="projectData.categories.length === 0">Pilih minimal 1 kategori untuk melanjutkan</span>
                                        <span x-show="projectData.categories.length > 0" x-text="projectData.categories.length + ' kategori dipilih'"></span>
                                    </div>
                                    <div class="text-xs opacity-75" x-show="projectData.categories.length > 0">
                                        Kategori membantu investor menemukan proyek tim Anda
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBJECTS SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-book-open-line text-[#b01116]"></i>
                            Mata Kuliah (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih mata kuliah yang berkaitan dengan proyek tim ini</p>
                        
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchSubject"
                                placeholder="Cari mata kuliah atau kode..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="subject in filteredSubjects" :key="'team-modal-sub-'+subject.id">
                                <label 
                                    class="flex flex-col p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.subjects.includes(subject.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only" 
                                            :checked="projectData.subjects.includes(subject.id)"
                                            @change="toggleSubject(subject.id)">
                                        <div 
                                            class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                            :class="projectData.subjects.includes(subject.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                            <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.subjects.includes(subject.id)"></i>
                                        </div>
                                        <span 
                                            class="text-sm font-medium transition-colors flex-1"
                                            :class="projectData.subjects.includes(subject.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                            x-text="subject.name"></span>
                                    </div>
                                    <template x-if="subject.code">
                                        <span class="text-xs text-gray-500 ml-8 mt-1" x-text="'Kode: ' + subject.code"></span>
                                    </template>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredSubjects.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-book-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada mata kuliah yang sesuai</p>
                        </div>
                        
                        <button 
                            type="button"
                            @click="showAddSubject = !showAddSubject"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddSubject ? 'Batal Tambah' : 'Tambah Mata Kuliah Baru'"></span>
                        </button>
                        
                        <div x-show="showAddSubject" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Mata Kuliah Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newSubject.name"
                                    placeholder="Nama Mata Kuliah *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newSubject.code"
                                    placeholder="Kode Mata Kuliah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <textarea 
                                    x-model="newSubject.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createSubject()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Mata Kuliah
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#b01116] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.subjects.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Mata Kuliah Dipilih</span>
                            </div>
                        </div>
                    </div>

                    <!-- TEACHERS SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-user-star-line text-[#b01116]"></i>
                            Dosen/Guru Pembimbing (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih dosen atau guru yang membimbing proyek tim ini</p>
                        
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchTeacher"
                                placeholder="Cari nama atau NIP..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <div class="space-y-2 max-h-72 overflow-y-auto p-1">
                            <template x-for="teacher in filteredTeachers" :key="'team-modal-teach-'+teacher.id">
                                <label 
                                    class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.teachers.includes(teacher.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.teachers.includes(teacher.id)"
                                        @change="toggleTeacher(teacher.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 mt-0.5 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.teachers.includes(teacher.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.teachers.includes(teacher.id)"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div 
                                            class="font-semibold transition-colors"
                                            :class="projectData.teachers.includes(teacher.id) ? 'text-[#b01116]' : 'text-gray-800'"
                                            x-text="teacher.name"></div>
                                        <template x-if="teacher.nip">
                                            <div class="text-sm text-gray-600 mt-0.5">NIP: <span x-text="teacher.nip"></span></div>
                                        </template>
                                        <template x-if="teacher.institution">
                                            <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <i class="ri-building-line"></i>
                                                <span x-text="teacher.institution"></span>
                                            </div>
                                        </template>
                                    </div>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredTeachers.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-user-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada dosen/guru yang sesuai</p>
                        </div>
                        
                        <button 
                            type="button"
                            @click="showAddTeacher = !showAddTeacher"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddTeacher ? 'Batal Tambah' : 'Tambah Dosen/Guru Baru'"></span>
                        </button>
                        
                        <div x-show="showAddTeacher" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border-2 border-blue-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Dosen/Guru Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.name"
                                    placeholder="Nama Lengkap *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.nip"
                                    placeholder="NIP/NIDN (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <div class="grid grid-cols-2 gap-3">
                                    <input 
                                        type="email" 
                                        x-model="newTeacher.email"
                                        placeholder="Email (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                    <input 
                                        type="text" 
                                        x-model="newTeacher.phone_number"
                                        placeholder="No. Telepon (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                </div>
                                <input 
                                    type="text" 
                                    x-model="newTeacher.institution"
                                    placeholder="Institusi/Sekolah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <button 
                                    type="button"
                                    @click="createTeacher()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Dosen/Guru
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#8d0d11] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.teachers.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Pembimbing Dipilih</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Team Members, Media & Review -->
                <div x-show="currentStep === 3" x-transition class="p-6 space-y-6">
                    <!-- TEAM MEMBERS SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-team-line text-[#b01116]"></i>
                            Anggota Tim <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Anda akan menjadi leader tim secara otomatis. Pilih anggota tim lainnya:</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchStudent"
                                placeholder="Cari pelajar berdasarkan nama, username, atau NIM..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Team Members List -->
                        <div class="space-y-3 max-h-96 overflow-y-auto p-1">
                            <template x-for="student in filteredStudents" :key="'team-student-'+student.id">
                                <div 
                                    class="p-4 border-2 rounded-lg transition-all duration-200"
                                    :class="projectData.team_members.includes(student.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    
                                    <!-- Student Info with Checkbox -->
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only" 
                                            :checked="projectData.team_members.includes(student.id)"
                                            @change="toggleTeamMember(student.id)">
                                        
                                        <div 
                                            class="w-5 h-5 rounded border-2 flex-shrink-0 mt-1 flex items-center justify-center transition-all"
                                            :class="projectData.team_members.includes(student.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300'">
                                            <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.team_members.includes(student.id)"></i>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 flex-1">
                                            <!-- Avatar -->
                                            <template x-if="student.user.avatar">
                                                <img :src="student.user.avatar_url" alt="Avatar" class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200">
                                            </template>
                                            <template x-if="!student.user.avatar">
                                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-lg font-bold ring-2 ring-gray-200">
                                                    <span x-text="student.user.username.charAt(0).toUpperCase()"></span>
                                                </div>
                                            </template>
                                            
                                            <!-- Student Details -->
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-800" x-text="student.user.full_name || student.user.username"></div>
                                                <div class="text-sm text-gray-600">@<span x-text="student.user.username"></span></div>
                                                <template x-if="student.student_id">
                                                    <div class="text-xs text-gray-500 mt-0.5">NIM: <span x-text="student.student_id"></span></div>
                                                </template>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <!-- Position Input (Shows when selected) -->
                                    <div 
                                        x-show="projectData.team_members.includes(student.id)" 
                                        x-transition
                                        class="mt-4 pl-8 space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">
                                            Posisi dalam Tim <span class="text-[#b01116]">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            x-model="projectData.team_positions[student.id]"
                                            placeholder="Contoh: Frontend Developer, UI Designer, Project Manager, Data Analyst"
                                            class="w-full px-3 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent text-sm">
                                        <p class="text-xs text-gray-500 flex items-center gap-1">
                                            <i class="ri-information-line"></i>
                                            Jelaskan peran dan tanggung jawab spesifik anggota ini dalam tim
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div x-show="filteredStudents.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-user-search-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada pelajar yang sesuai</p>
                            <p class="text-sm mt-1">Coba kata kunci lain</p>
                        </div>
                        
                        <div class="mt-4 p-3 rounded-lg border"
                             :class="projectData.team_members.length < 1 ? 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200' : 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-200'">
                            <div class="flex items-center gap-3"
                                 :class="projectData.team_members.length < 1 ? 'text-red-700' : 'text-gray-700'">
                                <i :class="projectData.team_members.length < 1 ? 'ri-error-warning-line' : 'ri-team-line'" class="text-xl"></i>
                                <div class="flex-1">
                                    <div class="font-semibold">
                                        <span x-show="projectData.team_members.length < 1">Pilih minimal 1 anggota tim untuk melanjutkan</span>
                                        <span x-show="projectData.team_members.length >= 1" x-text="(projectData.team_members.length + 1) + ' total anggota tim'"></span>
                                    </div>
                                    <div class="text-xs opacity-75">
                                        <span x-show="projectData.team_members.length < 1">Tim minimal terdiri dari 2 orang (leader + 1 anggota)</span>
                                        <span x-show="projectData.team_members.length >= 1">Termasuk Anda sebagai leader</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Media Upload -->
                    <div x-data="mediaPreview('team_media')">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Upload Media (Opsional)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-[#b01116] hover:bg-red-50 transition-all">
                            <i class="ri-upload-cloud-2-line text-6xl text-gray-400 mb-3"></i>
                            <div class="text-sm text-gray-600 mb-3 font-medium">
                                Drop files here or click to upload
                            </div>
                            <input type="file" 
                                   name="media[]" 
                                   multiple 
                                   accept="image/*,video/*" 
                                   class="hidden" 
                                   id="team_media"
                                   @change="handleFiles($event.target.files)">
                            <label for="team_media" class="inline-flex items-center gap-2 cursor-pointer bg-[#b01116] text-white px-6 py-3 rounded-lg hover:bg-[#8d0d11] transition-colors font-medium shadow-md hover:shadow-lg">
                                <i class="ri-folder-open-line"></i>
                                Choose Files
                            </label>
                            <div class="text-xs text-gray-500 mt-3">
                                Max 10 files • Each up to 10MB • JPG, PNG, MP4, MOV
                            </div>
                        </div>
                        
                        <!-- Image Previews -->
                        <div x-show="previews.length > 0" class="mt-4">
                            <p class="text-sm font-medium text-gray-700 mb-3">
                                Selected Files (<span x-text="previews.length"></span>)
                                <span class="text-xs text-gray-500 ml-2">• First image will be the main image</span>
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <template x-for="(preview, index) in previews" :key="index">
                                    <div class="relative group">
                                        <div class="aspect-square rounded-lg overflow-hidden border-2"
                                             :class="index === 0 ? 'border-[#b01116]' : 'border-gray-300'">
                                            <img :src="preview.url" 
                                                 :alt="preview.name" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <button type="button"
                                                @click="removeFile(index)"
                                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700">
                                            <i class="ri-close-line text-sm"></i>
                                        </button>
                                        <div x-show="index === 0" 
                                             class="absolute bottom-0 left-0 right-0 bg-[#b01116] text-white text-xs py-1 text-center font-medium">
                                            Main Image
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Review Project Data -->
                    <div class="bg-gradient-to-br from-red-50 via-pink-50 to-red-50 rounded-2xl p-8 border border-red-100 shadow-lg">
                        <!-- Header with Icon -->
                        <div class="flex items-center gap-4 mb-6 pb-5 border-b border-red-100">
                            <div class="flex-shrink-0 w-14 h-14 bg-white/80 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-md ring-2 ring-red-100">
                                <i class="ri-file-list-3-line text-2xl text-[#b01116]"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-xl">Review Proyek Tim Anda</h4>
                                <p class="text-sm text-gray-600 mt-0.5">Periksa kembali detail proyek sebelum submit</p>
                            </div>
                        </div>

                        <!-- Review Items Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- Title -->
                            <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="ri-file-text-line text-lg text-[#b01116]"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Judul Proyek</span>
                                        <p class="text-gray-800 font-semibold break-words" x-text="projectData.title || 'Belum diisi'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Categories -->
                            <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow border border-gray-100" :class="projectData.categories.length === 0 ? 'ring-2 ring-red-200' : ''">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="ri-price-tag-3-line text-lg text-blue-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Kategori</span>
                                        <p class="text-gray-800 font-semibold">
                                            <span x-text="projectData.categories.length"></span> kategori terpilih
                                        </p>
                                        <p x-show="projectData.categories.length === 0" class="text-xs text-[#b01116] font-medium mt-1 flex items-center gap-1">
                                            <i class="ri-error-warning-line"></i> Minimal 1 kategori wajib dipilih!
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Subjects -->
                            <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="ri-book-open-line text-lg text-purple-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Mata Kuliah</span>
                                        <p class="text-gray-800 font-semibold" x-text="projectData.subjects.length + ' mata kuliah terpilih'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Teachers -->
                            <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="ri-user-star-line text-lg text-green-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Dosen Pembimbing</span>
                                        <p class="text-gray-800 font-semibold" x-text="projectData.teachers.length + ' dosen terpilih'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Members -->
                            <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow border border-gray-100" :class="projectData.team_members.length === 0 ? 'ring-2 ring-red-200' : ''">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="ri-team-line text-lg text-indigo-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Anggota Tim</span>
                                        <p class="text-gray-800 font-semibold">
                                            <span x-text="projectData.team_members.length + 1"></span> orang total
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">Termasuk Anda sebagai leader</p>
                                        <p x-show="projectData.team_members.length === 0" class="text-xs text-[#b01116] font-medium mt-1 flex items-center gap-1">
                                            <i class="ri-error-warning-line"></i> Minimal 1 anggota tambahan diperlukan!
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <i class="ri-money-dollar-circle-line text-lg text-yellow-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Estimasi Harga</span>
                                        <p class="text-gray-800 font-semibold" x-text="projectData.price ? 'Rp ' + new Intl.NumberFormat('id-ID').format(projectData.price) : 'Tidak diisi'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow border border-gray-100 lg:col-span-2">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="ri-eye-line text-lg text-gray-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Status Publikasi</span>
                                        <div>
                                            <span x-show="projectData.status === 'draft'" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold">
                                                <i class="ri-draft-line"></i> Draft
                                            </span>
                                            <span x-show="projectData.status === 'published'" class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-semibold">
                                                <i class="ri-check-double-line"></i> Published
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Footer -->
                <div class="sticky bottom-0 bg-white border-t-2 border-gray-200 px-6 py-4 shadow-lg">
                    <div class="flex justify-between items-center gap-4">
                        <button type="button" 
                                @click="prevProjectStep()" 
                                x-show="currentStep > 1"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all flex items-center gap-2">
                            <i class="ri-arrow-left-line"></i>Kembali
                        </button>
                        
                        <button type="button" 
                                @click="showTeamProjectModal = false; resetProjectModal()" 
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all">
                            Batal
                        </button>
                        
                        <button type="button" 
                                @click="nextProjectStep()" 
                                x-show="currentStep < totalSteps"
                                :disabled="!canProceedToNextStep()"
                                :class="!canProceedToNextStep() ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-[#b01116] hover:bg-[#8d0d11] shadow-md hover:shadow-lg'"
                                class="px-8 py-3 text-white rounded-lg font-semibold transition-all flex items-center gap-2">
                            <span x-show="canProceedToNextStep()">Selanjutnya</span>
                            <span x-show="!canProceedToNextStep()" x-text="getValidationMessage()"></span>
                            <i class="ri-arrow-right-line" x-show="canProceedToNextStep()"></i>
                        </button>
                        
                        <button type="submit" 
                                x-show="currentStep === totalSteps"
                                :class=""
                                class="px-8 py-3 text-white rounded-lg font-semibold transition-all flex items-center gap-2 bg-gradient-to-r from-[#b01116] to-[#8d0d11] hover:from-[#8d0d11] hover:to-[#b01116] shadow-lg hover:shadow-xl">
                            <i class="ri-save-line"></i>
                            <span>Inisiasi Proyek Tim</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<!-- Edit Project Modal (Works for both Individual and Team) -->
<template x-teleport="body">
    <div x-show="showEditProjectModal"
         x-transition
         @keydown.escape.window="showEditProjectModal = false; resetProjectModal()"
         @click.self="showEditProjectModal = false; resetProjectModal()"
         class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         role="dialog" aria-modal="true" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
            
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-5 z-10">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="ri-edit-line text-[#b01116]"></i>
                        <span>Edit Proyek <span x-text="projectType === 'team' ? 'Tim' : 'Individu'" class="text-[#b01116]"></span></span>
                    </h2>
                    <button @click="showEditProjectModal = false; resetProjectModal()" 
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>
                
                <!-- Progress Steps -->
                <div class="flex items-center justify-center gap-2">
                    <template x-for="step in totalSteps" :key="step">
                        <div class="flex items-center gap-2">
                            <div class="relative w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-sm"
                                 :class="step < currentStep ? 'bg-[#8d0d11] text-white' : step === currentStep ? 'bg-[#b01116] text-white ring-4 ring-red-100 scale-110' : 'bg-gray-200 text-gray-500'">
                                <span x-show="step < currentStep">
                                    <i class="ri-check-line text-lg"></i>
                                </span>
                                <span x-show="step >= currentStep" x-text="step"></span>
                            </div>
                            <div x-show="step < totalSteps"
                                 class="w-20 h-1.5 transition-all duration-300 rounded-full" 
                                 :class="step < currentStep ? 'bg-[#8d0d11]' : 'bg-gray-200'"></div>
                        </div>
                    </template>
                </div>
                
                <div class="text-center mt-3 text-sm font-medium text-gray-700">
                    <span x-show="currentStep === 1" class="flex items-center justify-center gap-2">
                        <i class="ri-information-line"></i>
                        Langkah 1: Informasi Proyek
                    </span>
                    <span x-show="currentStep === 2" class="flex items-center justify-center gap-2">
                        <i class="ri-price-tag-3-line"></i>
                        Langkah 2: Kategori, Mata Kuliah & Pembimbing
                    </span>
                    <span x-show="currentStep === 3" class="flex items-center justify-center gap-2">
                        <i class="ri-upload-cloud-2-line"></i>
                        <span x-text="projectType === 'team' ? 'Langkah 3: Anggota Tim & Review' : 'Langkah 3: Media & Review'"></span>
                    </span>
                </div>
            </div>

            <!-- Modal Body -->
            <div>
                <!-- Step 1: Project Information -->
                <div x-show="currentStep === 1" x-transition class="p-6 space-y-5">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                        <i class="ri-file-text-line text-[#b01116]"></i>
                        Informasi Dasar Proyek
                    </h3>
                    
                    <!-- Project Title -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Judul Proyek <span class="text-[#b01116]">*</span>
                        </label>
                        <input type="text" x-model="projectData.title" required 
                               :class="projectData.title.trim() === '' ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-[#b01116] focus:border-[#b01116]'"
                               class="w-full px-4 py-3 border-2 rounded-lg transition-all"
                               placeholder="Masukkan judul proyek yang menarik...">
                        <p x-show="projectData.title.trim() === ''" class="text-xs text-red-500 mt-1 flex items-center gap-1">
                            <i class="ri-error-warning-line"></i>
                            Judul proyek wajib diisi
                        </p>
                    </div>

                    <!-- Project Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                            Deskripsi Proyek
                        </label>
                        <textarea x-model="projectData.description" rows="6" 
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all resize-none" 
                                  placeholder="Jelaskan detail proyek Anda, tujuan, fitur utama, dan hal menarik lainnya..."></textarea>
                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                            <i class="ri-lightbulb-line"></i>
                            Tips: Deskripsi yang detail akan menarik lebih banyak investor
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Project Price -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Estimasi Harga (Rp)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input type="number" x-model="projectData.price" min="0" step="1000"
                                       class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all"
                                       placeholder="0">
                            </div>
                        </div>

                        <!-- Project Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                Status Publikasi <span class="text-[#b01116]">*</span>
                            </label>
                            <select x-model="projectData.status" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-[#b01116] transition-all">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Categories, Subjects & Teachers -->
                <div x-show="currentStep === 2" x-transition class="p-6 space-y-8">
                    <!-- CATEGORIES SECTION -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-price-tag-3-line text-[#b01116]"></i>
                            Kategori Proyek <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Edit kategori proyek Anda</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchCategory"
                                placeholder="Cari kategori..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="category in filteredCategories" :key="category.id">
                                <label 
                                    class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.categories.includes(category.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.categories.includes(category.id)"
                                        @change="toggleCategory(category.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.categories.includes(category.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.categories.includes(category.id)"></i>
                                    </div>
                                    <span 
                                        class="text-sm font-medium transition-colors"
                                        :class="projectData.categories.includes(category.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                        x-text="category.name"></span>
                                </label>
                            </template>
                        </div>
                        
                        <!-- No Results -->
                        <div x-show="filteredCategories.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-search-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada kategori yang sesuai</p>
                            <p class="text-sm mt-1">Coba kata kunci lain atau tambahkan kategori baru</p>
                        </div>
                        
                        <!-- Add New Button -->
                        <button 
                            type="button"
                            @click="showAddCategory = !showAddCategory"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl" :class="showAddCategory ? 'rotate-45 transition-transform' : ''"></i>
                            <span x-text="showAddCategory ? 'Batal Tambah' : 'Tambah Kategori Baru'"></span>
                        </button>
                        
                        <!-- Inline Create Form -->
                        <div x-show="showAddCategory" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Kategori Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newCategory.name"
                                    placeholder="Nama Kategori *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent">
                                <textarea 
                                    x-model="newCategory.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createCategory()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                    <i class="ri-save-line"></i>
                                    Simpan Kategori
                                </button>
                            </div>
                        </div>
                        
                        <!-- Selection Counter -->
                        <div class="mt-4 p-3 rounded-lg border"
                             :class="projectData.categories.length === 0 ? 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200' : 'bg-gradient-to-r from-red-50 to-pink-50 border-red-200'">
                            <div class="flex items-center gap-3"
                                 :class="projectData.categories.length === 0 ? 'text-red-700' : 'text-gray-700'">
                                <i :class="projectData.categories.length === 0 ? 'ri-error-warning-line' : 'ri-checkbox-circle-line'" class="text-xl"></i>
                                <div class="flex-1">
                                    <div class="font-semibold">
                                        <span x-show="projectData.categories.length === 0">Pilih minimal 1 kategori untuk melanjutkan</span>
                                        <span x-show="projectData.categories.length > 0" x-text="projectData.categories.length + ' kategori dipilih'"></span>
                                    </div>
                                    <div class="text-xs opacity-75" x-show="projectData.categories.length > 0">
                                        Kategori membantu investor menemukan proyek Anda
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBJECTS SECTION (Similar structure) -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-book-open-line text-[#b01116]"></i>
                            Mata Kuliah (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Edit mata kuliah yang berkaitan</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchSubject"
                                placeholder="Cari mata kuliah atau kode..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                            <template x-for="subject in filteredSubjects" :key="subject.id">
                                <label 
                                    class="flex flex-col p-3 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.subjects.includes(subject.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only" 
                                            :checked="projectData.subjects.includes(subject.id)"
                                            @change="toggleSubject(subject.id)">
                                        <div 
                                            class="w-5 h-5 rounded border-2 mr-3 flex items-center justify-center transition-all flex-shrink-0"
                                            :class="projectData.subjects.includes(subject.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                            <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.subjects.includes(subject.id)"></i>
                                        </div>
                                        <span 
                                            class="text-sm font-medium transition-colors flex-1"
                                            :class="projectData.subjects.includes(subject.id) ? 'text-[#b01116]' : 'text-gray-700'"
                                            x-text="subject.name"></span>
                                    </div>
                                    <template x-if="subject.code">
                                        <span class="text-xs text-gray-500 ml-8 mt-1" x-text="'Kode: ' + subject.code"></span>
                                    </template>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredSubjects.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-book-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada mata kuliah yang sesuai</p>
                        </div>
                        
                        <!-- Add New Button & Form (Similar to categories) -->
                        <button 
                            type="button"
                            @click="showAddSubject = !showAddSubject"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddSubject ? 'Batal Tambah' : 'Tambah Mata Kuliah Baru'"></span>
                        </button>
                        
                        <div x-show="showAddSubject" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Mata Kuliah Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newSubject.name"
                                    placeholder="Nama Mata Kuliah *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newSubject.code"
                                    placeholder="Kode Mata Kuliah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <textarea 
                                    x-model="newSubject.description"
                                    placeholder="Deskripsi (opsional)"
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] resize-none"></textarea>
                                <button 
                                    type="button"
                                    @click="createSubject()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Mata Kuliah
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#b01116] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.subjects.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Mata Kuliah Dipilih</span>
                            </div>
                        </div>
                    </div>

                    <!-- TEACHERS SECTION (Similar structure) -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-user-star-line text-[#b01116]"></i>
                            Dosen/Guru Pembimbing (Opsional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Edit dosen atau guru pembimbing</p>
                        
                        <!-- Search Box -->
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                            <input 
                                type="text" 
                                x-model="searchTeacher"
                                placeholder="Cari nama atau NIP..."
                                class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent transition-all">
                        </div>
                        
                        <!-- Selection List -->
                        <div class="space-y-2 max-h-72 overflow-y-auto p-1">
                            <template x-for="teacher in filteredTeachers" :key="teacher.id">
                                <label 
                                    class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:shadow-md transition-all duration-200 group"
                                    :class="projectData.teachers.includes(teacher.id) ? 'border-[#b01116] bg-red-50 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <input 
                                        type="checkbox" 
                                        class="sr-only" 
                                        :checked="projectData.teachers.includes(teacher.id)"
                                        @change="toggleTeacher(teacher.id)">
                                    <div 
                                        class="w-5 h-5 rounded border-2 mr-3 mt-0.5 flex items-center justify-center transition-all flex-shrink-0"
                                        :class="projectData.teachers.includes(teacher.id) ? 'bg-[#b01116] border-[#b01116] scale-110' : 'border-gray-300 group-hover:border-gray-400'">
                                        <i class="ri-check-line text-white text-sm font-bold" x-show="projectData.teachers.includes(teacher.id)"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div 
                                            class="font-semibold transition-colors"
                                            :class="projectData.teachers.includes(teacher.id) ? 'text-[#b01116]' : 'text-gray-800'"
                                            x-text="teacher.name"></div>
                                        <template x-if="teacher.nip">
                                            <div class="text-sm text-gray-600 mt-0.5">NIP: <span x-text="teacher.nip"></span></div>
                                        </template>
                                        <template x-if="teacher.institution">
                                            <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <i class="ri-building-line"></i>
                                                <span x-text="teacher.institution"></span>
                                            </div>
                                        </template>
                                    </div>
                                </label>
                            </template>
                        </div>
                        
                        <div x-show="filteredTeachers.length === 0" class="text-center py-12 text-gray-500">
                            <i class="ri-user-line text-5xl mb-3 opacity-50"></i>
                            <p class="font-medium">Tidak ada dosen/guru yang sesuai</p>
                        </div>
                        
                        <!-- Add New Button & Form -->
                        <button 
                            type="button"
                            @click="showAddTeacher = !showAddTeacher"
                            class="w-full mt-3 py-3 border-2 border-dashed rounded-lg text-gray-600 hover:border-[#b01116] hover:text-[#b01116] hover:bg-red-50 transition-all flex items-center justify-center gap-2 font-medium">
                            <i class="ri-add-circle-line text-xl"></i>
                            <span x-text="showAddTeacher ? 'Batal Tambah' : 'Tambah Dosen/Guru Baru'"></span>
                        </button>
                        
                        <div x-show="showAddTeacher" x-transition x-collapse class="mt-4 p-4 bg-gradient-to-br from-red-50 to-pink-50 rounded-lg border-2 border-red-200">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ri-add-line text-[#b01116] text-xl"></i>
                                Tambah Dosen/Guru Baru
                            </h5>
                            <div class="space-y-3">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.name"
                                    placeholder="Nama Lengkap *"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <input 
                                    type="text" 
                                    x-model="newTeacher.nip"
                                    placeholder="NIP/NIDN (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <div class="grid grid-cols-2 gap-3">
                                    <input 
                                        type="email" 
                                        x-model="newTeacher.email"
                                        placeholder="Email (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                    <input 
                                        type="text" 
                                        x-model="newTeacher.phone_number"
                                        placeholder="No. Telepon (opsional)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                </div>
                                <input 
                                    type="text" 
                                    x-model="newTeacher.institution"
                                    placeholder="Institusi/Sekolah (opsional)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                                <button 
                                    type="button"
                                    @click="createTeacher()"
                                    class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white py-2.5 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                                    <i class="ri-save-line"></i>
                                    Simpan Dosen/Guru
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200">
                            <div class="flex items-center gap-3 text-red-700">
                                <div class="w-10 h-10 rounded-full bg-[#8d0d11] text-white flex items-center justify-center font-bold shadow-sm">
                                    <span x-text="projectData.teachers.length"></span>
                                </div>
                                <span class="text-sm font-semibold">Pembimbing Dipilih</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Team Members (for Team) or Media (for Individual) -->
                <div x-show="currentStep === 3" x-transition class="p-6 space-y-6">
                    <!-- Team Members Section (Only for Team Projects) -->
                    <div x-show="projectType === 'team'">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-team-line text-[#b01116]"></i>
                            Anggota Tim <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih minimal 1 anggota tim (selain Anda sebagai leader)</p>
                        
                        <div class="relative mb-4">
                            <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="searchStudent" placeholder="Cari pelajar..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116]">
                        </div>
                        
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <template x-for="student in filteredStudents" :key="student.id">
                                <div class="p-4 border-2 rounded-lg transition-all"
                                     :class="projectData.team_members.includes(student.id) ? 'border-[#b01116] bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" class="sr-only" 
                                               :checked="projectData.team_members.includes(student.id)"
                                               @change="toggleTeamMember(student.id)">
                                        <div class="w-5 h-5 rounded border-2 flex-shrink-0 mt-1 flex items-center justify-center"
                                             :class="projectData.team_members.includes(student.id) ? 'bg-[#b01116] border-[#b01116]' : 'border-gray-300'">
                                            <i class="ri-check-line text-white text-sm" x-show="projectData.team_members.includes(student.id)"></i>
                                        </div>
                                        <div class="flex items-center gap-3 flex-1">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-lg font-bold">
                                                <span x-text="student.user.username.charAt(0).toUpperCase()"></span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-800" x-text="student.user.full_name || student.user.username"></div>
                                                <div class="text-sm text-gray-600">@<span x-text="student.user.username"></span></div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <div x-show="projectData.team_members.includes(student.id)" x-transition class="mt-3 pl-8">
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Posisi dalam Tim *</label>
                                        <input type="text" x-model="projectData.team_positions[student.id]"
                                               placeholder="Contoh: Frontend Developer, UI Designer"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] text-sm">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Enhanced Media Management -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-lg">
                            <i class="ri-image-line text-[#b01116]"></i>
                            Kelola Gambar Proyek <span class="text-[#b01116]">*</span>
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">Minimal 1 gambar diperlukan untuk proyek</p>
                        
                        <!-- Existing Images Section -->
                        <div x-show="projectData.existing_images && projectData.existing_images.length > 0" class="mb-6">
                            <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="ri-gallery-line text-[#b01116]"></i>
                                Gambar Saat Ini (<span x-text="projectData.existing_images.length"></span>)
                            </h5>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
                                <template x-for="(image, index) in projectData.existing_images" :key="'existing-' + image.id">
                                    <div class="relative group">
                                        <div class="aspect-square rounded-lg overflow-hidden border-2"
                                             :class="isImageMarkedForDeletion(image.id) ? 'border-red-300 opacity-50' : index === 0 ? 'border-[#b01116] ring-2 ring-red-100' : 'border-gray-300'">
                                            <img :src="image.url" 
                                                 :alt="'Project Image ' + (index + 1)" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button type="button"
                                                    @click="markImageForDeletion(image.id)"
                                                    :class="isImageMarkedForDeletion(image.id) ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                                                    class="text-white rounded-full p-1.5 transition-colors"
                                                    :title="isImageMarkedForDeletion(image.id) ? 'Undo Delete' : 'Mark for Deletion'">
                                                <i :class="isImageMarkedForDeletion(image.id) ? 'ri-arrow-go-back-line' : 'ri-delete-bin-line'" class="text-xs"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Main Image Badge -->
                                        <div x-show="index === 0 && !isImageMarkedForDeletion(image.id)" 
                                             class="absolute bottom-0 left-0 right-0 bg-[#b01116] text-white text-xs py-1 text-center font-medium">
                                            <i class="ri-star-fill mr-1"></i>Gambar Utama
                                        </div>
                                        
                                        <!-- Deletion Overlay -->
                                        <div x-show="isImageMarkedForDeletion(image.id)" 
                                             class="absolute inset-0 bg-red-600/80 flex items-center justify-center rounded-lg">
                                            <div class="text-center text-white">
                                                <i class="ri-delete-bin-line text-2xl mb-1"></i>
                                                <div class="text-sm font-medium">Akan Dihapus</div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Add New Images Section -->
                        <div>
                            <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="ri-add-circle-line text-[#b01116]"></i>
                                Tambah Gambar Baru
                            </h5>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-[#b01116] hover:bg-red-50 transition-all">
                                <i class="ri-upload-cloud-2-line text-4xl text-gray-400 mb-3"></i>
                                <div class="text-sm text-gray-600 mb-3 font-medium">
                                    Drop files here or click to upload
                                </div>
                                <input type="file" 
                                       name="new_media[]" 
                                       multiple 
                                       accept="image/*" 
                                       class="hidden" 
                                       id="edit_media_detail"
                                       @change="handleNewMediaFiles($event)">
                                <label for="edit_media_detail" class="inline-flex items-center gap-2 cursor-pointer bg-[#b01116] text-white px-4 py-2.5 rounded-lg hover:bg-[#8d0d11] transition-colors font-medium shadow-md hover:shadow-lg">
                                    <i class="ri-folder-open-line"></i>
                                    Pilih Gambar
                                </label>
                                <div class="text-xs text-gray-500 mt-3">
                                    Max 10 files • Each up to 10MB • JPG, PNG, GIF, WebP
                                </div>
                            </div>
                            
                            <!-- New Images Preview -->
                            <div x-show="newMediaPreviews.length > 0" class="mt-4">
                                <p class="text-sm font-medium text-gray-700 mb-3">
                                    Gambar Baru (<span x-text="newMediaPreviews.length"></span>)
                                    <span class="text-xs text-gray-500 ml-2">• Akan ditambahkan setelah gambar yang ada</span>
                                </p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    <template x-for="(preview, index) in newMediaPreviews" :key="'new-' + index">
                                        <div class="relative group">
                                            <div class="aspect-square rounded-lg overflow-hidden border-2 border-green-300">
                                                <img :src="preview.url" 
                                                     :alt="preview.name" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                            <button type="button"
                                                    @click="removeNewMediaFile(index)"
                                                    class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700">
                                                <i class="ri-close-line text-xs"></i>
                                            </button>
                                            <div class="absolute bottom-0 left-0 right-0 bg-green-600 text-white text-xs py-1 text-center font-medium">
                                                <i class="ri-add-line mr-1"></i>Baru
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Summary -->
                        <div class="mt-4 p-4 rounded-lg border-2"
                             :class="getTotalImageCount() === 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'">
                            <div class="flex items-center gap-3"
                                 :class="getTotalImageCount() === 0 ? 'text-red-700' : 'text-green-700'">
                                <i :class="getTotalImageCount() === 0 ? 'ri-error-warning-line text-xl' : 'ri-checkbox-circle-line text-xl'"></i>
                                <div class="flex-1">
                                    <div class="font-semibold">
                                        <span x-show="getTotalImageCount() === 0">Minimal 1 gambar diperlukan</span>
                                        <span x-show="getTotalImageCount() > 0">
                                            Total: <span x-text="getTotalImageCount()"></span> gambar
                                        </span>
                                    </div>
                                    <div class="text-sm mt-1" x-show="getTotalImageCount() > 0">
                                        <span x-text="getExistingImagesCount() + ' gambar saat ini'"></span>
                                        <span x-show="newMediaPreviews.length > 0"> • <span x-text="newMediaPreviews.length + ' gambar baru'"></span></span>
                                        <span x-show="projectData.images_to_delete && projectData.images_to_delete.length > 0"> • <span x-text="projectData.images_to_delete.length + ' akan dihapus'"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Review Section with AS-IS vs TO-BE -->
                    <div class="bg-gradient-to-br from-red-50 via-pink-50 to-white rounded-2xl p-6 border-2 border-red-200 shadow-lg">
                        <div class="bg-gradient-to-r from-[#b01116] to-pink-600 text-white rounded-xl p-4 mb-6 shadow-md">
                            <h4 class="font-bold flex items-center gap-3 text-xl">
                                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                    <i class="ri-file-list-3-line text-2xl"></i>
                                </div>
                                <span>Review Perubahan Proyek</span>
                            </h4>
                            <p class="text-red-100 text-sm mt-2 ml-13">Periksa kembali semua perubahan sebelum menyimpan</p>
                        </div>
                        <div class="space-y-4 text-sm">
                            
                            <!-- Title Comparison -->
                            <div class="p-5 rounded-xl border-2 shadow-sm hover:shadow-md transition-all"
                                 :class="hasChanged('title') ? 'bg-gradient-to-br from-yellow-50 to-orange-50 border-yellow-300 ring-2 ring-yellow-200' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                         :class="hasChanged('title') ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-400'">
                                        <i class="ri-file-text-line text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-bold text-gray-800 block">Judul Proyek</span>
                                        <span x-show="hasChanged('title')" class="text-xs bg-gradient-to-r from-yellow-400 to-orange-400 text-white px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1 mt-1">
                                            <i class="ri-edit-line"></i>Diubah
                                        </span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('title') || 'Belum diisi'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.title || 'Belum diisi'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Description Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('description') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-file-text-line" :class="hasChanged('description') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Deskripsi Proyek</span>
                                    <span x-show="hasChanged('description')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 text-sm" x-text="getOriginalValue('description') ? (getOriginalValue('description').substring(0, 100) + (getOriginalValue('description').length > 100 ? '...' : '')) : 'Belum diisi'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 text-sm" x-text="projectData.description ? (projectData.description.substring(0, 100) + (projectData.description.length > 100 ? '...' : '')) : 'Belum diisi'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('price') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-money-dollar-circle-line" :class="hasChanged('price') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Estimasi Harga</span>
                                    <span x-show="hasChanged('price')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('price') ? 'Rp ' + new Intl.NumberFormat('id-ID').format(getOriginalValue('price')) : 'Tidak diisi'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.price ? 'Rp ' + new Intl.NumberFormat('id-ID').format(projectData.price) : 'Tidak diisi'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('status') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-eye-line" :class="hasChanged('status') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Status Publikasi</span>
                                    <span x-show="hasChanged('status')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium"
                                              :class="getOriginalValue('status') === 'published' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'"
                                              x-text="getOriginalValue('status') === 'draft' ? 'Draft' : getOriginalValue('status') === 'published' ? 'Published' : 'Archived'"></span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium"
                                              :class="projectData.status === 'published' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'"
                                              x-text="projectData.status === 'draft' ? 'Draft' : projectData.status === 'published' ? 'Published' : 'Archived'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Categories Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('categories') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-price-tag-3-line" :class="hasChanged('categories') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Kategori</span>
                                    <span x-show="hasChanged('categories')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('categories') + ' kategori dipilih'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.categories.length + ' kategori dipilih'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Subjects Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('subjects') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-book-open-line" :class="hasChanged('subjects') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Mata Kuliah</span>
                                    <span x-show="hasChanged('subjects')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('subjects') + ' mata kuliah terpilih'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.subjects.length + ' mata kuliah terpilih'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Teachers Comparison -->
                            <div class="p-4 rounded-lg border"
                                 :class="hasChanged('teachers') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-user-star-line" :class="hasChanged('teachers') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Pembimbing</span>
                                    <span x-show="hasChanged('teachers')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('teachers') + ' pembimbing terpilih'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.teachers.length + ' pembimbing terpilih'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Members Comparison (for team projects) -->
                            <div x-show="projectType === 'team'" 
                                 class="p-4 rounded-lg border"
                                 :class="hasChanged('team_members') ? 'bg-yellow-50 border-yellow-300' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="ri-team-line" :class="hasChanged('team_members') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <span class="font-semibold text-gray-700">Anggota Tim</span>
                                    <span x-show="hasChanged('team_members')" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">Berubah</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sebelum:</p>
                                        <p class="text-gray-600 font-medium" x-text="getOriginalValue('team_members') + ' anggota (+ 1 leader)'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Sesudah:</p>
                                        <p class="text-gray-800 font-medium" x-text="projectData.team_members.length + ' anggota (+ 1 leader)'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Images Comparison -->
                            <div class="p-5 rounded-xl border-2 shadow-sm hover:shadow-md transition-all"
                                 :class="hasImagesChanged() ? 'bg-gradient-to-br from-yellow-50 to-orange-50 border-yellow-300 ring-2 ring-yellow-200' : 'bg-white border-gray-200'">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                         :class="hasImagesChanged() ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-400'">
                                        <i class="ri-image-line text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-bold text-gray-800 block">Gambar Proyek</span>
                                        <span x-show="hasImagesChanged()" class="text-xs bg-gradient-to-r from-yellow-400 to-orange-400 text-white px-3 py-1 rounded-full font-semibold inline-flex items-center gap-1 mt-1">
                                            <i class="ri-edit-line"></i>Diubah
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Before Section -->
                                <div class="mb-5">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sebelum:</span>
                                        <div class="flex-1 h-px bg-gradient-to-r from-gray-300 to-transparent"></div>
                                    </div>
                                    <div class="bg-white/70 backdrop-blur-sm rounded-lg p-4 border border-gray-200">
                                        <p class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                            <i class="ri-gallery-line text-[#b01116]"></i>
                                            <span x-text="(projectData.existing_images ? projectData.existing_images.length : 0) + ' Gambar'"></span>
                                        </p>
                                        <div x-show="projectData.existing_images && projectData.existing_images.length > 0" class="grid grid-cols-4 md:grid-cols-6 gap-2">
                                            <template x-for="(image, index) in (projectData.existing_images || [])" :key="'review-existing-' + index">
                                                <div class="aspect-square rounded-lg border-2 overflow-hidden shadow-sm hover:shadow-md transition-all"
                                                     :class="index === 0 ? 'border-[#b01116] ring-2 ring-red-200' : 'border-gray-300'">
                                                    <img :src="image.url || image.file_path" class="w-full h-full object-cover" :alt="'Image ' + (index + 1)">
                                                    <div x-show="index === 0" class="absolute top-0 right-0 bg-[#b01116] text-white text-xs px-1.5 py-0.5 rounded-bl-lg font-semibold">
                                                        <i class="ri-star-fill"></i>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <p x-show="!projectData.existing_images || projectData.existing_images.length === 0" class="text-sm text-gray-500 italic">Tidak ada gambar</p>
                                    </div>
                                </div>
                                
                                <!-- After Section -->
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sesudah:</span>
                                        <div class="flex-1 h-px bg-gradient-to-r from-gray-300 to-transparent"></div>
                                    </div>
                                    <div class="bg-gradient-to-br from-white to-red-50/30 backdrop-blur-sm rounded-lg p-4 border-2 border-[#b01116]/20">
                                        <p class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                            <i class="ri-gallery-line text-[#b01116]"></i>
                                            <span x-text="getTotalImagesCount() + ' Gambar Total'"></span>
                                        </p>
                                        
                                        <!-- Summary Badges -->
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <div x-show="getExistingImagesCount() > 0" class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm">
                                                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                                <span x-text="getExistingImagesCount()"></span>
                                                <span>Tetap</span>
                                            </div>
                                            <div x-show="newMediaPreviews.length > 0" class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm">
                                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                                <span x-text="newMediaPreviews.length"></span>
                                                <span>Baru</span>
                                            </div>
                                            <div x-show="getDeletedImagesCount() > 0" class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm">
                                                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                                <span x-text="getDeletedImagesCount()"></span>
                                                <span>Dihapus</span>
                                            </div>
                                        </div>
                                        
                                        <!-- All Images Grid -->
                                        <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                                            <!-- Existing Images (not deleted) -->
                                            <template x-for="(image, index) in (projectData.existing_images || [])" :key="'review-after-existing-' + index">
                                                <div x-show="!projectData.images_to_delete || !projectData.images_to_delete.includes(image.id || index)" 
                                                     class="aspect-square rounded-lg border-2 border-blue-300 overflow-hidden shadow-sm hover:shadow-md transition-all relative group"
                                                     :class="index === 0 ? 'ring-2 ring-[#b01116]' : ''">
                                                    <img :src="image.url || image.file_path" class="w-full h-full object-cover" :alt="'Image ' + (index + 1)">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-blue-900/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-1">
                                                        <span class="text-white text-xs font-semibold">Tetap</span>
                                                    </div>
                                                    <div x-show="index === 0" class="absolute top-0 right-0 bg-[#b01116] text-white text-xs px-1.5 py-0.5 rounded-bl-lg font-semibold">
                                                        <i class="ri-star-fill"></i>
                                                    </div>
                                                </div>
                                            </template>
                                            
                                            <!-- New Images -->
                                            <template x-for="(preview, index) in newMediaPreviews" :key="'review-after-new-' + index">
                                                <div class="aspect-square rounded-lg border-2 border-green-400 overflow-hidden shadow-sm hover:shadow-md transition-all relative group">
                                                    <img :src="preview.url" class="w-full h-full object-cover" :alt="preview.name">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-green-900/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-1">
                                                        <span class="text-white text-xs font-semibold">Baru</span>
                                                    </div>
                                                    <div class="absolute top-0 right-0 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-bl-lg font-semibold">
                                                        <i class="ri-add-line"></i>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <p x-show="getTotalImagesCount() === 0" class="text-sm text-gray-500 italic">Tidak ada gambar</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer with Navigation -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4">
                    <div class="flex justify-between items-center gap-4">
                        <button type="button" 
                                @click="prevProjectStep()" 
                                x-show="currentStep > 1"
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors flex items-center gap-2">
                            <i class="ri-arrow-left-line"></i>Kembali
                        </button>
                        
                        <button type="button" 
                                @click="showEditProjectModal = false; resetProjectModal()" 
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                            Batal
                        </button>
                        
                        <button type="button" 
                                @click="nextProjectStep()" 
                                x-show="currentStep < totalSteps"
                                :disabled="!canProceedToNextStep()"
                                :class="!canProceedToNextStep() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#8d0d11]'"
                                class="px-6 py-2.5 bg-[#b01116] text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                            Selanjutnya<i class="ri-arrow-right-line"></i>
                        </button>
                        
                        <button type="button" 
                                @click="updateProject()"
                                x-show="currentStep === totalSteps"
                                :disabled="!canCreateProject()"
                                :class="!canCreateProject() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#8d0d11]'"
                                class="px-6 py-2.5 bg-[#b01116] text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                            <i class="ri-save-line"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

    </div>
</div>

<script>
function mediaPreview(inputId) {
    return {
        previews: [],
        files: [],
        inputId: inputId,
        
        handleFiles(fileList) {
            this.files = Array.from(fileList);
            this.previews = [];
            
            this.files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previews.push({
                            url: e.target.result,
                            name: file.name,
                            type: file.type,
                            size: file.size
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });
        },
        
        removeFile(index) {
            this.previews.splice(index, 1);
            this.files.splice(index, 1);
            
            // Update the SPECIFIC file input using the inputId
            const fileInput = document.getElementById(this.inputId);
            if (fileInput) {
                const dt = new DataTransfer();
                this.files.forEach(file => {
                    dt.items.add(file);
                });
                fileInput.files = dt.files;
            }
        }
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('profileEditor', () => ({
        showEditModal: false,
        currentStep: 1,
        totalSteps: 3,
        
        // Form data
        fullName: '{{ old('full_name', auth()->user()->full_name) }}',
        phoneNumber: '{{ old('phone_number', auth()->user()->phone_number) }}',
        studentId: '{{ old('student_id', auth()->user()->student->student_id ?? '') }}',
        shortAbout: '{{ old('short_about', auth()->user()->short_about) }}',
        about: '{{ old('about', auth()->user()->about) }}',
        
        // Avatar preview
        avatarPreview: null,
        removeAvatar: false,
        
        // Expertises
        selectedExpertises: {!! json_encode(old('expertises', optional(auth()->user()->student)->expertises ? auth()->user()->student->expertises->pluck('id')->toArray() : [])) !!},
        searchExpertise: '',
        showAddExpertise: false,
        newExpertiseName: '',
        expertises: {!! json_encode($expertises ?? []) !!},
        
        // Education
        education: {!! json_encode(old('education', optional(auth()->user()->student)->educationInfo ? auth()->user()->student->educationInfo->map(function($edu) {
            return [
                'id' => $edu->id,
                'institution_name' => $edu->institution_name,
                'degree' => $edu->degree,
                'field_of_study' => $edu->field_of_study,
                'start_date' => $edu->start_date,
                'end_date' => $edu->end_date,
                'is_current' => $edu->is_current,
                'description' => $edu->description
            ];
        })->toArray() : [])) !!},
        showAddEducation: false,
        
        // Computed properties
        get filteredExpertises() {
            if (this.searchExpertise === '') {
                return this.expertises;
            }
            return this.expertises.filter(exp => 
                exp.name.toLowerCase().includes(this.searchExpertise.toLowerCase())
            );
        },
        
        // Validation
        canProceedToNext() {
            if (this.currentStep === 1) {
                return this.fullName.trim() !== '' && 
                       this.phoneNumber.trim() !== '' && 
                       this.studentId.trim() !== '';
            }
            return true;
        },
        
        nextStep() {
            if (this.canProceedToNext() && this.currentStep < this.totalSteps) {
                this.currentStep++;
            }
        },
        
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },
        
        resetModal() {
            this.currentStep = 1;
            this.fullName = '{{ old('full_name', auth()->user()->full_name) }}';
            this.phoneNumber = '{{ old('phone_number', auth()->user()->phone_number) }}';
            this.studentId = '{{ old('student_id', auth()->user()->student->student_id ?? '') }}';
            this.shortAbout = '{{ old('short_about', auth()->user()->short_about) }}';
            this.about = '{{ old('about', auth()->user()->about) }}';
            this.selectedExpertises = {!! json_encode(old('expertises', optional(auth()->user()->student)->expertises ? auth()->user()->student->expertises->pluck('id')->toArray() : [])) !!};
            this.education = {!! json_encode(old('education', optional(auth()->user()->student)->educationInfo ? auth()->user()->student->educationInfo->map(function($edu) {
                return [
                    'id' => $edu->id,
                    'institution_name' => $edu->institution_name,
                    'degree' => $edu->degree,
                    'field_of_study' => $edu->field_of_study,
                    'start_date' => $edu->start_date,
                    'end_date' => $edu->end_date,
                    'is_current' => $edu->is_current,
                    'description' => $edu->description
                ];
            })->toArray() : [])) !!};
            this.avatarPreview = null;
            this.removeAvatar = false;
            this.searchExpertise = '';
            this.showAddExpertise = false;
            this.showAddEducation = false;
        },
        
        // Expertise methods
        toggleExpertise(expertiseId) {
            const index = this.selectedExpertises.indexOf(expertiseId);
            if (index > -1) {
                this.selectedExpertises.splice(index, 1);
            } else {
                this.selectedExpertises.push(expertiseId);
            }
        },
        
        isExpertiseSelected(expertiseId) {
            return this.selectedExpertises.includes(expertiseId);
        },
        
        async addExpertise() {
            if (this.newExpertiseName.trim() === '') return;
            
            try {
                const response = await fetch('{{ route("student.expertises.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: this.newExpertiseName })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.expertises.push(data.expertise);
                    this.selectedExpertises.push(data.expertise.id);
                    this.newExpertiseName = '';
                    this.showAddExpertise = false;
                }
            } catch (error) {
                console.error('Error adding expertise:', error);
            }
        },
        
        // Education methods
        addEducation() {
            this.education.push({
                id: 'new_' + Date.now(),
                institution_name: '',
                degree: '',
                field_of_study: '',
                start_date: '',
                end_date: '',
                is_current: false,
                description: ''
            });
            this.showAddEducation = true;
        },
        
        removeEducation(index) {
            this.education.splice(index, 1);
        },
        
        // Avatar preview
        handleAvatarPreview(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.avatarPreview = e.target.result;
                    this.removeAvatar = false;
                };
                reader.readAsDataURL(file);
            }
        },
        
        handleRemoveAvatar() {
            this.removeAvatar = true;
            this.avatarPreview = null;
            const fileInput = document.getElementById('avatar');
            if (fileInput) {
                fileInput.value = '';
            }
        },
        
        // ==================== PROJECT CREATION MODALS ====================
        
        // Project Modal State
        showIndividualProjectModal: false,
        showTeamProjectModal: false,
        showEditProjectModal: false,
        projectType: 'individual',
        
        // Original data for change detection
        originalProjectData: null,
        originalTeamPositions: null,
        
        // Project Form Data
        projectData: {
            title: '',
            description: '',
            price: '',
            status: 'draft',
            categories: [],
            subjects: [],
            teachers: [],
            team_members: [],
            team_positions: {},
            existing_images: [],
            images_to_delete: []
        },
        
        // Project Modal Data
        categories: {!! json_encode($categories ?? []) !!},
        subjects: {!! json_encode($subjects ?? []) !!},
        teachers: {!! json_encode($teachers ?? []) !!},
        students: {!! json_encode($students ?? []) !!},
        
        // Search filters
        searchCategory: '',
        searchSubject: '',
        searchTeacher: '',
        searchStudent: '',
        
        // Add new item flags
        showAddCategory: false,
        showAddSubject: false,
        showAddTeacher: false,
        
        // New item forms - using object notation
        newCategory: { name: '' },
        newSubject: { name: '', code: '' },
        newTeacher: { name: '', nip: '', email: '', phone_number: '', institution: '' },
        
        // Media preview handling
        newMediaPreviews: [],
        newMediaFiles: [],
        
        // Computed properties for filtered lists
        get filteredCategories() {
            if (this.searchCategory === '') {
                return this.categories;
            }
            return this.categories.filter(cat => 
                cat.name.toLowerCase().includes(this.searchCategory.toLowerCase())
            );
        },
        
        get filteredSubjects() {
            if (this.searchSubject === '') {
                return this.subjects;
            }
            return this.subjects.filter(sub => 
                sub.name.toLowerCase().includes(this.searchSubject.toLowerCase()) ||
                (sub.code && sub.code.toLowerCase().includes(this.searchSubject.toLowerCase()))
            );
        },
        
        get filteredTeachers() {
            if (this.searchTeacher === '') {
                return this.teachers;
            }
            return this.teachers.filter(teacher => 
                teacher.name.toLowerCase().includes(this.searchTeacher.toLowerCase()) ||
                (teacher.nip && teacher.nip.includes(this.searchTeacher)) ||
                (teacher.institution && teacher.institution.toLowerCase().includes(this.searchTeacher.toLowerCase()))
            );
        },
        
        get filteredStudents() {
            if (this.searchStudent === '') {
                return this.students;
            }
            return this.students.filter(student => 
                (student.user.full_name && student.user.full_name.toLowerCase().includes(this.searchStudent.toLowerCase())) ||
                (student.user.username && student.user.username.toLowerCase().includes(this.searchStudent.toLowerCase())) ||
                (student.student_id && student.student_id.includes(this.searchStudent))
            );
        },
        
        // Project validation
        canProceedToNextStep() {
            if (this.currentStep === 1) {
                return this.projectData.title.trim() !== '';
            }
            if (this.currentStep === 2) {
                return this.projectData.categories.length > 0;
            }
            return true;
        },
        
        // Project navigation
        nextProjectStep() {
            if (this.canProceedToNextStep() && this.currentStep < this.totalSteps) {
                this.currentStep++;
            }
        },
        
        prevProjectStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },
        
        // Reset project modal
        resetProjectModal() {
            this.currentStep = 1;
            this.projectData = {
                title: '',
                description: '',
                price: '',
                status: 'draft',
                categories: [],
                subjects: [],
                teachers: [],
                team_members: [],
                team_positions: {},
                existing_images: [],
                images_to_delete: []
            };
            this.originalProjectData = null;
            this.originalTeamPositions = null;
            this.searchCategory = '';
            this.searchSubject = '';
            this.searchTeacher = '';
            this.searchStudent = '';
            this.showAddCategory = false;
            this.showAddSubject = false;
            this.showAddTeacher = false;
            this.newCategory = { name: '' };
            this.newSubject = { name: '', code: '' };
            this.newTeacher = { name: '', nip: '', email: '', phone_number: '', institution: '' };
            this.newMediaPreviews = [];
            this.newMediaFiles = [];
            
            // Clear file inputs
            const fileInput = document.getElementById('edit_media_detail');
            if (fileInput) {
                fileInput.value = '';
            }
        },
        
        // Toggle selections
        toggleCategory(categoryId) {
            const index = this.projectData.categories.indexOf(categoryId);
            if (index > -1) {
                this.projectData.categories.splice(index, 1);
            } else {
                this.projectData.categories.push(categoryId);
            }
        },
        
        isCategorySelected(categoryId) {
            return this.projectData.categories.includes(categoryId);
        },
        
        toggleSubject(subjectId) {
            const index = this.projectData.subjects.indexOf(subjectId);
            if (index > -1) {
                this.projectData.subjects.splice(index, 1);
            } else {
                this.projectData.subjects.push(subjectId);
            }
        },
        
        isSubjectSelected(subjectId) {
            return this.projectData.subjects.includes(subjectId);
        },
        
        toggleTeacher(teacherId) {
            const index = this.projectData.teachers.indexOf(teacherId);
            if (index > -1) {
                this.projectData.teachers.splice(index, 1);
            } else {
                this.projectData.teachers.push(teacherId);
            }
        },
        
        isTeacherSelected(teacherId) {
            return this.projectData.teachers.includes(teacherId);
        },
        
        toggleTeamMember(studentId) {
            const index = this.projectData.team_members.indexOf(studentId);
            if (index > -1) {
                this.projectData.team_members.splice(index, 1);
                delete this.projectData.team_positions[studentId];
            } else {
                this.projectData.team_members.push(studentId);
                this.projectData.team_positions[studentId] = 'Member';
            }
        },
        
        isTeamMemberSelected(studentId) {
            return this.projectData.team_members.includes(studentId);
        },
        
        // Add new items via AJAX
        async createCategory() {
            if (this.newCategory.name.trim() === '') return;
            
            try {
                const response = await fetch('{{ route("student.categories.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: this.newCategory.name })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.categories.push(data.category);
                    this.projectData.categories.push(data.category.id);
                    this.newCategory = { name: '' };
                    this.showAddCategory = false;
                }
            } catch (error) {
                console.error('Error adding category:', error);
            }
        },
        
        async createSubject() {
            if (this.newSubject.name.trim() === '') return;
            
            try {
                const response = await fetch('{{ route("student.subjects.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        name: this.newSubject.name,
                        code: this.newSubject.code
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.subjects.push(data.subject);
                    this.projectData.subjects.push(data.subject.id);
                    this.newSubject = { name: '', code: '' };
                    this.showAddSubject = false;
                }
            } catch (error) {
                console.error('Error adding subject:', error);
            }
        },
        
        async createTeacher() {
            if (this.newTeacher.name.trim() === '') return;
            
            try {
                const response = await fetch('{{ route("student.teachers.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        name: this.newTeacher.name,
                        nip: this.newTeacher.nip,
                        email: this.newTeacher.email,
                        phone_number: this.newTeacher.phone_number,
                        institution: this.newTeacher.institution
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.teachers.push(data.teacher);
                    this.projectData.teachers.push(data.teacher.id);
                    this.newTeacher = { name: '', nip: '', email: '', phone_number: '', institution: '' };
                    this.showAddTeacher = false;
                }
            } catch (error) {
                console.error('Error adding teacher:', error);
            }
        },
        
        // Edit project - Load data via AJAX
        async loadProjectForEdit(projectId) {
            try {
                // Show loading
                Swal.fire({
                    title: 'Memuat data proyek...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch(`/student/projects/${projectId}/edit-data`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Gagal memuat data proyek');
                }

                const project = data.project;
                
                // Set project type
                this.projectType = project.type;
                
                // Load project data
                this.projectData = {
                    id: project.id,
                    title: project.title,
                    description: project.description,
                    price: project.price,
                    status: project.status,
                    categories: project.categories ? project.categories.map(c => c.id) : [],
                    subjects: project.subjects ? project.subjects.map(s => s.id) : [],
                    teachers: project.teachers ? project.teachers.map(t => t.id) : [],
                    team_members: project.team_members ? project.team_members.map(m => m.student.id) : [],
                    team_positions: project.team_members ? project.team_members.reduce((acc, m) => {
                        acc[m.student.id] = m.position || 'Team Member';
                        return acc;
                    }, {}) : {},
                    existing_images: project.media ? project.media.map(m => ({
                        id: m.id,
                        path: m.file_path,
                        url: '/storage/' + m.file_path,
                        is_main: m.order === 0
                    })) : [],
                    images_to_delete: []
                };
                
                // Store original data for change detection
                this.originalProjectData = JSON.parse(JSON.stringify(this.projectData));
                this.originalTeamPositions = JSON.parse(JSON.stringify(this.projectData.team_positions));
                
                // Reset to step 1 and show modal
                this.currentStep = 1;
                this.showEditProjectModal = true;
                
                // Close loading
                Swal.close();
                
            } catch (error) {
                console.error('Error loading project:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Gagal memuat data proyek',
                    confirmButtonColor: '#b01116'
                });
            }
        },
        
        // Update project
        async updateProject() {
            if (!this.canCreateProject()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: this.getValidationMessage(),
                    confirmButtonColor: '#b01116'
                });
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('title', this.projectData.title);
                formData.append('description', this.projectData.description);
                formData.append('price', this.projectData.price || '');
                formData.append('status', this.projectData.status);
                
                // Categories (required)
                this.projectData.categories.forEach(catId => {
                    formData.append('categories[]', catId);
                });
                
                // Subjects (optional)
                this.projectData.subjects.forEach(subId => {
                    formData.append('subjects[]', subId);
                });
                
                // Teachers (optional)
                this.projectData.teachers.forEach(teachId => {
                    formData.append('teachers[]', teachId);
                });
                
                // Team members (for team projects)
                if (this.projectType === 'team') {
                    this.projectData.team_members.forEach((memberId, index) => {
                        formData.append('team_members[]', memberId);
                        formData.append('team_positions[]', this.projectData.team_positions[memberId] || 'Team Member');
                    });
                }
                
                // Images to delete
                if (this.projectData.images_to_delete && this.projectData.images_to_delete.length > 0) {
                    this.projectData.images_to_delete.forEach(imageId => {
                        formData.append('images_to_delete[]', imageId);
                    });
                }
                
                // New media files
                const fileInput = document.getElementById('edit_media_detail');
                if (fileInput && fileInput.files.length > 0) {
                    Array.from(fileInput.files).forEach((file, index) => {
                        formData.append('new_media[]', file);
                    });
                }
                
                // Show loading
                Swal.fire({
                    title: 'Menyimpan perubahan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                const response = await fetch(`/student/projects/${this.projectData.id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Proyek berhasil diperbarui',
                        confirmButtonColor: '#b01116',
                        timer: 2000
                    });
                    
                    // Reload page to show updated data
                    window.location.reload();
                } else {
                    throw new Error(result.message || 'Gagal memperbarui proyek');
                }
            } catch (error) {
                console.error('Error updating project:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Gagal memperbarui proyek',
                    confirmButtonColor: '#b01116'
                });
            }
        },
        
        markImageForDeletion(imageId) {
            if (!this.projectData.images_to_delete) {
                this.projectData.images_to_delete = [];
            }
            
            const imageData = this.projectData.existing_images.find(img => img.id === imageId);
            
            if (this.projectData.images_to_delete.includes(imageId)) {
                // Undo deletion
                const index = this.projectData.images_to_delete.indexOf(imageId);
                this.projectData.images_to_delete.splice(index, 1);
            } else {
                // Mark for deletion
                this.projectData.images_to_delete.push(imageId);
            }
        },
        
        isImageMarkedForDeletion(imageId) {
            return this.projectData.images_to_delete && this.projectData.images_to_delete.includes(imageId);
        },
        
        getTotalImageCount() {
            const existingCount = this.getExistingImagesCount();
            const newCount = document.getElementById('edit_media_detail')?.files?.length || 0;
            return existingCount + newCount;
        },
        
        getExistingImagesCount() {
            if (!this.projectData.existing_images) return 0;
            const deletedCount = this.projectData.images_to_delete ? this.projectData.images_to_delete.length : 0;
            return this.projectData.existing_images.length - deletedCount;
        },
        
        canCreateProject() {
            const hasTitle = this.projectData.title && this.projectData.title.trim() !== '';
            const hasCategories = this.projectData.categories && this.projectData.categories.length > 0;
            const hasMinImages = this.getTotalImageCount() >= 1;
            const hasTeamMembers = this.projectType === 'individual' || (this.projectData.team_members && this.projectData.team_members.length > 0);
            return hasTitle && hasCategories && hasMinImages && hasTeamMembers;
        },
        
        getValidationMessage() {
            if (this.currentStep === 1 && this.projectData.title.trim() === '') {
                return 'Judul proyek wajib diisi';
            }
            if (this.currentStep === 2 && this.projectData.categories.length === 0) {
                return 'Pilih minimal 1 kategori';
            }
            if (this.getTotalImageCount() < 1) {
                return 'Upload minimal 1 gambar';
            }
            return 'Lengkapi data';
        },
        
        // Handle new media files for edit modal
        handleNewMediaFiles(event) {
            const files = event.target.files;
            this.newMediaFiles = Array.from(files);
            this.newMediaPreviews = [];
            
            Array.from(files).forEach((file) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.newMediaPreviews.push({
                            url: e.target.result,
                            name: file.name,
                            type: 'image'
                        });
                    };
                    reader.readAsDataURL(file);
                } else if (file.type.startsWith('video/')) {
                    this.newMediaPreviews.push({
                        url: URL.createObjectURL(file),
                        name: file.name,
                        type: 'video'
                    });
                }
            });
        },
        
        // Remove new media file
        removeNewMediaFile(index) {
            this.newMediaPreviews.splice(index, 1);
            this.newMediaFiles.splice(index, 1);
            
            // Update the file input
            const fileInput = document.getElementById('edit_media_detail');
            if (fileInput) {
                const dt = new DataTransfer();
                this.newMediaFiles.forEach(file => {
                    dt.items.add(file);
                });
                fileInput.files = dt.files;
            }
        }
    }));
});
</script>
@endsection