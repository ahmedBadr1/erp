@props([
    'disabled' => false,
    'required' => false,
        'multiple' => false,
    'placeholder' => '',
    'value' => '',
    'name' => '',
        'id' => '',
    'class' => '',
        'model' => null,
        'help' => 'SVG, PNG, JPG or GIF (MAX. 800x400px).'
])

<input class=" w-full px-4 py-2 appearance-none bg-gray-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md active:border-slate-500 dark:active:bg-slate-600 focus:bg-gray-200  dark:focus:bg-slate-600"
       aria-describedby="file_input_help"  type="file"

       placeholder="{{$placeholder}}"
       value="{{ $value }}"
       {{ $disabled ?? 'disabled'  }}
       {{ $required ?? 'required' }} placeholder="{{ $placeholder }}" name="{{ $name }}"
       @if($id) id="{{ $id }}" @endif
       {{ $attributes->wire('model') }}
       @if($multiple) multiple @endif
>
@if($help)
    <p class="mt-1 text-sm text-gray-500 " id="{{$name}}_help">{{ $help }}</p>
@endif

