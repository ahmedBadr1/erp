@props([
    'type' => null,
     'bg' => null,
    'color' => null,
    ])
<div
@if($type === 'success')
    class="mb-4 rounded-lg  bg-[#D6FAE4] px-6 py-5 text-base text-[#094621] "
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
</div>
