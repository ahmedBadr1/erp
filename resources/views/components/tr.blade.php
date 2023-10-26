@props([
    'class' => ''
])

<tr class="border-b dark:border-neutral-500 {{ $class }}">
    {{ $slot }}
</tr>
