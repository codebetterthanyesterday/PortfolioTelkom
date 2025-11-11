<aside id="admin-sidebar" class="fixed top-0 left-0 bottom-0 w-64 bg-white border-r border-gray-300 transition-transform duration-300 ease-in-out z-40 lg:translate-x-0 -translate-x-full">
    <div class="h-full flex flex-col">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between px-6 border-b border-gray-300" style="height: var(--navbar-height, 60px);">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-[#b01116] rounded-lg flex items-center justify-center">
                    <i class="ri-admin-line text-white"></i>
                </div>
                <span class="font-bold text-gray-800">Admin Panel</span>
            </div>
            <!-- Close button (mobile only) -->
            <button @click="$dispatch('toggle-sidebar')" class="lg:hidden text-gray-600 hover:text-gray-800">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 overflow-y-auto py-6 px-4">
            <div class="space-y-1">
                <!-- Dashboard -->
                <x-admin.nav-link href="{{ route('admin.dashboard') }}" icon="ri-dashboard-line">
                    Dashboard
                </x-admin.nav-link>

                <!-- Projects Section -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase">Manajemen</p>
                </div>
                
                <x-admin.nav-link href="#" icon="ri-folder-line" badge="24">
                    Proyek
                </x-admin.nav-link>

                <x-admin.nav-link href="#" icon="ri-user-line" badge="156">
                    Mahasiswa
                </x-admin.nav-link>

                <x-admin.nav-link href="#" icon="ri-message-3-line" badge="8">
                    Komentar
                </x-admin.nav-link>

                <x-admin.nav-link href="#" icon="ri-bookmark-line">
                    Wishlist
                </x-admin.nav-link>

                <!-- Content Section -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase">Konten</p>
                </div>

                <x-admin.nav-link href="#" icon="ri-article-line">
                    Blog
                </x-admin.nav-link>

                <x-admin.nav-link href="#" icon="ri-question-answer-line">
                    Q&A
                </x-admin.nav-link>

                <!-- System Section -->
                {{-- <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase">Sistem</p>
                </div>

                <x-admin.nav-link href="#" icon="ri-settings-3-line">
                    Pengaturan
                </x-admin.nav-link>

                <x-admin.nav-link href="#" icon="ri-history-line">
                    Log Aktivitas
                </x-admin.nav-link> --}}
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="border-t border-gray-300 p-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="ri-external-link-line text-xl"></i>
                <span class="font-medium">Lihat Website</span>
            </a>
        </div>
    </div>
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div x-data="{ sidebarOpen: false }" 
     @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
     x-show="sidebarOpen"
     @click="sidebarOpen = false; $dispatch('toggle-sidebar')"
     x-transition.opacity
     class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

<script>
    // Toggle sidebar for mobile
    document.addEventListener('alpine:init', () => {
        Alpine.data('sidebar', () => ({
            init() {
                window.addEventListener('toggle-sidebar', () => {
                    const sidebar = document.getElementById('admin-sidebar');
                    sidebar.classList.toggle('-translate-x-full');
                });
            }
        }));
    });
    
    // Fallback if Alpine.js event doesn't work
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            const target = e.target.closest('[\\@click*="toggle-sidebar"]');
            if (target) {
                const sidebar = document.getElementById('admin-sidebar');
                sidebar.classList.toggle('-translate-x-full');
            }
        });
    });
</script>