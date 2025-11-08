<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
            const navbarHeight = navbar.offsetHeight;
            const footerHeight = footer.offsetHeight;
            main.style.paddingTop = `${navbarHeight}px`;
            main.style.paddingBottom = `${footerHeight}px`;
        }
        window.addEventListener('load', adjustMainPadding);
        window.addEventListener('resize', adjustMainPadding);
    </script>
</body>
</html>