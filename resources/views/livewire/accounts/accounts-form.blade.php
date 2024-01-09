<div>
    <x-h h="2">{{ __('message.'.$title,['model' => __('Account')]) }}</x-h>
    <form method="POST" action="#" wire:submit.prevent="save" enctype="multipart/form-data">
        @csrf
        <x-grid>
            <x-form.group name="name" cols="4">
                <x-input.label :value="__('Name')" :required="true"></x-input.label>
                <x-input.text type="text" wire:model.lazy="name" :required="true"
                              name="name"
                              placeholder="{{ __('Name') }}"></x-input.text>
            </x-form.group>
            <x-form.group name="category_id" cols="4">
                <x-input.label :value="__('names.category')" :required="true"></x-input.label>
                <x-input.select wire:model.lazy="category_id" :required="true" :options="$categories"  :disabled="!empty($account)"
                                placeholder="{{ __('names.category') }}"></x-input.select>
            </x-form.group>
            <x-form.group name="description" cols="4">
                <x-input.label :value="__('names.description')" :required="true"></x-input.label>
                <x-input.textarea wire:model.lazy="description" :required="false" name="description"
                                  placeholder="description"></x-input.textarea>
            </x-form.group>
            <x-form.group name="currency_id" cols="4">
                <x-input.label :value="__('names.currency')" :required="true"></x-input.label>
                <x-input.select wire:model.lazy="currency_id" :required="true" :options="$currencies" :disabled="!empty($account)"
                                placeholder="{{ __('names.currency') }}"></x-input.select>
            </x-form.group>
            @if(!$account)
                <x-form.group name="opening_balance" cols="4">
                    <x-input.label :value="__('Balance')" :required="true"></x-input.label>
                    <x-input.text type="number" wire:model.lazy="opening_balance" :required="false"
                                  name="opening_balance"
                                  placeholder="{{ __('Balance') }}"></x-input.text>
                </x-form.group>
                <x-form.group name="due" cols="4">
                    <x-input.label :value="__('names.opening_date')" :required="true"></x-input.label>
                    <input wire:model.live="opening_date" type="date"
                           class=" w-full px-4 py-2 appearance-none bg-gray-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md active:border-slate-500 dark:active:bg-slate-600 focus:bg-gray-200  dark:focus:bg-slate-600 "
                    />
                </x-form.group>
            @endif
        </x-grid>
        <div class="flex justify-end w-full">
            <x-input.button name="{{$color}}" wire:click.prevent="save">
                {{ __('names.'.$button) }}
            </x-input.button>
        </div>
    </form>
</div>

