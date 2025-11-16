@extends('layout.layout')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
        @if($notifications->where('read_at', null)->count() > 0)
        <form action="{{ route(auth()->user()->isStudent() ? 'student.notifications.readAll' : 'investor.notifications.readAll') }}" 
              method="POST">
            @csrf
            <button type="submit" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                Mark all as read
            </button>
        </form>
        @endif
    </div>
    {{-- fathinabrar905 --}}
    @forelse($notifications as $notification)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4 {{ $notification->isUnread() ? 'bg-blue-50 border-blue-200' : '' }}">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                @if($notification->type === 'team_mention')
                <div class="w-12 h-12 bg-gradient-to-br from-[#b01116] to-pink-600 rounded-full flex items-center justify-center">
                    <i class="ri-team-line text-white text-xl"></i>
                </div>
                @else
                <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center">
                    <i class="ri-notification-line text-white text-xl"></i>
                </div>
                @endif
            </div>
            
            <div class="flex-1">
                @php
                    $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                @endphp
                
                @if($notification->type === 'team_mention')
                <p class="font-semibold text-gray-800 mb-1">{{ $data['leader_name'] ?? 'Someone' }} added you to a project</p>
                <p class="text-gray-600 text-sm mb-2">{{ $data['message'] ?? 'You have been added as a team member' }}</p>
                <a href="{{ route('projects.show', $data['project_slug']) }}" 
                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-sm font-medium">
                    <i class="ri-external-link-line"></i>
                    View Project: {{ $data['project_title'] }}
                </a>
                @else
                <p class="font-semibold text-gray-800">Notification</p>
                <p class="text-gray-600 text-sm">{{ $data['message'] ?? 'You have a new notification' }}</p>
                @endif
                
                <p class="text-xs text-gray-500 mt-2">
                    <i class="ri-time-line"></i>
                    {{ $notification->created_at->diffForHumans() }}
                </p>
            </div>
            
            <div class="flex-shrink-0">
                @if($notification->isUnread())
                <form action="{{ route(auth()->user()->isStudent() ? 'student.notifications.read' : 'investor.notifications.read', $notification) }}" 
                      method="POST">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-medium px-3 py-1 rounded-full hover:bg-blue-100 transition-colors">
                        <i class="ri-check-line"></i>
                        Mark as read
                    </button>
                </form>
                @else
                <span class="inline-flex items-center gap-1 text-xs text-gray-500 px-3 py-1">
                    <i class="ri-check-double-line"></i>
                    Read
                </span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
            <i class="ri-notification-off-line text-4xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-800 mb-2">No notifications yet</h3>
        <p class="text-gray-600">When you receive notifications, they will appear here</p>
    </div>
    @endforelse

    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection

