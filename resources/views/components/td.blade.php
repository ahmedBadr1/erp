@props([
    'medium' =>false,
    'class' => '',
    'colspan' => null,
    'center' =>false
    ])

<td @if($colspan) colspan="{{ $colspan }}" @endif class="whitespace-nowrap @if($center)  flex justify-center @endif text-center  px-6 py-4 {{ $class }} @if($medium) font-medium @endif" {{ $attributes }}>{{ $slot }}</td>
