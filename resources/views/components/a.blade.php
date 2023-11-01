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
<a     {{ $attributes }} @if($disabled) disabled @endif
class=" flex items-center text-white bg-{{$color}}-500 hover:bg-{{$color}}-800  focus:ring-4 focus:ring-{{$color}}-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-{{$color}}-600 dark:hover:bg-{{$color}}-700 focus:outline-none dark:focus:ring-{{$color}}-800 {{$class}}     @if($rounded) rounded-full @endif"
@if($collapse)
        x-data="collapse"
        @click="toggle('{{$target}}')"
@endif

>
    @if($icon)  <x-i  name="{{$icon}}"></x-i> @endif
    {{ $slot }}
</a>
