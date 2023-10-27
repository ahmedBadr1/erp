@props([
    'options' => null,
    'selected' => '',
    'name' => '',
    'class' => '',
        'model' => '',
        'placeholder' => null,
        'multiple' => false,
        'label' => null,
])
<div class="" dir="ltr">
<select data-te-select-init
        @if($multiple) multiple @endif
    wire:model.lazy="{{ $model }}"
    class=" {{ $class }}" name="{{ $name }}">
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
@if($label)<x-input.label >{{ __($label) }}</x-input.label> @endif
</div>

