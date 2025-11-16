<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Badges -->
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach($project->categories as $category)
        <span class="px-3 py-1 flex items-center justify-center bg-[#b01116] hover:bg-[#8d0d11] text-white text-xs font-semibold rounded-full">{{ $category->name }}</span>
        @endforeach
        <span class="px-3 py-1 bg-pink-100 hover:bg-pink-100 flex items-center justify-center text-[#b01116] border border-pink-200 text-xs font-semibold rounded-full">
            {{ $project->type === 'team' ? 'Tim' : 'Individu' }}
        </span>
    </div>

    <!-- Project Title -->
    <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $project->title }}</h1>
    
    <!-- Project Owner -->
    <div class="flex items-center gap-2 mb-4">
        @if($project->student->user->avatar)
        <img src="{{ $project->student->user->avatar_url }}" alt="{{ $project->student->user->full_name }}" class="w-8 h-8 rounded-full object-cover">
        @else
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#b01116] to-pink-600 flex items-center justify-center text-white text-xs font-bold">
            {{ strtoupper(substr($project->student->user->username, 0, 1)) }}
        </div>
        @endif
        <div>
            <p class="text-sm font-medium text-gray-700">{{ $project->student->user->full_name ?? $project->student->user->username }}</p>
            @if($project->type === 'team')
            <p class="text-xs text-gray-500">Team Project Initiator</p>
            @else
            <p class="text-xs text-gray-500">Project Creator</p>
            @endif
        </div>
    </div>

    <!-- Price -->
    @if($project->price)
    <div class="mb-6">
        <p class="text-sm text-gray-500 mb-1">Estimasi Harga</p>
        <p class="text-3xl font-bold text-[#333]">{{ $project->formatted_price }}</p>
    </div>
    @else
    <div class="mb-6">
        <p class="text-sm text-gray-500 mb-1">Estimasi Harga</p>
        <p class="text-3xl font-bold text-[#333]">Tidak tersedia</p>
    </div>
    @endif

    <!-- CTA Buttons Based on Role -->
    <div class="space-y-3">
        @auth
            @if($isOwner)
                <!-- Student/Owner Actions -->
                <button @click="loadProjectForEdit({{ $project->id }})" 
                        class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                    <i class="ri-edit-line"></i>
                    Edit Proyek
                </button>
                <button @click="deleteProject({{ $project->id }})" 
                        class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2 border border-red-200">
                    <i class="ri-delete-bin-line"></i>
                    Hapus Proyek
                </button>
            @elseif(auth()->user()->isInvestor())
                <!-- Investor Actions -->
                <a href="{{ $project->student->getWhatsappLink() }}" 
                   target="_blank"
                   class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                    <i class="ri-message-3-line"></i>
                    Chat Dengan {{ $project->type === 'team' ? 'Team Leader' : 'Mahasiswa' }}
                </a>
                
                <form action="{{ route('investor.wishlists.toggle', $project) }}" method="POST" id="wishlistForm">
                    @csrf
                    <button type="submit" 
                            class="w-full {{ $isWishlisted ? 'bg-pink-100 border-pink-300' : 'bg-pink-50 border-pink-200' }} hover:bg-pink-100 text-[#b01116] font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2 border">
                        <i class="{{ $isWishlisted ? 'ri-bookmark-fill' : 'ri-bookmark-line' }}"></i>
                        {{ $isWishlisted ? 'Tersimpan' : 'Simpan Proyek' }}
                    </button>
                </form>
            @else
                <!-- Other Student (not owner) -->
                <a href="{{ $project->student->getWhatsappLink() }}" 
                   target="_blank"
                   class="w-full bg-[#b01116] hover:bg-[#8d0d11] text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
                    <i class="ri-message-3-line"></i>
                    Chat Dengan {{ $project->type === 'team' ? 'Team Leader' : 'Mahasiswa' }}
                </a>
            @endif
        @else
            <!-- Guest Actions (Disabled) -->
            <button disabled 
                    class="w-full bg-gray-100 text-gray-400 font-semibold py-3 px-4 rounded-lg cursor-not-allowed flex items-center justify-center gap-2 border border-gray-200"
                    title="Login untuk menghubungi">
                <i class="ri-message-3-line"></i>
                Login untuk Chat
            </button>
            <button disabled 
                    class="w-full bg-gray-50 text-gray-400 font-semibold py-3 px-4 rounded-lg cursor-not-allowed flex items-center justify-center gap-2 border border-gray-200"
                    title="Login untuk menyimpan">
                <i class="ri-bookmark-line"></i>
                Login untuk Simpan
            </button>
            
            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-sm text-[#b01116] hover:text-[#8d0d11] font-medium">
                    Sudah punya akun? Login sekarang
                </a>
            </div>
        @endauth
    </div>
</div>

