@props([
    'name' => null,
    'cols' => 1,
    ])

<div class="relative z-0 w-full h-auto max-w-full group
@switch($cols)
     @case(2)
        md:col-span-2
            @break
     @case(3)
   lg:col-span-3 md:col-span-2
        @break
    @case(4)
   lg:col-span-4 md:col-span-2
        @break
            @case(5)
   xl:col-span-5
             @case(6)
   xl:col-span-6 lg:col-span-3 md:col-span-2
        @break
                     @case(12)
   xl:col-span-12 lg:col-span-6 md:col-span-3
        @break
    @default
        col-span-1
@endswitch

 @error($name) text-red-800 dark:text-red-600 placeholder-red-700 @enderror ">
        {{$slot}}
    @error($name)
    <div class="text-xs text-red-800 dark:text-red-600">{{ $message }}</div>
    @enderror
</div>
