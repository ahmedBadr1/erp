<nav id="full-screen-example" dir="{{ $localeDirs[App::getLocale()] }}"
     class="fixed left-0 top-0 z-[1035] h-screen w-60 -translate-x-full overflow-hidden bg-slate-200 shadow-[0_4px_12px_0_rgba(0,0,0,0.07),_0_2px_4px_rgba(0,0,0,0.05)] data-[te-sidenav-hidden='false']:translate-x-0 dark:bg-slate-800"
     style="{{ $localeDirs[App::getLocale()] === 'rtl' ? 'right:0;' : '' }} width: 240px; height: 100vh; position: fixed;  transition: all 0.3s linear 0s; transform: translateX(0%);">
    <div class="pt-6 text-center">
        <x-app.logo></x-app.logo>
        {{ config('app.name') }}
    </div>
    <div id="scrollContainer" style="max-height: calc(100% - 265px); position: relative;"
         class="perfect-scrollbar ps--active-y group/ps overflow-hidden [overflow-anchor:none] touch-none">
        <ul class="relative m-0 list-none px-[0.2rem]">
            <li class="relative">
                <a class="group flex h-12 cursor-pointer items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-700 outline-none transition duration-300 ease-linear hover:bg-gray-300/30 hover:text-inherit hover:outline-none focus:bg-gray-300/30 focus:text-inherit focus:outline-none active:bg-gray-300/30 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                   href="{{ route('admin.dashboard') }}" tabindex="0">
                    <i class="bx bxs-dashboard bx-sm"></i>
                    <span>{{ __('Dashbaord') }}</span>
                </a>
            </li>
            <li class="relative">
                <a class="group flex h-12 cursor-pointer items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-700 outline-none transition duration-300 ease-linear hover:bg-gray-300/30 hover:text-inherit hover:outline-none focus:bg-gray-300/30 focus:text-inherit focus:outline-none active:bg-gray-300/30 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                   href="{{ route('admin.reports') }}" data-te-sidenav-link-ref="" tabindex="0">
                    <i class="bx bxs-report bx-sm"></i>
                    <span>{{ __('Reports') }}</span>
                </a>
            </li>
        </ul>
        <hr class="border-gray-300">
        <ul class="relative m-0 list-none px-[0.2rem] ">
            <li class="relative" x-data="dropdown" @click="toggle()">
                <a class="flex h-12 cursor-pointer justify-between items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                   href="#"
                   role="button" data-te-collapse-collapsed="" aria-expanded="false" tabindex="0">
                    <span class="flex h-12 cursor-pointer justify-between items-center ">
                        <i class='bx bxs-user-detail bx-sm px-1'></i>
                        {{ __('Humans Resources') }}
                    </span>
                    <span
                        class=" mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none text-gray-600 dark:text-gray-300 ">
                        <i class="bx bx-chevron-down bx-sm" x-bind:class="open == true ? 'bx-rotate-180' : '' "></i>
                    </span>
                </a>
                <ul class=" relative m-0  list-none p-0 data-[te-collapse-show]:block space-y-3 transition "
                    x-cloak x-show="open">
                    @if(havePermissionTo('users.index'))
                        <li class="relative">
                            <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                               data-te-sidenav-link-ref="" href="{{ route('admin.users.index') }}"
                               tabindex="0">{{ __("Users") }}</a>
                        </li>
                    @endif
                    @if(havePermissionTo('roles.index'))
                        <li class="relative">
                            <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                               data-te-sidenav-link-ref="{{ route('admin.roles.index') }}"
                               href="{{ route('admin.roles.index') }}" tabindex="0">{{ __('Roles') }}</a>
                        </li>
                    @endif
                    @if(havePermissionTo('employees.index'))
                        <li class="relative">
                            <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                               data-te-sidenav-link-ref="" tabindex="0">{{ __('Employees') }}</a>
                        </li>
                    @endif
                </ul>
            </li>

            <li class="relative" x-data="dropdown" @click="toggle()">
                <a class="flex h-12 cursor-pointer justify-between items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                   role="button" aria-expanded="false" tabindex="0">
                    <span class="flex h-12 cursor-pointer justify-between items-center ">
                        <i class='bx bxs-user-account bx-sm px-1'></i>
                        {{ __('Clients Management') }}
                    </span>
                    <span class=" mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&amp;>svg]:text-gray-600 dark:[&amp;>svg]:text-gray-300">
                          <i class="bx bx-chevron-down bx-sm"
                             x-bind:class="open == true ? 'bx-rotate-180' : '' "></i>
                    </span>
                </a>
                <ul class=" relative m-0  list-none p-0 data-[te-collapse-show]:block " x-cloak x-show="open">
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                           data-te-sidenav-link-ref="" tabindex="0">{{ __("Clients") }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                           data-te-sidenav-link-ref="" tabindex="0">{{ __('Actions') }}</a>
                    </li>
                </ul>
            </li>
            <li class="relative" x-data="dropdown" @click="toggle()">
                <a class="flex h-12 cursor-pointer justify-between items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom">
                    <span class="flex h-12 cursor-pointer justify-between items-center ">
                        <i class='bx bx-box bx-sm px-1'></i>
                        {{ __('Inventory') }}
                    </span>
                    <span class=" mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&amp;>svg]:text-gray-600 dark:[&amp;>svg]:text-gray-300">
                          <i class="bx bx-chevron-down bx-sm" x-bind:class="open == true ? 'bx-rotate-180' : '' "></i>
                    </span>
                </a>
                <ul class=" relative m-0 list-none p-0 data-[te-collapse-show]:block " x-show="open">
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                          >{{ __("Warehouses") }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                          >{{ __('Products') }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                           >{{ __('Transfers') }}</a>
                    </li>
                </ul>
            </li>
            <li class="relative" x-data="dropdown" @click="toggle()">
                <a class="flex h-12 cursor-pointer justify-between items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom">
                    <span class="flex h-12 cursor-pointer justify-between items-center ">
                        <i class='bx bx-paperclip bx-sm px-1 rotate-90'></i>
                        {{ __('Accounting') }}
                    </span>
                    <span class=" mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&amp;>svg]:text-gray-600 dark:[&amp;>svg]:text-gray-300">
                          <i class="bx bx-chevron-down bx-sm" x-bind:class="open == true ? 'bx-rotate-180' : '' "></i>
                    </span>
                </a>
                <ul class=" relative m-0  list-none p-0 data-[te-collapse-show]:block " x-show="open">
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                        >{{ __("Accounts") }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                          >{{ __('Entries') }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                           >{{ __('General Ledger') }}</a>
                    </li>
                </ul>
            </li>
            <li class="relative" x-data="dropdown" @click="toggle()">
                <a class="flex h-12 cursor-pointer justify-between items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom">
                    <span class="flex h-12 cursor-pointer justify-between items-center ">
                        <i class='bx bxs-shopping-bags bx-sm px-1'></i>
                        {{ __('Purchases') }}
                    </span>
                    <span class=" mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&amp;>svg]:text-gray-600 dark:[&amp;>svg]:text-gray-300">
                          <i class="bx bx-chevron-down bx-sm" x-bind:class="open == true ? 'bx-rotate-180' : '' "></i>
                    </span>
                </a>
                <ul class=" relative m-0 list-none p-0 data-[te-collapse-show]:block " x-show="open">
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                          >{{ __("Bills") }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                           >{{ __('Suppliers') }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom">{{ __('Payments') }}</a>
                    </li>
                </ul>
            </li>
            <li class="relative" x-data="dropdown" @click="toggle()">
                <a class="flex h-12 cursor-pointer justify-between items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom">
                    <span class="flex h-12 cursor-pointer justify-between items-center ">
                        <i class='bx bx-dollar bx-sm px-1'></i>
                        {{ __('Sales') }}
                    </span>
                    <span class=" mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&amp;>svg]:text-gray-600 dark:[&amp;>svg]:text-gray-300">
                          <i class="bx bx-chevron-down bx-sm" x-bind:class="open == true ? 'bx-rotate-180' : '' "></i>
                    </span>
                </a>
                <ul class=" relative m-0  list-none p-0 data-[te-collapse-show]:block " x-show="open" >
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                          >{{ __("Invoices") }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                        >{{ __('Revenues') }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                          >{{ __('Returns') }}</a>
                    </li>
                </ul>
            </li>
            <li class="relative" >
                <a class="group flex h-12 cursor-pointer items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-700 outline-none transition duration-300 ease-linear hover:bg-gray-300/30 hover:text-inherit hover:outline-none focus:bg-gray-300/30 focus:text-inherit focus:outline-none active:bg-gray-300/30 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                   href="#!" >
                    <i class='bx bxs-help-circle bx-sm'></i>
                    <span>{{ __('Help Center') }}</span>
                </a>
            </li>
            <li class="relative" x-data="dropdown" @click="toggle()">
                <a class="flex h-12 cursor-pointer justify-between items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom">
                    <span class="flex h-12 cursor-pointer justify-between items-center ">
                        <i class='bx bxs-cog bx-sm px-1'></i>
                        {{ __('Setting') }}
                    </span>
                    <span class=" mr-[0.8rem] transition-transform duration-300 ease-linear motion-reduce:transition-none [&amp;>svg]:text-gray-600 dark:[&amp;>svg]:text-gray-300">
                          <i class="bx bx-chevron-down bx-sm" x-bind:class="open == true ? 'bx-rotate-180' : '' "></i>
                    </span>
                </a>
                <ul class=" relative m-0  list-none p-0 data-[te-collapse-show]:block " x-show="open">
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                          >{{ __("App Setting") }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                          >{{ __('Business Details') }}</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                           >{{ __('Invitations') }}</a>
                    </li>
                </ul>
            </li
        </ul>
        <div class="ps__rail-x group/x absolute bottom-0 h-[0.9375rem] hidden opacity-0 transition-[background-color,_opacity] duration-200 ease-linear motion-reduce:transition-none z-[1035] group-[&amp;.ps--active-x]/ps:block group-hover/ps:opacity-60 group-focus/ps:opacity-60 group-[&amp;.ps--scrolling-x]/ps:opacity-60 hover:!opacity-90 focus:!opacity-90 [&amp;.ps--clicking]:!opacity-90 outline-none group-[&amp;.ps--active-x]/ps:bg-transparent hover:!bg-[#eee] focus:!bg-[#eee] [&amp;.ps--clicking]:!bg-[#eee] dark:hover:!bg-[#555] dark:focus:!bg-[#555] dark:[&amp;.ps--clicking]:!bg-[#555]"
            style="left: 0px; top: 0px; transform: translateY(calc(-100% + 295px));">
            <div class="ps__thumb-x absolute bottom-0.5 rounded-md h-1.5 group-focus/ps:opacity-100 group-active/ps:opacity-100 [transition:background-color_.2s_linear,_height_.2s_ease-in-out] group-hover/x:h-[11px] group-focus/x:h-[0.6875rem] group-[&amp;.ps--clicking]/x:bg-[#999] group-[&amp;.ps--clicking]/x:h-[11px] outline-none bg-[#aaa] group-hover/x:bg-[#999] group-focus/x:bg-[#999]"
                tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y group/y absolute right-0 w-[0.9375rem] hidden opacity-0 transition-[background-color,_opacity] duration-200 ease-linear motion-reduce:transition-none z-[1035] group-[&amp;.ps--active-y]/ps:block group-hover/ps:opacity-60 group-focus/ps:opacity-60 group-[&amp;.ps--scrolling-y]/ps:opacity-60 hover:!opacity-90 focus:!opacity-90 [&amp;.ps--clicking]:!opacity-90 outline-none group-[&amp;.ps--active-y]/ps:bg-transparent hover:!bg-[#eee] focus:!bg-[#eee] [&amp;.ps--clicking]:!bg-[#eee] dark:hover:!bg-[#555] dark:focus:!bg-[#555] dark:[&amp;.ps--clicking]:!bg-[#555]"
            style="top: 0px; height: 687px; left: 0px; transform: translateX(calc(-100% + 240px));">
            <div class="ps__thumb-y absolute right-0.5 rounded-md w-1.5 group-focus/ps:opacity-100 group-active/ps:opacity-100 [transition:background-color_.2s_linear,_width_.2s_ease-in-out,_opacity] group-hover/y:w-[11px] group-focus/y:w-[0.6875rem] group-[&amp;.ps--clicking]/y:w-[11px] outline-none bg-[#aaa] group-hover/y:bg-[#999] group-focus/y:bg-[#999] group-[&amp;.ps--clicking]/y:bg-[#999]"
                tabindex="0" style="top: 0px; height: 545px;"></div>
        </div>
    </div>
    <div class="absolute bottom-0 h-12 w-full bg-inherit text-center">
        <hr class="mb-3 border-gray-300">
        <p>Erp System</p>
    </div>
</nav>

{{--                            <ul class="pl-9 mt-1 @if(!in_array(Request::segment(1), ['dashboard'])){{ 'hidden' }}@endif" :class="open ? '!block' : 'hidden'">--}}
{{--                                <li class="mb-1 last:mb-0">--}}
{{--                                    <a class="block text-slate-400 hover:text-slate-200 transition duration-150 truncate @if(Route::is('dashboard')){{ '!text-indigo-500' }}@endif" href="{{ route('admin.dashboard') }}">--}}
{{--                                        <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Main</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}


@pushonce('scripts')
    <script defer>
        document.addEventListener('alpine:init', () => {
            // Stores variable globally
            Alpine.store('sidebar', {
                active: 'dashboard',
            });
            // Creating component Dropdown
            Alpine.data('dropdown', () => ({
                open: false,
                toggle(tab) {
                    this.open = !this.open;
                    Alpine.store('sidebar').active = tab;
                    console.log(tab)
                },
                activeClass: 'bg-gray-800 text-gray-200',
                expandedClass: 'border-l border-gray-400 ml-4 pl-4',
                shrinkedClass: 'sm:absolute top-0 left-20 sm:shadow-md sm:z-10 sm:bg-gray-900 sm:rounded-md sm:p-4 border-l sm:border-none border-gray-400 ml-4 pl-4 sm:ml-0 w-28'
            }));
            // Creating tooltip
            Alpine.data('tooltip', () => ({
                show: false,
                visibleClass: 'block sm:absolute -top-7 sm:border border-gray-800 left-5 sm:text-sm sm:bg-gray-900 sm:px-2 sm:py-1 sm:rounded-md'
            }))

        })
    </script>
@endpushonce
