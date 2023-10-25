<nav id="full-screen-example" dir="{{ $localeDirs[App::getLocale()] }}"
     class="fixed left-0 top-0 z-[1035] h-screen w-60 -translate-x-full overflow-hidden bg-gray-200 shadow-[0_4px_12px_0_rgba(0,0,0,0.07),_0_2px_4px_rgba(0,0,0,0.05)] dark:bg-zinc-800 md:data-[te-sidenav-hidden='false']:translate-x-0 sidenav-dark"
     data-te-sidenav-init="" data-te-sidenav-mode-breakpoint-over="0" data-te-sidenav-mode-breakpoint-side="sm"
     data-te-sidenav-hidden="false" data-te-sidenav-color="dark" data-te-sidenav-content="#content"
     data-te-sidenav-scroll-container="#scrollContainer"
     style="{{ $localeDirs[App::getLocale()] === 'rtl' ? 'right:0;' : '' }} width: 240px; height: 100vh; position: fixed;  transition: all 0.3s linear 0s; transform: translateX(0%);">
    <div class="pt-6 text-center">
        <x-logo></x-logo>
        {{ config('app.name') }}
    </div>
    <div id="scrollContainer" style="max-height: calc(100% - 265px); position: relative;"
         class="perfect-scrollbar ps--active-y group/ps overflow-hidden [overflow-anchor:none] touch-none">
        <ul class="relative m-0 list-none px-[0.2rem]" data-te-sidenav-menu-ref="">
            <li class="relative">
                <a class="group flex h-12 cursor-pointer items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-700 outline-none transition duration-300 ease-linear hover:bg-gray-300/30 hover:text-inherit hover:outline-none focus:bg-gray-300/30 focus:text-inherit focus:outline-none active:bg-gray-300/30 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                   href="#!" data-te-sidenav-link-ref="" tabindex="0">
                    <i class="bx bxs-dashboard bx-sm"></i>
                    <span>{{ __('Dashbaord') }}</span>
                    <span
                        class="mr-4 [&amp;>svg]:h-3.5 [&amp;>svg]:w-3.5 [&amp;>svg]:fill-gray-700 dark:[&amp;>svg]:fill-gray-300">

          </span>

                </a>
            </li>
            <li class="relative">
                <a class="group flex h-12 cursor-pointer items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-700 outline-none transition duration-300 ease-linear hover:bg-gray-300/30 hover:text-inherit hover:outline-none focus:bg-gray-300/30 focus:text-inherit focus:outline-none active:bg-gray-300/30 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                   href="#!" data-te-sidenav-link-ref="" tabindex="0">
                    <i class="bx bxs-report bx-sm"></i>
                    <span>{{ __('Reports') }}</span>
                    <span
                        class="mr-4 [&amp;>svg]:h-3.5 [&amp;>svg]:w-3.5 [&amp;>svg]:fill-gray-700 dark:[&amp;>svg]:fill-gray-300">

          </span>

                </a>
            </li>
        </ul>
        <hr class="border-gray-300">
        <ul class="relative m-0 list-none px-[0.2rem]">

            <li class="relative" >
                <a class="group flex h-12 cursor-pointer justify-between truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-700 outline-none transition duration-300 ease-linear hover:bg-gray-300/30 hover:text-inherit hover:outline-none focus:bg-gray-300/30 focus:text-inherit focus:outline-none active:bg-gray-300/30 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10" data-te-sidenav-link-ref="" data-te-collapse-init="" href="#sidenav-collapse-549319-0-0" role="button" data-te-collapse-collapsed="" aria-expanded="false" tabindex="0">

                    <span>{{ __('Settings') }}</span>
                    <span  data-te-sidenav-rotate-icon-ref="">
                        <i class="bx bx-chevron-up bx-sm"></i>
                       </span>
                </a>
                <ul class="show relative m-0 list-none p-0 data-[te-collapse-show]:block !visible hidden" data-te-sidenav-collapse-ref="" id="sidenav-collapse-549319-0-0" style="" data-te-collapse-item="">
                    <li class="relative">
                        <a @if(Route::is('admin.dashboard')){{ '!text-indigo-500' }}@endif" href="{{ route('admin.dashboard') }}" class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-700 outline-none transition duration-300 ease-linear hover:bg-gray-300/30 hover:text-inherit hover:outline-none focus:bg-gray-300/30 focus:text-inherit focus:outline-none active:bg-gray-300/30 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom" data-te-sidenav-link-ref="" tabindex="0">
                            Family</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-700 outline-none transition duration-300 ease-linear hover:bg-gray-300/30 hover:text-inherit hover:outline-none focus:bg-gray-300/30 focus:text-inherit focus:outline-none active:bg-gray-300/30 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom" data-te-sidenav-link-ref="" tabindex="0">Friends</a>
                    </li>
                    <li class="relative">
                        <a class="flex h-6 cursor-pointer items-center truncate rounded-[5px] py-4 pl-[3.4rem] pr-6 text-[0.78rem] text-gray-700 outline-none transition duration-300 ease-linear hover:bg-gray-300/30 hover:text-inherit hover:outline-none focus:bg-gray-300/30 focus:text-inherit focus:outline-none active:bg-gray-300/30 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom" data-te-sidenav-link-ref="" tabindex="0">Work</a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <a class="group flex h-12 cursor-pointer items-center truncate rounded-[5px] px-6 py-4 text-[0.875rem] text-gray-700 outline-none transition duration-300 ease-linear hover:bg-gray-300/30 hover:text-inherit hover:outline-none focus:bg-gray-300/30 focus:text-inherit focus:outline-none active:bg-gray-300/30 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 relative overflow-hidden inline-block align-bottom"
                   href="#!" data-te-sidenav-link-ref="" tabindex="0">
          <span
              class="mr-4 [&amp;>svg]:h-3.5 [&amp;>svg]:w-3.5 [&amp;>svg]:fill-gray-700 dark:[&amp;>svg]:fill-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
              <!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
              <path
                  d="M370.7 96.1C346.1 39.5 289.7 0 224 0S101.9 39.5 77.3 96.1C60.9 97.5 48 111.2 48 128v64c0 16.8 12.9 30.5 29.3 31.9C101.9 280.5 158.3 320 224 320s122.1-39.5 146.7-96.1c16.4-1.4 29.3-15.1 29.3-31.9V128c0-16.8-12.9-30.5-29.3-31.9zM336 144v16c0 53-43 96-96 96H208c-53 0-96-43-96-96V144c0-26.5 21.5-48 48-48H288c26.5 0 48 21.5 48 48zM189.3 162.7l-6-21.2c-.9-3.3-3.9-5.5-7.3-5.5s-6.4 2.2-7.3 5.5l-6 21.2-21.2 6c-3.3 .9-5.5 3.9-5.5 7.3s2.2 6.4 5.5 7.3l21.2 6 6 21.2c.9 3.3 3.9 5.5 7.3 5.5s6.4-2.2 7.3-5.5l6-21.2 21.2-6c3.3-.9 5.5-3.9 5.5-7.3s-2.2-6.4-5.5-7.3l-21.2-6zM112.7 316.5C46.7 342.6 0 407 0 482.3C0 498.7 13.3 512 29.7 512H128V448c0-17.7 14.3-32 32-32H288c17.7 0 32 14.3 32 32v64l98.3 0c16.4 0 29.7-13.3 29.7-29.7c0-75.3-46.7-139.7-112.7-165.8C303.9 338.8 265.5 352 224 352s-79.9-13.2-111.3-35.5zM176 448c-8.8 0-16 7.2-16 16v48h32V464c0-8.8-7.2-16-16-16zm96 32c8.8 0 16-7.2 16-16s-7.2-16-16-16s-16 7.2-16 16s7.2 16 16 16z"></path>
            </svg>
          </span>
                    <span>Log out</span>
                </a>
            </li>
        </ul>
        <div
            class="ps__rail-x group/x absolute bottom-0 h-[0.9375rem] hidden opacity-0 transition-[background-color,_opacity] duration-200 ease-linear motion-reduce:transition-none z-[1035] group-[&amp;.ps--active-x]/ps:block group-hover/ps:opacity-60 group-focus/ps:opacity-60 group-[&amp;.ps--scrolling-x]/ps:opacity-60 hover:!opacity-90 focus:!opacity-90 [&amp;.ps--clicking]:!opacity-90 outline-none group-[&amp;.ps--active-x]/ps:bg-transparent hover:!bg-[#eee] focus:!bg-[#eee] [&amp;.ps--clicking]:!bg-[#eee] dark:hover:!bg-[#555] dark:focus:!bg-[#555] dark:[&amp;.ps--clicking]:!bg-[#555]"
            style="left: 0px; top: 0px; transform: translateY(calc(-100% + 295px));">
            <div
                class="ps__thumb-x absolute bottom-0.5 rounded-md h-1.5 group-focus/ps:opacity-100 group-active/ps:opacity-100 [transition:background-color_.2s_linear,_height_.2s_ease-in-out] group-hover/x:h-[11px] group-focus/x:h-[0.6875rem] group-[&amp;.ps--clicking]/x:bg-[#999] group-[&amp;.ps--clicking]/x:h-[11px] outline-none bg-[#aaa] group-hover/x:bg-[#999] group-focus/x:bg-[#999]"
                tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div
            class="ps__rail-y group/y absolute right-0 w-[0.9375rem] hidden opacity-0 transition-[background-color,_opacity] duration-200 ease-linear motion-reduce:transition-none z-[1035] group-[&amp;.ps--active-y]/ps:block group-hover/ps:opacity-60 group-focus/ps:opacity-60 group-[&amp;.ps--scrolling-y]/ps:opacity-60 hover:!opacity-90 focus:!opacity-90 [&amp;.ps--clicking]:!opacity-90 outline-none group-[&amp;.ps--active-y]/ps:bg-transparent hover:!bg-[#eee] focus:!bg-[#eee] [&amp;.ps--clicking]:!bg-[#eee] dark:hover:!bg-[#555] dark:focus:!bg-[#555] dark:[&amp;.ps--clicking]:!bg-[#555]"
            style="top: 0px; height: 687px; left: 0px; transform: translateX(calc(-100% + 240px));">
            <div
                class="ps__thumb-y absolute right-0.5 rounded-md w-1.5 group-focus/ps:opacity-100 group-active/ps:opacity-100 [transition:background-color_.2s_linear,_width_.2s_ease-in-out,_opacity] group-hover/y:w-[11px] group-focus/y:w-[0.6875rem] group-[&amp;.ps--clicking]/y:w-[11px] outline-none bg-[#aaa] group-hover/y:bg-[#999] group-focus/y:bg-[#999] group-[&amp;.ps--clicking]/y:bg-[#999]"
                tabindex="0" style="top: 0px; height: 545px;"></div>
        </div>
    </div>
    <div class="absolute bottom-0 h-24 w-full bg-inherit text-center">
        <hr class="mb-6 border-gray-300">
        <p>Erp System</p>
    </div>
</nav>

{{--                            <ul class="pl-9 mt-1 @if(!in_array(Request::segment(1), ['dashboard'])){{ 'hidden' }}@endif" :class="open ? '!block' : 'hidden'">--}}
{{--                                <li class="mb-1 last:mb-0">--}}
{{--                                    <a class="block text-slate-400 hover:text-slate-200 transition duration-150 truncate @if(Route::is('dashboard')){{ '!text-indigo-500' }}@endif" href="{{ route('admin.dashboard') }}">--}}
{{--                                        <span class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Main</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}


