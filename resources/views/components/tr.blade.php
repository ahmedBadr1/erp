@props([
    'class' => ''
])

<tr class="border-b dark:border-neutral-500 {{ $class }}" {{ $attributes }}>
    {{ $slot }}
</tr>
