<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">


        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        <script>
            if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
                document.querySelector('html').classList.remove('dark');
                document.querySelector('html').style.colorScheme = 'light';
            } else {
                document.querySelector('html').classList.add('dark');
                document.querySelector('html').style.colorScheme = 'dark';
            }
        </script>
    </head>
    <body  dir="{{ $localeDirs[App::getLocale()] }}"
        class="font-inter antialiased bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400"
        :class="{ 'sidebar-expanded': sidebarExpanded }"
        x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' ,darkMode: false }"
        x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value));
    if (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      localStorage.setItem('darkMode', JSON.stringify(true));
    }
    darkMode = JSON.parse(localStorage.getItem('darkMode'));
    $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" x-cloak>
        <script>
            if (localStorage.getItem('sidebar-expanded') == 'true') {
                document.querySelector('body').classList.add('sidebar-expanded');
            } else {
                document.querySelector('body').classList.remove('sidebar-expanded');
            }
        </script>

        <!-- Page wrapper -->
        <div class="flex h-screen overflow-hidden " id="app">

            <x-app.sidebar />

            <!-- Content area -->
            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden @if($attributes['background']){{ $attributes['background'] }}@endif" x-ref="contentarea"
                 style="{{ App::getLocale() === 'ar' ? 'padding-right: 240px;margin-left: 0px;' : 'padding-left: 240px;margin-right: 0px;' }} transition: all 0s ease 0s;" >

                <x-app.header />

                <main>
                    {{ $slot }}
                </main>

            </div>

        </div>

        @livewireScripts

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
