@props([
    'class' => '',
    'responsive' => false
])

<div class="flex flex-col  {{ $class }} @if($responsive) overflow-x-auto @endif">
    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <table class="min-w-full text-left text-sm font-light">
                    <thead class="border-b font-medium dark:border-neutral-500">
                    {{$thead}}
                    </thead>
                    <tbody>
                    {{ $slot }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
