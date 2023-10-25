@props([
    'align' => 'right'
])
<div
    class="relative"
    data-te-dropdown-ref
    data-te-dropdown-alignment="end">
    <!-- First dropdown trigger -->
    <a
        class="hidden-arrow ml-2.5 flex items-center text-neutral-600 transition duration-200 hover:text-neutral-700 hover:ease-in-out focus:text-neutral-700 disabled:text-black/30 motion-reduce:transition-none dark:text-neutral-200 dark:hover:text-neutral-300 dark:focus:text-neutral-300 [&.active]:text-black/90 dark:[&.active]:text-neutral-400 w-8 h-8 justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600/80 rounded-full"
        href="#"
        id="dropdownLangButton"
        role="button"
        data-te-dropdown-toggle-ref
        aria-expanded="false">
        <!-- Dropdown trigger icon -->
        <span class="[&>svg]:w-5">
              <i class='bx bxs-flag-alt' ></i>
          </span>
    </a>
    <!-- First dropdown menu -->
    <ul
        class="absolute z-[1000] float-left m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-left text-base shadow-lg dark:bg-neutral-700 [&[data-te-dropdown-show]]:block"
        aria-labelledby="dropdownLangButton"
        data-te-dropdown-menu-ref>
        <!-- First dropdown menu items -->
        @foreach($langs as $key=> $lang)
            <li>
                <a class="block w-full whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400 dark:text-neutral-200 dark:hover:bg-white/30"
                    href="{{ route('lang.switch',$key) }}"
                    data-te-dropdown-item-ref>
                    {{ $lang }}</a>
            </li>
        @endforeach

    </ul>
</div>

