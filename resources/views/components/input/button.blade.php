@props([
    'class' => '',
    'icon' => '',
    'color' => 'indigo-500',
    'disabled' => false,
    'type' => 'button',
    'collapse' => false,
    'target' => null,
])
<button type="{{$type}}" @if($disabled) disabled @endif
class="middle none center rounded-lg bg-{{$color}} py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-{{$color}}/20 transition-all hover:shadow-lg hover:shadow-{{$color}}/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none {{$class}}"
@if($collapse)
            data-te-collapse-init
        data-te-ripple-init
        data-te-ripple-color="light"
        data-te-target="#{{$target}}"
        aria-expanded="false"
        aria-controls="{{$target}}"
@endif
>
    @if($icon)  <i  class="bx {{$icon}} bx-sm"></i> @endif
    {{ $slot }}

</button>
