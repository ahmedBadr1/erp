@props([
    'class' => '',
    'icon' => '',
    'color' => 'indigo-500',
    'disabled' => false,
    'type' => 'button'
])
<button type="{{$type}}" @if($disabled) disabled @endif
class="middle none center rounded-lg bg-{{$color}} py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-{{$color}}/20 transition-all hover:shadow-lg hover:shadow-{{$color}}/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none {{$class}}"
        data-ripple-light="true">
    {{ $slot }}
    @if($icon)  <i  data-lucide="{{$icon}}"></i> @endif
</button>
