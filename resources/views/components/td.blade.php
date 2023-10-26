@props([
    'medium' =>false,
    'class' => '',
    'colspan' => null
    ])

<td @if($colspan) colspan="{{ $colspan }}" @endif class="whitespace-nowrap px-6 py-4 {{ $class }} @if($medium) font-medium @endif">{{ $slot }}</td>
