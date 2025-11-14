<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield("title") &mdash; {{ config('app.name') }}</title>
    <link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css"
    rel="stylesheet"
    />
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite('resources/css/app.css')
</head>
<body>
    <div id="website-container" class="regular-layout">
        <x-navbar></x-navbar>
        <main>
            @yield('content')
        </main>
        <x-back-to-top></x-back-to-top>
        <x-footer></x-footer>
    </div>
    <script>
        const navbar = document.querySelector('header');
        const main = document.querySelector('main');
        const footer = document.querySelector('footer');

        function adjustMainPadding() {
            // Get the base navbar height (without expanded mobile menu)
            const navbarFirstChild = navbar.querySelector('nav > div:first-child');
            const navbarHeight = navbarFirstChild ? navbarFirstChild.offsetHeight : navbar.offsetHeight;
            const footerHeight = footer.offsetHeight;
            main.style.paddingTop = `${navbarHeight}px`;
            main.style.paddingBottom = `${footerHeight}px`;
        }
        
        // Initial adjustment
        window.addEventListener('load', adjustMainPadding);
        
        // Adjust on window resize
        window.addEventListener('resize', adjustMainPadding);
        
        // Observe navbar height changes (for mobile menu toggle) but don't adjust padding
        if (navbar) {
            const observer = new MutationObserver(function(mutations) {
                // Only adjust if screen is desktop size
                if (window.innerWidth >= 1024) {
                    adjustMainPadding();
                }
            });
            
            observer.observe(navbar, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class']
            });
        }
    </script>
</body>
</html>