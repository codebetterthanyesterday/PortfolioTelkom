@extends('layout.layout')

@section('title', $project->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="projectDetail({{ $project->id }})">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Column (2 columns width) -->
        <div class="flex-1 lg:w-2/3">
            <!-- Product Preview with Carousel -->
            <div class="relative mb-8" x-data="{ 
            currentSlide: 0, 
            slides: {{ $project->media->count() > 0 ? $project->media->count() : 1 }},
            autoplayInterval: null,
            isPaused: false,
            startAutoplay() {
            if (this.slides > 1) {
            this.autoplayInterval = setInterval(() => {
            if (!this.isPaused) {
            this.currentSlide = this.currentSlide < this.slides - 1 ? this.currentSlide + 1 : 0;
            }
            }, 5000);
            }
            },
            stopAutoplay() {
            if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            }
            }
            }" 
            x-init="startAutoplay()" 
            @mouseenter="isPaused = true" 
            @mouseleave="isPaused = false"
            x-effect="if (currentSlide >= 0) $el.dispatchEvent(new CustomEvent('slide-changed'))">
            
            <!-- Main Carousel Container -->
            <div class="relative bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="relative h-[400px] sm:h-[500px] md:h-[600px]">
            @if($project->media->count() > 0)
            @foreach($project->media as $index => $media)
            <div x-show="currentSlide === {{ $index }}" x-transition class="absolute inset-0">
                <!-- Backdrop Blur Background -->
                <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 scale-110 blur-2xl opacity-50">
                    @if($media->isImage())
                    <img src="{{ $media->url }}" alt="Backdrop" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900"></div>
                    @endif
                </div>
                </div>
                
                <!-- Media Content (Original Size) -->
                <div class="relative h-full flex items-center justify-center p-4">
                @if($media->isImage())
                <img src="{{ $media->url }}" alt="{{ $project->title }}" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
                @else
                <video controls class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
                    <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                    Your browser does not support the video tag.
                </video>
                @endif
                </div>
            </div>
                @endforeach
            @else
                <!-- Placeholder if no media -->
                <div class="absolute inset-0 flex items-center justify-center bg-gray-100">
                <div class="text-center">
                <i class="ri-image-line text-6xl text-gray-400"></i>
                <p class="text-gray-500 mt-2">No media available</p>
            </div>
            </div>
            @endif

            @if($project->media->count() > 1)
            <!-- Navigation Arrows -->
            <button @click="currentSlide = currentSlide > 0 ? currentSlide - 1 : slides - 1" 
                class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 w-10 h-10 rounded-full shadow-lg flex items-center justify-center transition-all z-10">
                <i class="ri-arrow-left-s-line text-xl"></i>
            </button>
            <button @click="currentSlide = currentSlide < slides - 1 ? currentSlide + 1 : 0" 
                class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 w-10 h-10 rounded-full shadow-lg flex items-center justify-center transition-all z-10">
                <i class="ri-arrow-right-s-line text-xl"></i>
            </button>

            <!-- Dots Indicator -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
            <template x-for="i in slides" :key="i">
                <button @click="currentSlide = i - 1" 
                :class="currentSlide === i - 1 ? 'bg-[#b01116] w-8' : 'bg-white/70 w-2'"
                class="h-2 rounded-full transition-all duration-300 shadow-sm"></button>
            </template>
                </div>
            @endif
            </div>
            </div>
            </div>

            <!-- Right Column (Mobile Only - appears after product preview) -->
            <div class="lg:hidden mb-8">
            @include('pages.partials.project-sidebar')
            </div>

            <!-- Project Description -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Deskripsi Proyek</h2>
            <div class="prose prose-gray max-w-none">
            <p class="text-gray-600 leading-relaxed mb-4 whitespace-pre-line">{{ $project->description }}</p>
            </div>

            <!-- Course Info -->
            @if($project->subjects->count() > 0 || $project->teachers->count() > 0)
            <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if($project->subjects->count() > 0)
            <div>
                <p class="text-sm text-gray-500 mb-1">MATA KULIAH/PELAJARAN</p>
                @foreach($project->subjects as $subject)
                <p class="font-semibold text-gray-800">{{ $subject->name }} @if($subject->code)({{ $subject->code }})@endif</p>
                @endforeach
            </div>
            @endif
            @if($project->teachers->count() > 0)
            <div>
                <p class="text-sm text-gray-500 mb-1">DOSEN/GURU PEMBIMBING</p>
                @foreach($project->teachers as $teacher)
                <p class="font-semibold text-gray-800">{{ $teacher->name }} @if($teacher->nip)({{ $teacher->nip }})@endif</p>
                @endforeach
            </div>
            @endif
            </div>
            </div>
            @endif

            <!-- Project Stats -->
            <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center gap-6 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <i class="ri-eye-line text-gray-400"></i>
                <span>{{ $project->view_count }} views</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="ri-calendar-line text-gray-400"></i>
                <span>{{ $project->created_at->format('d M Y') }}</span>
            </div>
            @if($project->wishlists->count() > 0)
            <div class="flex items-center gap-2">
                <i class="ri-bookmark-line text-gray-400"></i>
                <span>{{ $project->wishlists->count() }} saves</span>
            </div>
            @endif
            </div>
            </div>
            </div>

            <!-- Team Members (Only for Team Projects) -->
            @if($project->type === 'team' && $project->members->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Anggota Tim</h2>
            <div class="space-y-4 sm:space-y-6">
            @php
            $leaders = $project->members->filter(fn($m) => $m->role === 'leader');
            $nonLeaders = $project->members->filter(fn($m) => $m->role !== 'leader');
            $sortedMembers = $leaders->concat($nonLeaders);
            @endphp
            @foreach($sortedMembers as $member)
            <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 pb-4 sm:pb-6 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
            @if($member->student->user->avatar)
            <img src="{{ $member->student->user->avatar_url }}" alt="{{ $member->student->user->full_name }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover shrink-0">
            @else
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-2xl font-bold shrink-0">
                {{ strtoupper(substr($member->student->user->username, 0, 1)) }}
            </div>
            @endif
            <div class="flex-1 w-full">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                <div class="flex-1">
                <div class="flex items-center gap-2">
                <h3 class="font-bold text-gray-800 text-base sm:text-lg">{{ $member->student->user->full_name ?? $member->student->user->username }}</h3>
                @if($member->role === 'leader')
                <span class="px-2 py-0.5 bg-[#b01116] text-white text-xs font-semibold rounded-full">Leader</span>
                @endif
                </div>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                @if($member->student->student_id)
                NIM: {{ $member->student->student_id }}
                @else
                {{ "@" . $member->student->user->username }}
                @endif
                </p>
                </div>
                <a href="{{ route('detail.student', $member->student->user->username) }}" class="text-[#b01116] hover:text-[#8d0d11] font-medium text-xs sm:text-sm flex items-center gap-1 whitespace-nowrap">
                Lihat Profil <i class="ri-arrow-right-line"></i>
                </a>
                </div>
                <p class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3"><strong>Posisi:</strong> {{ $member->position ?? 'Team Member' }}</p>
            </div>
            </div>
            @endforeach
            </div>
            </div>
            @endif

            <!-- Tabbed Section: Overview & Comments -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
            <button @click="activeTab = 'overview'" 
                :class="activeTab === 'overview' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-all">
                <i class="ri-information-line mr-2"></i>Overview
            </button>
            <button @click="activeTab = 'comments'" 
                :class="activeTab === 'comments' ? 'border-[#b01116] text-[#b01116]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-all">
                <i class="ri-chat-3-line mr-2"></i>Komentar ({{ $project->comments->count() }})
            </button>
            </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'" x-transition>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Proyek</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gray-500 rounded-full flex items-center justify-center">
                <i class="ri-eye-line text-white text-xl"></i>
                </div>
                <div>
                <p class="text-2xl font-bold text-gray-700">{{ $project->view_count }}</p>
                <p class="text-sm text-gray-600">Total Views</p>
                </div>
                </div>
                </div>
                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-[#b01116] rounded-full flex items-center justify-center">
                <i class="ri-bookmark-line text-white text-xl"></i>
                </div>
                <div>
                <p class="text-2xl font-bold text-[#b01116]">{{ $project->wishlists->count() }}</p>
                <p class="text-sm text-red-600">Wishlists</p>
                </div>
                </div>
                </div>
                <div class="bg-pink-50 rounded-lg p-4 border border-pink-200">
                <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center">
                <i class="ri-chat-3-line text-white text-xl"></i>
                </div>
                <div>
                <p class="text-2xl font-bold text-pink-700">{{ $project->comments->count() }}</p>
                <p class="text-sm text-pink-600">Comments</p>
                </div>
                </div>
                </div>
            </div>
            </div>

            <!-- Comments Tab -->
            <div x-show="activeTab === 'comments'" x-transition>
            @auth
                <!-- Comment Form -->
                <div class="mb-6">
                <form action="{{ route(auth()->user()->isStudent() ? 'student.comments.store' : 'investor.comments.store', $project) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tulis Komentar</label>
                <textarea name="content" rows="4" required
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent resize-none"
                      placeholder="Bagikan pemikiran Anda tentang proyek ini..."></textarea>
                </div>
                <div class="flex justify-end">
                <button type="submit" class="bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-2 px-6 rounded-lg transition-colors flex items-center gap-2">
                    <i class="ri-send-plane-fill"></i>
                    Kirim Komentar
                </button>
                </div>
                </form>
                </div>
            @else
                <!-- Guest Message -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6 text-center">
                <i class="ri-lock-line text-4xl text-blue-500 mb-2"></i>
                <p class="text-gray-700 font-medium mb-3">Anda harus login untuk memberikan komentar</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                <i class="ri-login-box-line"></i>
                Login Sekarang
                </a>
                </div>
            @endauth

            <!-- Comments List -->
            <div class="space-y-6">
                @forelse($project->comments as $comment)
                <div class="border-b border-gray-200 pb-6 last:border-0">
                <div class="flex items-start gap-3">
                @if($comment->user->avatar)
                <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->username }}" class="w-10 h-10 rounded-full object-cover">
                @else
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-sm font-bold">
                {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                </div>
                @endif
                <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-gray-800">{{ $comment->user->full_name ?? $comment->user->username }}</span>
                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
                <p class="text-gray-600 mb-3">{{ $comment->content }}</p>
                
                <div class="flex items-center gap-4">
                    @auth
                    <button @click="replyTo = replyTo === {{ $comment->id }} ? null : {{ $comment->id }}" 
                    class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium flex items-center gap-1">
                    <i class="ri-reply-line"></i>
                    Balas
                    </button>
                    @endauth
                    
                    @auth
                    @if(auth()->id() === $comment->user_id || $isOwner)
                    <form action="{{ route(auth()->user()->isStudent() ? 'student.comments.destroy' : 'investor.comments.destroy', $comment) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                    <i class="ri-delete-bin-line"></i>
                    Hapus
                    </button>
                    </form>
                    @endif
                    @endauth
            </div>

                <!-- Reply Form -->
                @auth
                <div x-show="replyTo === {{ $comment->id }}" x-transition class="mt-4">
                    <form action="{{ route(auth()->user()->isStudent() ? 'student.comments.store' : 'investor.comments.store', $project) }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <textarea name="content" rows="3" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#b01116] focus:border-transparent resize-none text-sm"
                      placeholder="Tulis balasan Anda..."></textarea>
                    <div class="flex justify-end gap-2">
                    <button type="button" @click="replyTo = null" class="text-sm text-gray-600 hover:text-gray-800 font-medium py-1 px-3">
                    Batal
                    </button>
                    <button type="submit" class="bg-[#b01116] hover:bg-[#8d0d11] text-white text-sm font-semibold py-1 px-4 rounded-lg transition-colors">
                    Kirim
                    </button>
                    </div>
                    </form>
                </div>
                @endauth

                <!-- Replies -->
                @if($comment->allReplies->count() > 0)
                <div class="mt-4 space-y-4 pl-4 border-l-2 border-gray-200">
                    @foreach($comment->allReplies as $reply)
                    <div class="flex items-start gap-3">
                    @if($reply->user->avatar)
                    <img src="{{ $reply->user->avatar_url }}" alt="{{ $reply->user->username }}" class="w-8 h-8 rounded-full object-cover">
                    @else
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($reply->user->username, 0, 1)) }}
                    </div>
                    @endif
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-gray-800 text-sm">{{ $reply->user->full_name ?? $reply->user->username }}</span>
                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-600 text-sm">{{ $reply->content }}</p>
                    
                    @auth
                    @if(auth()->id() === $reply->user_id || $isOwner)
                    <form action="{{ route(auth()->user()->isStudent() ? 'student.comments.destroy' : 'investor.comments.destroy', $reply) }}" 
                          method="POST" 
                          class="inline mt-2"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus balasan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                        <i class="ri-delete-bin-line"></i>
                        Hapus
                        </button>
                    </form>
                    @endif
                    @endauth
                    </div>
                    </div>
                    @endforeach
                </div>
                @endif
                </div>
                </div>
                </div>
                @empty
                <div class="text-center py-12">
                <i class="ri-chat-off-line text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Belum ada komentar. Jadilah yang pertama!</p>
                </div>
                @endforelse
            </div>
            </div>
            </div>
            </div>
        </div>

        <!-- Right Column (Desktop Only - Fixed Sidebar) -->
        <div class="hidden lg:block lg:w-1/3">
            <div class="lg:sticky lg:top-24">
                @include('pages.partials.project-sidebar')
            </div>
        </div>
    </div>

    @if($isOwner)
    <!-- Edit Project Modal -->
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
                                <input type="text" x-model="searchStudent" placeholder="Cari mahasiswa..."
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
                                            <div class="w-5 h-5 rounded border-2 shrink-0 mt-1 flex items-center justify-center"
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

                        <!-- Media Upload -->
                        <div x-data="mediaPreview('edit_media_detail')">
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
                                       id="edit_media_detail"
                                       @change="handleFiles($event.target.files)">
                                <label for="edit_media_detail" class="inline-flex items-center gap-2 cursor-pointer bg-[#b01116] text-white px-6 py-3 rounded-lg hover:bg-[#8d0d11] transition-colors font-medium shadow-md hover:shadow-lg">
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

                        <!-- Review Section -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2 text-lg">
                                <i class="ri-file-list-3-line text-[#b01116]"></i>
                                Review Perubahan
                            </h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-start gap-3 p-3 rounded-lg transition-all"
                                     :class="hasChanged('title') ? 'bg-yellow-50 border-2 border-yellow-300' : 'bg-white'">
                                    <i class="ri-file-text-line mt-0.5"
                                       :class="hasChanged('title') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-700">Judul:</span>
                                        <div class="mt-1">
                                            <p class="text-gray-600" x-text="projectData.title || 'Belum diisi'"></p>
                                            <p x-show="hasChanged('title')" class="text-xs text-yellow-700 mt-1 flex items-center gap-1">
                                                <i class="ri-arrow-right-line"></i>
                                                <span>Berubah dari: "<span x-text="getOriginalValue('title') || 'Belum diisi'"></span>"</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg transition-all"
                                     :class="hasChanged('description') ? 'bg-yellow-50 border-2 border-yellow-300' : 'bg-white'">
                                    <i class="ri-file-text-line mt-0.5"
                                       :class="hasChanged('description') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-700">Deskripsi:</span>
                                        <div class="mt-1">
                                            <p class="text-gray-600" x-text="projectData.description ? (projectData.description.substring(0, 100) + (projectData.description.length > 100 ? '...' : '')) : 'Belum diisi'"></p>
                                            <p x-show="hasChanged('description')" class="text-xs text-yellow-700 mt-1 flex items-center gap-1">
                                                <i class="ri-edit-line"></i>
                                                <span>Deskripsi telah diubah</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg transition-all"
                                     :class="hasChanged('price') ? 'bg-yellow-50 border-2 border-yellow-300' : 'bg-white'">
                                    <i class="ri-money-dollar-circle-line mt-0.5"
                                       :class="hasChanged('price') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-700">Estimasi Harga:</span>
                                        <div class="mt-1">
                                            <p class="text-gray-600" x-text="projectData.price ? 'Rp ' + new Intl.NumberFormat('id-ID').format(projectData.price) : 'Tidak diisi'"></p>
                                            <p x-show="hasChanged('price')" class="text-xs text-yellow-700 mt-1 flex items-center gap-1">
                                                <i class="ri-arrow-right-line"></i>
                                                <span>Berubah dari: <span x-text="getOriginalValue('price') ? 'Rp ' + new Intl.NumberFormat('id-ID').format(getOriginalValue('price')) : 'Tidak diisi'"></span></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg transition-all"
                                     :class="hasChanged('status') ? 'bg-yellow-50 border-2 border-yellow-300' : 'bg-white'">
                                    <i class="ri-eye-line mt-0.5"
                                       :class="hasChanged('status') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-700">Status:</span>
                                        <div class="mt-1">
                                            <p class="text-gray-600">
                                                <span x-show="projectData.status === 'draft'" class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs font-medium">Draft</span>
                                                <span x-show="projectData.status === 'published'" class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">Published</span>
                                                <span x-show="projectData.status === 'archived'" class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs font-medium">Archived</span>
                                            </p>
                                            <p x-show="hasChanged('status')" class="text-xs text-yellow-700 mt-1 flex items-center gap-1">
                                                <i class="ri-arrow-right-line"></i>
                                                <span>Berubah dari: <span x-text="getOriginalValue('status') === 'draft' ? 'Draft' : getOriginalValue('status') === 'published' ? 'Published' : 'Archived'"></span></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg transition-all"
                                     :class="hasChanged('categories') ? 'bg-yellow-50 border-2 border-yellow-300' : 'bg-white'">
                                    <i class="ri-price-tag-3-line mt-0.5"
                                       :class="hasChanged('categories') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-700">Kategori:</span>
                                        <div class="mt-1">
                                            <p class="text-gray-600" x-text="projectData.categories.length + ' dipilih'"></p>
                                            <p x-show="hasChanged('categories')" class="text-xs text-yellow-700 mt-1 flex items-center gap-1">
                                                <i class="ri-arrow-right-line"></i>
                                                <span>Berubah dari: <span x-text="getOriginalValue('categories') + ' dipilih'"></span></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg transition-all"
                                     :class="hasChanged('subjects') ? 'bg-yellow-50 border-2 border-yellow-300' : 'bg-white'">
                                    <i class="ri-book-open-line mt-0.5"
                                       :class="hasChanged('subjects') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-700">Mata Kuliah:</span>
                                        <div class="mt-1">
                                            <p class="text-gray-600" x-text="projectData.subjects.length + ' terpilih'"></p>
                                            <p x-show="hasChanged('subjects')" class="text-xs text-yellow-700 mt-1 flex items-center gap-1">
                                                <i class="ri-arrow-right-line"></i>
                                                <span>Berubah dari: <span x-text="getOriginalValue('subjects') + ' terpilih'"></span></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 p-3 rounded-lg transition-all"
                                     :class="hasChanged('teachers') ? 'bg-yellow-50 border-2 border-yellow-300' : 'bg-white'">
                                    <i class="ri-user-star-line mt-0.5"
                                       :class="hasChanged('teachers') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-700">Pembimbing:</span>
                                        <div class="mt-1">
                                            <p class="text-gray-600" x-text="projectData.teachers.length + ' terpilih'"></p>
                                            <p x-show="hasChanged('teachers')" class="text-xs text-yellow-700 mt-1 flex items-center gap-1">
                                                <i class="ri-arrow-right-line"></i>
                                                <span>Berubah dari: <span x-text="getOriginalValue('teachers') + ' terpilih'"></span></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="projectType === 'team'" 
                                     class="flex items-start gap-3 p-3 rounded-lg transition-all"
                                     :class="hasChanged('team_members') ? 'bg-yellow-50 border-2 border-yellow-300' : 'bg-white'">
                                    <i class="ri-team-line mt-0.5"
                                       :class="hasChanged('team_members') ? 'text-yellow-600' : 'text-gray-400'"></i>
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-700">Anggota Tim:</span>
                                        <div class="mt-1">
                                            <p class="text-gray-600" x-text="projectData.team_members.length + ' anggota (+ 1 leader)'"></p>
                                            <p x-show="hasChanged('team_members')" class="text-xs text-yellow-700 mt-1 flex items-center gap-1">
                                                <i class="ri-arrow-right-line"></i>
                                                <span>Berubah dari: <span x-text="getOriginalValue('team_members') + ' anggota (+ 1 leader)'"></span></span>
                                            </p>
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
                                    @click="prevStep()" 
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
                                    @click="nextStep()" 
                                    x-show="currentStep < totalSteps"
                                    :disabled="!canProceedToNext()"
                                    :class="!canProceedToNext() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#8d0d11]'"
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
    @endif
