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
                    autocomplete="off"
                    hidden_element="hidden_due"
                />
            </x-form.group>
            <x-form.group name="description" cols="6">
                <x-input.label :value="__('names.description')" :required="true"></x-input.label>
                <x-input.textarea wire:model.lazy="description" :required="false" name="description"
                              placeholder="description"></x-input.textarea>
            </x-form.group>


            <x-form.group name="status">
                <x-input.label :value="__('names.accounts')" :required="true"></x-input.label>
                <x-input.select wire:model.lazy="status_id" :required="true" :options="$accounts"
                                placeholder="{{ __('names.account') }}"></x-input.select>
            </x-form.group>
            <x-form.group name="credit_limit">
                <x-input.label :value="__('Credit Limit')" :required="true"></x-input.label>
                <x-input.text type="number" wire:model.lazy="credit_limit" :required="false"
                              name="credit_limit"
                              placeholder="{{ __('Credit Limit') }}"></x-input.text>
            </x-form.group>
        </x-grid>
        <x-grid cols="3" gap="2" >
            <x-form.group name="address">
                <x-input.label :value="__('Address')" :required="true"></x-input.label>
                <x-input.text type="text" wire:model.lazy="address" :required="true" name="address"
                              placeholder="{{ __('Address') }}"></x-input.text>
            </x-form.group>
            <x-form.group name="phone">
                <x-input.label :value="__('names.phone-number')" :required="true"></x-input.label>
                <x-input.text type="number" wire:model.lazy="phone" :required="true" name="phone"
                              placeholder="{{ __('names.phone-number') }}"></x-input.text>
            </x-form.group>


            <x-form.group name="email">
                <x-input.label :value="__('Email')" :required="false"></x-input.label>
                <x-input.text wire:model.lazy="email" :required="false" name="email"
                              placeholder="{{ __('names.email') }}"></x-input.text>
            </x-form.group>





        </x-grid>
        <div class="">
            <x-input.button name="{{$color}}" wire:click.prevent="save">
                {{ __('names.'.$button) }}
            </x-input.button>
        </div>
    </form>
</div>

