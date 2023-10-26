@props([
    'h' => 1
])
@if($h == 1)
    <h1 class="mb-2 mt-0 text-5xl font-medium leading-tight text-primary">
        {{$slot}}
    </h1>
@elseif($h == 2)
    <h2 class="mb-2 mt-0 text-4xl font-medium leading-tight text-primary">
        {{$slot}}
    </h2>
@elseif($h == 3)
    <h3 class="mb-2 mt-0 text-3xl font-medium leading-tight text-primary">
        {{$slot}}
    </h3>
@elseif($h == 4)
    <h4 class="mb-2 mt-0 text-2xl font-medium leading-tight text-primary">
        {{$slot}}
    </h4>
@elseif($h == 5)
    <h5 class="mb-2 mt-0 text-xl font-medium leading-tight text-primary">
        {{$slot}}
    </h5>
@elseif($h == 6)
    <h6 class="mb-2 mt-0 text-base font-medium leading-tight text-primary">
        {{$slot}}
    </h6>
@else
    <h1 class="mb-2 mt-0 text-5xl font-medium leading-tight text-primary">
        {{$slot}}
    </h1>
@endif


