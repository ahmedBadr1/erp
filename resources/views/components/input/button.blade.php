@props([
    'class' => '',
    'icon' => '',
    'color' => 'indigo',
    'disabled' => false,
    'type' => 'button',
    'collapse' => false,
    'target' => null,
    'rounded' => false,
])
<button type="{{$type}}" @if($disabled) disabled @endif
class="w-full flex items-center middle none center rounded-lg bg-{{$color}}-500  py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-{{$color}}/20 transition-all hover:shadow-lg hover:shadow-{{$color}}/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none {{$class}}     @if($rounded) rounded-full @endif"
@if($collapse)
        aria-expanded="false"
        aria-controls="{{$target}}"
@endif

        x-data="{ expanded: false }"
        @click="expanded = ! expanded"
>
    @if($icon)  <x-i  name="{{$icon}}"></x-i> @endif
    {{ $slot }}
</button>
