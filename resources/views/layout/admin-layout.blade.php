<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') &mdash; {{ config('app.name') }}</title>
    <link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css"
    rel="stylesheet"
    />
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite('resources/css/app.css')
</head>
<body>
    <div id="website-container" class="admin-layout">
        <x-admin.navbar></x-admin.navbar>
        <x-admin.sidebar></x-admin.sidebar>
        <main>
            @yield('content')
        </main>
        <x-admin.footer></x-admin.footer>
    </div>
    <script>
        const webContainer = document.querySelector('#website-container.admin-layout');
        const navbar = document.querySelector('header');
        const sidebar = document.querySelector('aside');
        const main = document.querySelector('main');
        const footer = document.querySelector('footer');

        function adjustAdminLayout() {
            // Get the navbar height
            const navbarHeight = navbar.offsetHeight;
            
            // Set CSS variable for navbar height
            document.documentElement.style.setProperty('--navbar-height', `${navbarHeight}px`);
            
            // Get sidebar width (only on desktop)
            const sidebarWidth = window.innerWidth >= 1024 ? sidebar.offsetWidth : 0;
            const footerHeight = footer ? footer.offsetHeight : 0;
            
            // Apply padding to main content
            main.style.paddingTop = `${navbarHeight}px`;
            main.style.paddingLeft = `${sidebarWidth}px`;
            main.style.paddingBottom = `${footerHeight}px`;
            main.style.minHeight = '100vh';
        }
        
        // Initial adjustment
        window.addEventListener('load', adjustAdminLayout);
        
        // Adjust on window resize
        window.addEventListener('resize', adjustAdminLayout);
        
        // Run immediately
        adjustAdminLayout();
    </script>
</body>
</html>