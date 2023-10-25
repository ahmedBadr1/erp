@props([
    'closeable' => true,
    'type' => null,
     'bg' => null,
    'color' => null,
    ])
<div
@if($type === 'success')
    class="mb-4 rounded-lg  bg-[#D6FAE4] px-6 py-5 text-base text-[#094621]  flex justify-between"
@elseif($type === 'error')
    class="mb-4 rounded-lg  bg-[#FAE5E9] px-6 py-5 text-base text-[#6A1523] "
@elseif($type === 'info')
    class="mb-4 rounded-lg  bg-[#E7F4F9] px-6 py-5 text-base text-[#1A5265] "
@elseif($type === 'primary')
    class="mb-4 rounded-lg  bg-[#E3EBF7] px-6 py-5 text-base text-[#183058] "
@else
    class="mb-4 rounded-lg  bg-{{$bg}}-100 px-6 py-5 text-base text-{{$color}}-700 dark:bg-{{$bg}}-900 dark:text-{{$color}}-600 "
@endif
    role="alert"
    data-te-alert-init
    data-te-alert-show
>
 <span class="flex justify-start items-center">
    @if($type === 'success')
         <i class='bx bx-check-circle bx-sm'></i>
    @elseif($type === 'error')
         <i class='bx bx-error-circle bx-sm'></i>
     @elseif($type === 'info')
         <i class='bx bx-info-circle bx-sm'></i>
    @endif
    <span class="mx-2">
             {{ $slot }}
    </span>

    </span>
@if($closeable)
    <button
        type="button"
        class=" box-content rounded-none border-none p-1 text-warning-900 opacity-50 hover:text-warning-900 hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
        data-te-alert-dismiss
        aria-label="Close">
    <span
        class="w-[1em] focus:opacity-100 disabled:pointer-events-none disabled:select-none disabled:opacity-25 [&.disabled]:pointer-events-none [&.disabled]:select-none [&.disabled]:opacity-25">
      <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="currentColor"
          class="h-6 w-6">
        <path
            fill-rule="evenodd"
            d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z"
            clip-rule="evenodd" />
      </svg>
    </span>
    </button>
    @endif
</div>
