<div dir="ltr"
    x-data="{ color: '#ffffff' }"
    x-init="
        // Wire up to show the picker when clicking the 'Change' button.
        picker = new Picker($refs.button);
        // Run this callback every time a new color is picked.
        picker.onDone = rawColor => {
            // Set the Alpine 'color' property.
            color = rawColor.hex;
            // Dispatch the color property for 'wire:model' to pick up.
            $dispatch('color', color)
        }
    "
wire:ignore
{{ $attributes }}
>
<span x-text="color" :style="`background: ${color}`"></span>
<button x-ref="button">Change</button>
</div>
