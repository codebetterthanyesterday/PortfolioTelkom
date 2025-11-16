<div class="group bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
    <div class="relative h-32 bg-gradient-to-br from-[#b01116] to-[#8d0d11]">
        <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2">
            @if($student->user->avatar)
                <img src="{{ $student->user->avatar_url }}" 
                     alt="{{ $student->user->username }}" 
                     class="w-24 h-24 rounded-full border-4 border-white object-cover">
            @else
                <div class="w-24 h-24 rounded-full border-4 border-white bg-gray-200 flex items-center justify-center text-3xl font-bold text-gray-600">
                    {{ strtoupper(substr($student->user->username, 0, 1)) }}
                </div>
            @endif
        </div>
    </div>
    
    <div class="pt-14 pb-6 px-6 text-center">
        <h3 class="text-lg font-bold text-gray-900 mb-1">
            {{ "@" . $student->user->username }}
        </h3>
        <p class="text-sm text-gray-600 mb-2">
            @if($student->user->full_name)
                {{ Str::limit($student->user->full_name, 20) }}
            @else
                {{ "@" . $student->user->username }}
            @endif
        </p>
        
        @if($student->student_id)
        <p class="text-xs text-gray-500 mb-3">{{ $student->student_id }}</p>
        @endif
        
        <div class="flex items-center justify-center gap-2 mb-4">
            <div class="flex items-center gap-1 text-[#b01116]">
                <i class="ri-folder-line"></i>
                <span class="text-sm font-semibold">{{ $student->projects_count }}</span>
            </div>
            <span class="text-xs text-gray-500">Projects</span>
        </div>
        
        <a href="{{ route('detail.student', $student->user->username) }}" 
           class="inline-flex items-center gap-2 text-sm font-medium text-[#b01116] hover:text-[#8d0d11] transition-colors">
            View Profile
            <i class="ri-arrow-right-line"></i>
        </a>
    </div>
</div>

