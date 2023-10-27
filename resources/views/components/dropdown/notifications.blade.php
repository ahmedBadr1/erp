@props([
    'align' => 'right'
])


<div class="relative inline-flex" x-data="{ open: false }">
    <button
        class="w-8 h-8  flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600/80 rounded-full"
        :class="{ 'bg-slate-200': open }"
        aria-haspopup="true"
        @click.prevent="open = !open"
        :aria-expanded="open"
    >
        <span class="sr-only">{{__('Language')}}</span>
             <x-i name="bxs-bell" size="sm" animate="bx-burst-hover"></x-i>
            <!-- Notification counter -->
        <span
            class="absolute -mt-4 ml-2.5 rounded-full bg-red-500 px-[0.35em] py-[0.15em] text-[0.6rem] font-bold leading-none text-white"
        >1</span>

    </button>
    <div
        class="origin-top-right z-10 absolute top-full min-w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 py-1.5 rounded shadow-lg overflow-hidden mt-1 {{$align === 'right' ? 'right-0' : 'left-0'}}"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        x-show="open"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <ul>
{{--            @foreach(auth()->user()->notifications as $key=> $notification)--}}
{{--                <li>--}}
{{--                    <a class="block w-full whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400 dark:text-neutral-200 dark:hover:bg-white/30"--}}
{{--                       href="{{ $notification['url'] }}" @click="open = false" @focus="open = true" @focusout="open = false">--}}
{{--                        <span>   {{ $notification['body'] }}</span>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @endforeach--}}
                <li>
                    <a class="block w-full  whitespace-nowrap bg-transparent px-4 py-2 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400 dark:text-neutral-200 dark:hover:bg-white/30"
                       href="#" @click="open = false" @focus="open = true" @focusout="open = false">
                        <span>  Action</span>
                    </a>
                </li>

        </ul>
    </div>
</div>

