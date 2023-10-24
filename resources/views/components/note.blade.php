@props([
    'color' => 'amber',
    'icon' => 'error'
])
<div class="bg-{{ $color }}-100 text-{{ $color }}-900 px-3 py-2 rounded">
    <i class='bx bx-{{ $icon }} bx-sm'></i>
    <span class="text-sm">
                    {{ $slot }}
                </span>
</div>
