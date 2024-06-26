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
<!--Email input-->
<div class="relative mb-3">
    <input
        type="{{$type}}"
        class="peer m-0 block h-[58px] w-full rounded border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-4 text-base font-normal leading-tight text-neutral-700 transition duration-200 ease-linear placeholder:text-transparent focus:border-primary focus:pb-[0.625rem] focus:pt-[1.625rem] focus:text-neutral-700 focus:outline-none peer-focus:text-primary dark:border-neutral-600 dark:text-neutral-200 dark:focus:border-primary dark:peer-focus:text-primary [&:not(:placeholder-shown)]:pb-[0.625rem] [&:not(:placeholder-shown)]:pt-[1.625rem]"
        id="floatingInput"
        placeholder="{{$placeholder}}"
    @if($model)
        wire:model.lazy="{{$model}}"
        @endif
        />
    <label
        for="floatingInput"
        class="pointer-events-none absolute @if($localeDirs[App::getLocale()] == 'rtl') right-0 @else left-0 @endif  top-0 origin-[0_0] border border-solid border-transparent px-3 py-4 text-neutral-500 transition-[opacity,_transform] duration-200 ease-linear peer-focus:-translate-y-2 peer-focus:translate-x-[0.15rem] peer-focus:scale-[0.85] peer-focus:text-primary peer-[:not(:placeholder-shown)]:-translate-y-2 peer-[:not(:placeholder-shown)]:translate-x-[0.15rem] peer-[:not(:placeholder-shown)]:scale-[0.85] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-primary"
    >{{ $label }}</label>
</div>
