@props([
    'name' => null,

    ])

<div class="relative z-0 w-full h-auto max-w-full group @error($name) text-red-800 dark:text-red-600 placeholder-red-700 @enderror ">
        {{$slot}}
    @error($name)
    <div class="text-xs text-red-800 dark:text-red-600">{{ $message }}</div>
    @enderror
</div>
