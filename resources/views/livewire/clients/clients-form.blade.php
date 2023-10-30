<div>
    <x-h h="2">{{ __('message.'.$title,['model' => __('names.client')]) }}</x-h>
    <form method="POST" action="#" wire:submit.prevent="save" enctype="multipart/form-data">
        @csrf
        <x-grid cols="4" gap="4" >
            <x-form.group name="name">
                <x-input.label :value="__('names.client-name')" :required="true"></x-input.label>
                <x-input.text wire:model.lazy="name" :required="false" name="name"
                              placeholder="status"></x-input.text>
            </x-form.group>

            <x-form.group name="code">
                <x-input.label :value="__('names.code')" :required="true"></x-input.label>
                <x-input.text type="text" wire:model.lazy="code" :required="false"
                              name="code"
                              placeholder="{{ __('names.code') }}"></x-input.text>
            </x-form.group>
            <x-form.group name="status">
                <x-input.label :value="__('names.status')" :required="true"></x-input.label>
                <x-input.select wire:model.lazy="status_id" :required="true" :options="$statuses"
                              placeholder="{{ __('names.status') }}"></x-input.select>
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


            <x-form.group name="image">
                <x-input.label :value="__('names.image')" :required="false"></x-input.label>
                <x-input.file wire:model="image" name="image"/>
                @if ($client && $image_path)
                    Photo Preview:
                    <img width="400" src="{{ asset('storage/'.$image_path) }}">
                @endif
            </x-form.group>


        </x-grid>
        <div class="">
            <x-input.button name="{{$color}}" wire:click.prevent="save">
                {{ __('names.'.$button) }}
            </x-input.button>
        </div>
    </form>
</div>