</div>

<script>
function projectDetail(projectId) {
    return {
        activeTab: 'overview',
        replyTo: null,
        showEditProjectModal: false,
        editingProject: null,
        currentStep: 1,
        totalSteps: 3,
        
        // Project data
        projectType: '{{ $project->type }}',
        projectData: {
            title: '',
            description: '',
            price: '',
            status: 'draft',
            categories: [],
            subjects: [],
            teachers: [],
            team_members: [],
            team_positions: {}
        },
        originalProjectData: null,
        originalTeamPositions: null,
        
        // Search filters
        searchCategory: '',
        searchSubject: '',
        searchTeacher: '',
        searchStudent: '',
        
        // Available data
        @if($isOwner)
        availableCategories: @json($categories),
        availableSubjects: @json($subjects),
        availableTeachers: @json($teachers),
        availableStudents: @json($students),
        @endif
        
        selectedFiles: [],
        
        // Computed filters
        get filteredCategories() {
            @if($isOwner)
            return !this.searchCategory
                ? this.availableCategories
                : this.availableCategories.filter(c => c.name.toLowerCase().includes(this.searchCategory.toLowerCase()));
            @else
            return [];
            @endif
        },
        get filteredSubjects() {
            @if($isOwner)
            return !this.searchSubject
                ? this.availableSubjects
                : this.availableSubjects.filter(s =>
                    s.name.toLowerCase().includes(this.searchSubject.toLowerCase()) ||
                    (s.code && s.code.toLowerCase().includes(this.searchSubject.toLowerCase()))
                );
            @else
            return [];
            @endif
        },
        get filteredTeachers() {
            @if($isOwner)
            return !this.searchTeacher
                ? this.availableTeachers
                : this.availableTeachers.filter(t =>
                    t.name.toLowerCase().includes(this.searchTeacher.toLowerCase()) ||
                    (t.nip && t.nip.toLowerCase().includes(this.searchTeacher.toLowerCase()))
                );
            @else
            return [];
            @endif
        },
        get filteredStudents() {
            @if($isOwner)
            return !this.searchStudent
                ? this.availableStudents
                : this.availableStudents.filter(st =>
                    (st.user.full_name && st.user.full_name.toLowerCase().includes(this.searchStudent.toLowerCase())) ||
                    st.user.username.toLowerCase().includes(this.searchStudent.toLowerCase()) ||
                    (st.student_id && st.student_id.toLowerCase().includes(this.searchStudent.toLowerCase()))
                );
            @else
            return [];
            @endif
        },
        
        init() {
            // Check if there's a hash in URL to show comments
            if (window.location.hash === '#comments') {
                this.activeTab = 'comments';
            }
        },
        
        // Navigation
        nextStep() {
            if (this.currentStep < this.totalSteps) this.currentStep++;
        },
        prevStep() {
            if (this.currentStep > 1) this.currentStep--;
        },
        
        // Validation
        canProceedToNext() {
            if (this.currentStep === 1) {
                return this.projectData.title.trim() !== '' && this.projectData.status !== '';
            }
            if (this.currentStep === 2) {
                return this.projectData.categories.length > 0;
            }
            return true;
        },
        canCreateProject() {
            const hasTitle = this.projectData.title.trim() !== '';
            const hasCategories = this.projectData.categories.length > 0;
            const hasTeamMembers = this.projectType === 'individual' || this.projectData.team_members.length > 0;
            return hasTitle && hasCategories && hasTeamMembers;
        },
        
        // Reset modal
        resetProjectModal() {
            this.projectData = {
                title: '',
                description: '',
                price: '',
                status: 'draft',
                categories: [],
                subjects: [],
                teachers: [],
                team_members: [],
                team_positions: {}
            };
            this.currentStep = 1;
            this.editingProject = null;
            this.originalProjectData = null;
            this.originalTeamPositions = null;
            this.searchCategory = '';
            this.searchSubject = '';
            this.searchTeacher = '';
            this.searchStudent = '';
        },
        
        // Change detection helpers
        hasChanged(field) {
            if (!this.originalProjectData) return false;
            if (Array.isArray(this.projectData[field])) {
                const original = (this.originalProjectData[field] || []).sort().join(',');
                const current = (this.projectData[field] || []).sort().join(',');
                return original !== current;
            }
            return this.projectData[field] !== this.originalProjectData[field];
        },
        getChangedValue(field) {
            if (!this.originalProjectData) return this.projectData[field];
            if (Array.isArray(this.projectData[field])) {
                return this.projectData[field].length;
            }
            return this.projectData[field];
        },
        getOriginalValue(field) {
            if (!this.originalProjectData) return null;
            if (Array.isArray(this.originalProjectData[field])) {
                return this.originalProjectData[field].length;
            }
            return this.originalProjectData[field];
        },
        
        // Load project data for editing
        async loadProjectForEdit(projectId) {
            try {
                const res = await fetch(`/student/projects/${projectId}/edit-data`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });
                
                const data = await res.json();
                
                if (!res.ok) {
                    alert('Gagal memuat data proyek: ' + (data.message || 'Error'));
                    return;
                }
                
                if (!data.success || !data.project) {
                    alert('Format response tidak valid');
                    return;
                }
                
                this.editingProject = data.project;
                this.projectType = data.project.type;
                this.projectData = {
                    title: data.project.title || '',
                    description: data.project.description || '',
                    price: data.project.price || '',
                    status: data.project.status || 'draft',
                    categories: data.project.categories?.map(c => c.id) || [],
                    subjects: data.project.subjects?.map(s => s.id) || [],
                    teachers: data.project.teachers?.map(t => t.id) || [],
                    team_members: data.project.team_members?.filter(m => m.role !== 'leader').map(m => m.student_id) || [],
                    team_positions: {}
                };
                
                // Populate team positions
                if (data.project.team_members) {
                    data.project.team_members.forEach(member => {
                        if (member.role !== 'leader') {
                            this.projectData.team_positions[member.student_id] = member.position || '';
                        }
                    });
                }
                
                // Store original data for change detection
                this.originalProjectData = JSON.parse(JSON.stringify(this.projectData));
                this.originalTeamPositions = JSON.parse(JSON.stringify(this.projectData.team_positions));
                
                this.currentStep = 1;
                this.showEditProjectModal = true;
            } catch (e) {
                console.error('Error loading project:', e);
                alert('Gagal memuat data proyek: ' + e.message);
            }
        },
        
        // Update project
        async updateProject() {
            if (!this.canCreateProject()) {
                alert('Mohon lengkapi semua data yang diperlukan');
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('title', this.projectData.title);
                formData.append('description', this.projectData.description);
                formData.append('price', this.projectData.price || '');
                formData.append('status', this.projectData.status);
                
                this.projectData.categories.forEach(id => formData.append('categories[]', id));
                this.projectData.subjects.forEach(id => formData.append('subjects[]', id));
                this.projectData.teachers.forEach(id => formData.append('teachers[]', id));
                
                if (this.projectType === 'team') {
                    this.projectData.team_members.forEach(id => {
                        formData.append('team_members[]', id);
                        formData.append('team_positions[]', this.projectData.team_positions[id] || '');
                    });
                }
                
                if (this.selectedFiles.length > 0) {
                    this.selectedFiles.forEach(file => formData.append('media[]', file));
                }
                
                const res = await fetch(`/student/projects/${this.editingProject.id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await res.json();
                
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Gagal memperbarui proyek');
                }
                
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Proyek berhasil diperbarui',
                    icon: 'success',
                    confirmButtonColor: '#b01116'
                }).then(() => {
                    window.location.reload();
                });
            } catch (e) {
                console.error(e);
                Swal.fire({
                    title: 'Gagal!',
                    text: e.message || 'Gagal memperbarui proyek',
                    icon: 'error',
                    confirmButtonColor: '#b01116'
                });
            }
        },
        
        // Toggles
        toggleCategory(id) {
            const i = this.projectData.categories.indexOf(id);
            i > -1 ? this.projectData.categories.splice(i, 1) : this.projectData.categories.push(id);
        },
        toggleSubject(id) {
            const i = this.projectData.subjects.indexOf(id);
            i > -1 ? this.projectData.subjects.splice(i, 1) : this.projectData.subjects.push(id);
        },
        toggleTeacher(id) {
            const i = this.projectData.teachers.indexOf(id);
            i > -1 ? this.projectData.teachers.splice(i, 1) : this.projectData.teachers.push(id);
        },
        toggleTeamMember(id) {
            const i = this.projectData.team_members.indexOf(id);
            if (i > -1) {
                this.projectData.team_members.splice(i, 1);
                delete this.projectData.team_positions[id];
            } else {
                this.projectData.team_members.push(id);
                this.projectData.team_positions[id] = '';
            }
        }
    }
}

// Delete Project Function
function deleteProject(projectId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Proyek yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#b01116',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send delete request
            fetch(`/student/projects/${projectId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Proyek berhasil dihapus',
                        icon: 'success',
                        confirmButtonColor: '#b01116'
                    }).then(() => {
                        // Redirect to profile page
                        window.location.href = '{{ route("student.profile") }}';
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Gagal menghapus proyek',
                        icon: 'error',
                        confirmButtonColor: '#b01116'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus proyek',
                    icon: 'error',
                    confirmButtonColor: '#b01116'
                });
            });
        }
    });
}

