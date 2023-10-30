@props([
    'cols' => 2,
    'colsMd' => 3 ,
    'gap' => 4,
        'gapMd' => 4
    ])

<div class="grid grid-cols-{{$cols}} md:grid-cols-{{$colsMd}} gap-{{$gap}} md:gap-{{$gapMd}} " {{ $a }} {{ $attributes }}>
    {{ $slot }}
</div>
