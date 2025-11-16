<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
    <div class="relative">
        @if($project->media->first())
            <!-- Blur Backdrop -->
            <div class="absolute inset-0 -z-10 blur-2xl opacity-20">
                <img src="{{ $project->media->first()->url }}" alt="Backdrop" class="w-full h-full object-cover">
            </div>
            <!-- Main Image -->
            <img src="{{ $project->media->first()->url }}" alt="{{ $project->title }}" class="w-full h-48 object-cover">
        @else
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                <i class="ri-image-line text-4xl text-gray-400"></i>
            </div>
        @endif
        
        <!-- Wishlist Toggle (Investors Only) -->
        @auth
            @if(auth()->user()->isInvestor())
            <form action="{{ route('investor.wishlists.toggle', $project) }}" 
                  method="POST" 
                  class="wishlist-form absolute top-3 right-3"
                  data-project-id="{{ $project->id }}">
                @csrf
                <button type="submit" 
                        class="w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-md flex items-center justify-center transition-all">
                    <i class="{{ in_array($project->id, $wishlistedProjects ?? []) ? 'ri-heart-fill text-[#b01116]' : 'ri-heart-line text-gray-600' }} text-xl"></i>
                </button>
            </form>
            @endif
        @endauth
        
        <!-- Project Type Badge -->
        <span class="absolute top-3 left-3 bg-[#b01116] text-white text-xs font-semibold px-3 py-1 rounded-full">
            {{ strtoupper($project->type) }}
        </span>
    </div>
    
    <div class="p-5">
        <!-- Categories -->
        <div class="flex flex-wrap gap-2 mb-3">
            @foreach($project->categories->take(2) as $category)
            <span class="text-xs px-2 py-1 bg-red-50 text-red-600 rounded-md">{{ $category->name }}</span>
            @endforeach
        </div>
        
        <!-- Last Updated -->
        <p class="text-xs text-gray-500 mb-2">{{ $project->updated_at->format('d M Y') }}</p>
        
        <!-- Title -->
        <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">{{ $project->title }}</h3>
        
        <!-- Owner -->
        <p class="text-sm text-gray-600 mb-2">{{ $project->student->user->full_name ?? $project->student->user->username }}</p>
        
        <!-- View Count -->
        <p class="text-xs text-gray-400 mb-3 flex items-center gap-1">
            <i class="ri-eye-line"></i> {{ $project->view_count }} views
        </p>
        
        <!-- CTA Button -->
        <a href="{{ route('projects.show', $project->slug) }}" 
           class="inline-flex items-center text-sm font-medium text-[#b01116] hover:text-[#8d0d11] transition-colors">
            View Details
            <i class="ri-arrow-right-line ml-1"></i>
        </a>
    </div>
</div>