// Wishlist Form Handler
@auth
    @if(auth()->user()->isInvestor())
    document.addEventListener('DOMContentLoaded', function() {
        const wishlistForm = document.getElementById('wishlistForm');
        if (wishlistForm) {
            wishlistForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const button = this.querySelector('button[type="submit"]');
                const icon = button.querySelector('i');
                const originalText = button.innerHTML;
                
                // Disable button and show loading
                button.disabled = true;
                button.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Memproses...';
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update button state
                    const isWishlisted = button.querySelector('i').classList.contains('ri-bookmark-line');
                    
                    if (isWishlisted) {
                        icon.classList.remove('ri-bookmark-line');
                        icon.classList.add('ri-bookmark-fill');
                        button.classList.add('bg-pink-100', 'border-pink-300');
                        button.classList.remove('bg-pink-50', 'border-pink-200');
                        button.innerHTML = '<i class="ri-bookmark-fill"></i> Tersimpan';
                    } else {
                        icon.classList.remove('ri-bookmark-fill');
                        icon.classList.add('ri-bookmark-line');
                        button.classList.remove('bg-pink-100', 'border-pink-300');
                        button.classList.add('bg-pink-50', 'border-pink-200');
                        button.innerHTML = '<i class="ri-bookmark-line"></i> Simpan Proyek';
                    }
                    
                    button.disabled = false;
                    
                    // Show toast notification
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: isWishlisted ? 'Proyek disimpan!' : 'Proyek dihapus dari wishlist'
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    button.disabled = false;
                    button.innerHTML = originalText;
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan. Silakan coba lagi.',
                        confirmButtonColor: '#b01116'
                    });
                });
            });
        }
    });
    @endif
@endauth

// Show success/error messages from session
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        confirmButtonColor: '#b01116',
        timer: 3000
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session("error") }}',
        confirmButtonColor: '#b01116'
    });
@endif

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
</script>

@endsection
