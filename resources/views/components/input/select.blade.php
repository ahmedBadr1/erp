@props([
    'options' => null,
    'selected' => '',
    'name' => '',
    'class' => '',
        'model' => '',
        'placeholder' => null,

        'label' => null,
])
<div class="w-full m-2 relative rounded-md shadow-sm" >
    @if($label)<x-input.label >{{ __($label) }}</x-input.label> @endif
    <select
    {{ $attributes }}
        class=" w-full px-4 py-2 appearance-none bg-gray-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md active:border-slate-500 {{ $class }}"
    name="{{ $name }}">
    @if($placeholder)
        <option value="">{{ __('message.select',['model'=>__('names.'.$placeholder)] ) }}</option>
    @endif
    @if ($options)
        @foreach ($options as $key => $option)
            <option value="{{ $key }}" {{ intValue($selected) == $key ? 'selected' : '' }}>
                {{ $option }}
            </option>
        @endforeach
    @endif
        {{$slot}}
</select>
</div>




