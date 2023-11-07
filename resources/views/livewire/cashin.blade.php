<div>

    <x-h h="2">{{$cashOut ? __('Cash Out') : __('Cash In') }}</x-h>
    <form method="POST" action="#" wire:submit.prevent="save" enctype="multipart/form-data">
        @csrf
        <x-grid>
            @if($cashOut)
                <x-form.group name="credit_account" cols="4">
                    <x-input.label :value="__('names.accounts')" :required="true"></x-input.label>
                    <x-input.select wire:model.lazy="credit_account" :required="true" placeholder="{{ __('names.account') }}">
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} - {{$account->code}}</option>
                        @endforeach
                    </x-input.select>
                </x-form.group>

                <x-form.group name="debit_account" cols="4">
                    <x-input.label :value="__('names.accounts')" :required="true"></x-input.label>
                    <x-input.select wire:model.lazy="debit_account" :required="true" placeholder="{{ __('names.account') }}">
                        @foreach($cashAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} - {{$acc->code}}</option>
                        @endforeach
                    </x-input.select>
                </x-form.group>


            @else
                <x-form.group name="credit_account" cols="4">
                    <x-input.label :value="__('names.accounts')" :required="true"></x-input.label>
                    <x-input.select wire:model.lazy="credit_account" :required="true" placeholder="{{ __('names.account') }}">
                        @foreach($cashAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} - {{$acc->code}}</option>
                        @endforeach
                    </x-input.select>
                </x-form.group>

                <x-form.group name="debit_account" cols="4">
                    <x-input.label :value="__('names.accounts')" :required="true"></x-input.label>
                    <x-input.select wire:model.lazy="debit_account" :required="true" placeholder="{{ __('names.account') }}">
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} - {{$account->code}}</option>
                        @endforeach
                    </x-input.select>
                </x-form.group>
            @endif


            <x-form.group name="amount" cols="4">
                <x-input.label :value="__('Amount')" :required="true"></x-input.label>
                <x-input.text type="number" wire:model.lazy="amount" :required="false"
                              name="amount"
                              placeholder="{{ __('Amount') }}"></x-input.text>
            </x-form.group>
            <x-form.group name="description" cols="6">
                <x-input.label :value="__('names.description')" :required="true"></x-input.label>
                <x-input.textarea wire:model.lazy="description" :required="false" name="description"
                                  placeholder="description"></x-input.textarea>
            </x-form.group>
        </x-grid>
        <div class="flex justify-end w-full">
            <x-input.button  wire:click.prevent="save">
                {{ __('names.save') }}
            </x-input.button>
        </div>
    </form>
</div>
