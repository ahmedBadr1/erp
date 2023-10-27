@props([
    'options' => null,
    'selected' => '',
    'name' => '',
    'class' => '',
        'model' => '',
        'placeholder' => '',
        'multiple' => false,
        'label' => '',
])
<select data-te-select-init @if($multiple) multiple @endif
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
@if($label)<label data-te-select-label-ref>{{ __($label) }}</label> @endif


