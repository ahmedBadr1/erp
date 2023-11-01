<div>
    <x-h h="2">{{ __('message.'.$title,['model' => __('Transaction')]) }}</x-h>
    <form method="POST" action="#" wire:submit.prevent="save" enctype="multipart/form-data">
        @csrf
        <x-grid  >
            <x-form.group name="due" cols="6">
                <x-input.label :value="__('names.due')" :required="true"></x-input.label>
                <x-input.date-picker
                    wire:model.lazy="due"
                    id="due"
                    hidden_element="due"
                />

            </x-form.group>
            <x-form.group name="description" cols="6">
                <x-input.label :value="__('names.description')" :required="true"></x-input.label>
                <x-input.textarea wire:model.lazy="description" :required="false" name="description"
                              placeholder="description"></x-input.textarea>
            </x-form.group>
{{--            <x-form.group name="color" cols="6">--}}
{{--            <x-input.color wire:model.lazy="color"/>--}}
{{--            </x-form.group>--}}

            @foreach($entries as $key => $ent)

                <x-form.group name="entries[{{$key}}]['credit']" cols="2">
                    <x-input.label :value="$ent['credit'] ? __('Credit') : __('Debit')" ></x-input.label>
                                        <x-input.text type="checkbox" wire:model.lazy="entries.{{$key}}.credit" :required="false" value="1"
                                                      name="credit"
                                                      placeholder="{{ __('Credit Limit') }}"></x-input.text>
                </x-form.group>
                <x-form.group name="entries.{{$key}}.account_id" cols="4">
                    <x-input.label :value="__('names.accounts')" :required="true"></x-input.label>
                    <x-input.select wire:model.lazy="entries.{{$key}}.account_id" :required="true" :options="$accounts"
                                    placeholder="{{ __('names.account') }}"></x-input.select>
                </x-form.group>
                <x-form.group name="entries.{{$key}}.amount" cols="4">
                    <x-input.label :value="__('Amount')" :required="true"></x-input.label>
                    <x-input.text type="number" wire:model.lazy="entries.{{$key}}.amount" :required="false"
                                  name="amount"
                                  placeholder="{{ __('Amount') }}"></x-input.text>
                </x-form.group>
                <x-form.group name="entries[{{$key}}]['amount']" cols="2">
                    <x-input.button wire:click.prevent="deleteEntry({{$key}})" color="red" icon="bx bx-x bx-sm" size="sm" >
                    </x-input.button>
                </x-form.group>

            @endforeach
            <x-input.button wire:click.prevent="addEntry()" icon="bx bx-plus"  >
                {{ __("Add Entry") }}
            </x-input.button>
        </x-grid>
        <div class="flex justify-end w-full">
            <x-input.button name="{{$color}}" wire:click.prevent="save">
                {{ __('names.'.$button) }}
            </x-input.button>
        </div>
    </form>
</div>

