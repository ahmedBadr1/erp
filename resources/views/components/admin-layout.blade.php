<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head >
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body
    class="font-inter antialiased bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400"
    :class="{ 'sidebar-expanded': sidebarExpanded }"
    x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }"
    x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))"
    dir="{{ $dir }}"
>

<script>
    if (localStorage.getItem('sidebar-expanded') == 'true') {
        document.querySelector('body').classList.add('sidebar-expanded');
    } else {
        document.querySelector('body').classList.remove('sidebar-expanded');
    }
</script>

<!-- Page wrapper -->
<div class="flex h-screen overflow-hidden">

    <x-app.sidebar />

    <!-- Content area -->
    <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden @if($attributes['background']){{ $attributes['background'] }}@endif" x-ref="contentarea"
         style="padding-left: 240px; margin-right: 0px; transition: all 0s ease 0s;"
    >

        <x-app.header />

        <main>
            {{ $slot }}
        </main>

    </div>

</div>
<script type="module">
    // On page load or when changing themes, best to add inline in `head` to avoid FOUC
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    };

    function setDarkTheme() {
        document.documentElement.classList.add("dark");
        localStorage.theme = "dark";
    };

    function setLightTheme() {
        document.documentElement.classList.remove("dark");
        localStorage.theme = "light";
    };

    function onThemeSwitcherItemClick(event) {
        const theme = event.target.dataset.theme;

        if (theme === "system") {
            localStorage.removeItem("theme");
            setSystemTheme();
        } else if (theme === "dark") {
            setDarkTheme();
        } else {
            setLightTheme();
        }
    };

    const themeSwitcherItems = document.querySelectorAll("#theme-switcher");
    themeSwitcherItems.forEach((item) => {
        item.addEventListener("click", onThemeSwitcherItemClick);
    });
</script>
@yield('scripts')
</body>
</html>
