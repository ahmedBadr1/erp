@props([
    'cols' => 12,
    'colsMd' => null ,
    'gap' => 4,
        'gapMd' => null,
        'hidden' => false
    ])

<div class="grid mx-2
@switch($cols)
     @case(3)
   lg:grid-cols-3 md:grid-cols-2
        @break
    @case(4)
   lg:grid-cols-4 md:grid-cols-2
        @break
            @case(5)
   xl:grid-cols-5
             @case(6)
   xl:grid-cols-6 lg:grid-cols-3 md:grid-cols-2
        @break
                     @case(12)
   xl:grid-cols-12 lg:grid-cols-6 md:grid-cols-3
        @break
    @default
        grid-cols-2
@endswitch
 gap-{{$gap}}  @if($colsMd)md:grid-cols-{{$colsMd}}@endif @if($gapMd) md:gap-{{$gapMd}} @endif @if($hidden) hidden @endif  "
{{ $attributes }}>
    {{ $slot }}
</div>
