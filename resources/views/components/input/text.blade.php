@props([
    'disabled' => false,
    'required' => false,
    'placeholder' => '',
    'value' => '',
    'name' => '',
        'id' => '',
    'type' => 'text',
    'class' => '',
    'aria_label' => '',
        'label' => '',
        'autofocus' => false,
        'autocomplete' => true,
        'model' => null,
        'icon' => null
])
@if($label) <label class="mx-2">{{ $label }}</label> @endif

<div class="my-2 relative rounded-md shadow-sm">
    @if($icon)
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-600">
                             <x-i name="{{$icon}}"> </x-i>
        </span>
    @endif

    <input type="{{$type}}"
           placeholder="{{$placeholder}}"
           class=" w-full px-4 py-2 appearance-none bg-gray-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md active:border-slate-500 dark:active:bg-slate-600 focus:bg-gray-200  dark:focus:bg-slate-600 {{ $class }}"
           value="{{ $value }}"
           {{ $disabled ? 'disabled' : '' }}  {{ $autofocus ? 'autofocus' : '' }} {{ $autocomplete ? 'autocomplete' : '' }}
           {{ $required ? 'required' : '' }} placeholder="{{ $placeholder }}" name="{{ $name }}"
           @if($id) id="{{ $id }}" @endif
           @if($aria_label) aria-label="{{$aria_label}}" @endif
        {{ $attributes->wire('model') }}
    >
</div>
