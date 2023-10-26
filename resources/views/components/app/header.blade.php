<header class="sticky top-0 bg-gray-200 dark:bg-[#182235] border-b border-slate-200 dark:border-slate-700 z-30">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 -mb-px">

            <!-- Header: Left side -->
            <div class="flex">

                <!-- Hamburger button -->
                <!-- Toggler -->
                <button
                    class="mt-10 inline-block  px-6 py-2.5 text-xs font-medium uppercase leading-tight text-gray-700  transition duration-150 ease-in-out hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg lg:hidden"
                    data-te-sidenav-toggle-ref
                    data-te-target="#sidenav-1"
                    aria-controls="#sidenav-1"
                    aria-haspopup="true">
  <span class="block [&>svg]:h-5 [&>svg]:w-5 [&>svg]:text-white">
<i class='bx bx-menu bx-sm'></i>
  </span>
                </button>
                <!-- Toggler -->

                <x-breadcrumb/>

            </div>

            <!-- Header: Right side -->
            <div class="flex items-center space-x-3">
                <!-- Lang Switch -->
                <x-dropdown.lang-switch />
                <!-- Search Button with Modal -->
                <x-modal-search/>

                <!-- Notifications button -->
                <x-dropdown.notifications align="right"/>

                <!-- Info button -->
                <x-dropdown.help align="right"/>

                <!-- Dark mode toggle -->
                <x-theme-toggle/>

                <!-- Divider -->
                <hr class="w-px h-6 bg-slate-200 dark:bg-slate-700 border-none"/>

                <!-- User button -->
                <x-dropdown.profile align="right"/>

            </div>

        </div>
    </div>
</header>
