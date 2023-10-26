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
        'model' => null
])

<div class="w-72 mt-5" >
    <div class="relative h-10 w-full min-w-[200px]">
        <input type="{{ $type }}" @if($aria_label) aria-label="{{$aria_label}}" @endif
        @if($aria_label) aria-label="{{$aria_label}}" @endif
               class="peer h-full w-full rounded-[7px] border border-indigo-200 border-t-transparent bg-transparent px-3 py-2.5 font-sans text-sm font-normal text-gray-800 outline outline-0 transition-all placeholder-shown:border placeholder-shown:border-indigo-200 placeholder-shown:border-t-indigo-200 focus:border-2 focus:border-blue-500 focus:border-t-transparent focus:outline-0 disabled:border-0 disabled:bg-indigo-50"
               {!! $attributes->merge([
               'class' => 'form-control ' . $class,
               ]) !!}
               value="{{ $value }}"
               {{ $disabled ? 'disabled' : '' }}  {{ $autofocus ? 'autofocus' : '' }} {{ $autocomplete ? 'autocomplete' : '' }}
               {{ $required ? 'required' : '' }} placeholder="{{ $placeholder }}" name="{{ $name }}"
               @if($id)id="{{ $id }}" @endif
        />
        <label
            @if($localeDirs[App::getLocale()] == 'rtl')
                class="before:content[' '] after:content[' '] pointer-events-none absolute right-0 -top-1.5 flex h-full w-full
                        select-none text-[11px] font-normal leading-tight text-indigo-400 transition-all before:pointer-events-none
                        before:mt-[6.5px] before:mr-1 before:box-border before:block before:h-1.5 before:w-2.5 before:rounded-tl-md
                        before:border-t before:border-l before:border-indigo-200 before:transition-all after:pointer-events-none
                        after:mt-[6.5px] after:ml-1 after:box-border after:block after:h-1.5 after:w-2.5 after:flex-grow
                        after:rounded-tr-md after:border-t after:border-r after:border-indigo-200 after:transition-all
                        peer-placeholder-shown:text-sm peer-placeholder-shown:leading-[3.75] peer-placeholder-shown:text-indigo-500
                        peer-placeholder-shown:before:border-transparent peer-placeholder-shown:after:border-transparent
                        peer-focus:text-[11px] peer-focus:leading-tight peer-focus:text-blue-500 peer-focus:before:border-t-2
                        peer-focus:before:border-l-2 peer-focus:before:border-blue-500 peer-focus:after:border-t-2
                        peer-focus:after:border-r-2 peer-focus:after:border-blue-500 peer-disabled:text-transparent
                        peer-disabled:before:border-transparent peer-disabled:after:border-transparent
                        peer-disabled:peer-placeholder-shown:text-indigo-500">
            @else
                class="before:content[' '] after:content[' '] pointer-events-none absolute left-0 -top-1.5 flex h-full
                    w-full select-none text-[11px] font-normal leading-tight text-indigo-400 transition-all
                    before:pointer-events-none before:mt-[6.5px] before:mr-1 before:box-border before:block before:h-1.5
                    before:w-2.5 before:rounded-tl-md before:border-t before:border-l before:border-indigo-200
                    before:transition-all after:pointer-events-none after:mt-[6.5px] after:ml-1 after:box-border after:block
                    after:h-1.5 after:w-2.5 after:flex-grow after:rounded-tr-md after:border-t after:border-r
                    after:border-indigo-200 after:transition-all peer-placeholder-shown:text-sm
                    peer-placeholder-shown:leading-[3.75] peer-placeholder-shown:text-indigo-500
                    peer-placeholder-shown:before:border-transparent peer-placeholder-shown:after:border-transparent
                    peer-focus:text-[11px] peer-focus:leading-tight peer-focus:text-blue-500 peer-focus:before:border-t-2
                    peer-focus:before:border-l-2 peer-focus:before:border-blue-500 peer-focus:after:border-t-2
                    peer-focus:after:border-r-2 peer-focus:after:border-blue-500 peer-disabled:text-transparent
                    peer-disabled:before:border-transparent peer-disabled:after:border-transparent
                    peer-disabled:peer-placeholder-shown:text-indigo-500">
            @endif
            {{ $label }}
        </label>
    </div>
</div>
