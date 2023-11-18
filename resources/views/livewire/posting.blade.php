<div>
    <x-h h="3" class="text-center">
        {{ __('Posting') }}
    </x-h>
    <div class="flex justify-between">
        <div class="w-3/4	">
            <x-input.text type="search" wire:model.lazy="search" :placeholder="__('Search')"/>
        </div>

        @if (havePermissionTo('accounting.entries.create'))
            <x-a href="#" wire:click.prevent="save" >
                {{ __('Posting') }}
                <i class='bx bx-send bx-sm bx-rotate-180'></i>
            </x-a>
        @endif
        <x-input.button collapse="1" target="filter" color="indigo">
            <i class='bx bx-filter-alt bx-sm'></i>
            {{ __('names.filter') }}
        </x-input.button>


    </div>

    <x-grid cols="6" gap="2" id="filter" wire:ignore x-data="collapse" hidden="1">

        <x-form.group name="due">
            <x-input.label :value="__('names.start_date')" :required="true"></x-input.label>
            <input wire:model.live="start_date" type="date"
                   class=" w-full px-4 py-2 appearance-none bg-gray-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md active:border-slate-500 dark:active:bg-slate-600 focus:bg-gray-200  dark:focus:bg-slate-600 "
            />
        </x-form.group>
        <x-form.group name="due">
            <x-input.label :value="__('names.end_date')" :required="true"></x-input.label>
            <input wire:model.lazy="end_date" type="date"
                   class=" w-full px-4 py-2 appearance-none bg-gray-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md active:border-slate-500 dark:active:bg-slate-600 focus:bg-gray-200  dark:focus:bg-slate-600 "
            />
        </x-form.group>

        <x-input.select wire:model.lazy="status_id" label="Status" placeholder="status">
        </x-input.select>

        <x-input.select wire:model.lazy="orderBy" label="Order By" placeholder="order-by">
            <option value="description">{{ __('names.description') }}</option>
            <option value="amount">{{ __('names.amount') }}</option>
            <option value="credit">{{ __('names.credit') }}</option>
            <option value="created_at">{{ __('names.created-at') }}</option>
        </x-input.select>

        <x-input.select wire:model.lazy="orderDesc" label="Order Desc" placeholder="order-desc">
            <option value="1">{{ __('names.desc') }}</option>
            <option value="0">{{ __('names.asc') }}</option>
        </x-input.select>
        <x-input.select wire:model.lazy="perPage" label="Per Page" placeholder="per-page">
            <option>5</option>
            <option>10</option>
            <option>25</option>
            <option>50</option>
            <option>100</option>
        </x-input.select>
    </x-grid>

    <x-table>
        <x-slot name="thead">
            <tr>
                <th scope="col" class="px-6 py-4 text-center">#</th>
                <th scope="col" class="px-6 py-4 text-center">{{ __('Amount') }}</th>
                <th scope="col" class="px-6 py-4 text-center">{{ __('Description') }}</th>
                <th scope="col" class="px-6 py-4 text-center">{{ __('Entries') }}</th>
                <th scope="col" class="px-6 py-4 text-center">{{ __('Type') }}</th>
                <th scope="col" class="px-6 py-4 text-center">{{ __('Posting') }}</th>
                <th scope="col" class="px-6 py-4 text-center">{{ __('Status') }}</th>
                <th scope="col" class="px-6 py-4 text-center">{{ __('Details') }}</th>
                <th scope="col" class="px-6 py-4 text-center">{{ __('Check') }}</th>
            </tr>
        </x-slot>
        @forelse($transactions as $key => $transaction)
            <x-tr>
                <x-td medium="true">{{ $transaction->id }}</x-td>
                <x-td><span title="{{$transaction->amount}}">{{ formatMoney($transaction->amount) }}</span></x-td>
                <x-td>{{ Str::limit($transaction->description,40) }}</x-td>
                <x-td>{{ $transaction->type }}-{{ $transaction?->id }}</x-td>
                <x-td>{{ $transaction->post ? __('Posted') : __('Un Posted') }}</x-td>
                <x-td>{{ $transaction->locked ? __('Locked') : __('Un Locked') }}</x-td>
                <x-td >
                    <x-input.button collapse="1" target="{{$transaction->id}}" color="rose" icon="bx bx-chevrons-left"></x-input.button>
                </x-td>
                <x-td >
                    <table class="hidden" id="{{$transaction->id}}">
                        <thead>
                        <tr>
                            <th scope="col" class="px-6 py-4 text-center">{{ __('Account') }}</th>
                            <th scope="col" class="px-6 py-4 text-center">{{ __('Amount') }}</th>
                            <th scope="col" class="px-6 py-4 text-center">{{ __('Credit') }}</th>
                        </tr>
                        </thead>
                       <tbody>
                       @foreach($transaction->entries as $entry)
                           <tr>
                               <td>{{ $entry->account?->name }}</td>
                               <td>{{ $entry->amount }}</td>
                               <td>{{ $entry->credit ? __('Credit') : __('Debit') }}</td>
                           </tr>
                       @endforeach
                       </tbody>
                    </table>
                </x-td>
                <x-td >
                    <input type="checkbox" name="post[]" wire:model.live="checks.{{$transaction->id}}" value="1" >
                </x-td>
            </x-tr>

        @empty
            <x-tr>
                <x-td colspan="10" medium="true">
                    <div class="flex  justify-center items-center">
                        <img class="" style="height: 100%" src="{{ asset('images/empty.png') }}"
                             alt="">
                    </div>
                    <div class="text-center">
                        {{ __('message.empty', ['model' => __('names.transactions')]) }}
                    </div>
                </x-td>
            </x-tr>
        @endforelse
    </x-table>
    <div class="" dir="ltr">
        {{ $transactions->links() }}
    </div>

</div>
