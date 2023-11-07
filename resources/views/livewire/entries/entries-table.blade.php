<div>
    <x-h h="3" class="text-center">
        {{ __('Entries Data') }}
    </x-h>
    <div class="flex justify-between">
        <div class="w-3/4	">
            <x-input.text type="search" wire:model.lazy="search" :placeholder="__('Search')"/>
        </div>

        @if (havePermissionTo('accounting.entries.create'))
            <x-a href="{{ route('admin.accounting.entries.create') }}">
                <i class='bx bx-plus-circle bx-sm'></i>
                {{ __('message.add', ['model' => __('Entry')]) }}
            </x-a>
        @endif
        <x-input.button collapse="1" target="filter" color="indigo">
            <i class='bx bx-filter-alt bx-sm'></i>
            {{ __('names.filter') }}
        </x-input.button>


    </div>

    <x-grid cols="6" gap="2" id="filter" wire:ignore x-data="collapse" hidden="1">

        <x-form.group name="due" >
            <x-input.label :value="__('names.start_date')" :required="true"></x-input.label>
            <input wire:model.live="start_date" type="date"
                   class=" w-full px-4 py-2 appearance-none bg-gray-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md active:border-slate-500 dark:active:bg-slate-600 focus:bg-gray-200  dark:focus:bg-slate-600 "
            />
        </x-form.group>
        <x-form.group name="due" >
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
                <th scope="col" class="px-6 py-4">#</th>
                <th scope="col" class="px-6 py-4">{{ __('Type') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Amount') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Description') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Account') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Transaction') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Posting') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Status') }}</th>
            </tr>
        </x-slot>
        @forelse($entries as $key => $entry)
            <x-tr>
                <x-td medium="true">{{ $entry->id }}</x-td>
                <x-td>{{ $entry->credit ? __('Credit') : __('Debit') }}</x-td>
                <x-td> <span title="{{$entry->amount}}">{{ formatMoney($entry->amount) }}</span></x-td>
                <x-td>{{ Str::limit($entry->transaction?->description,40) }}</x-td>
                <x-td>{{ $entry->account?->name }}</x-td>
                <x-td>{{ $entry->transaction?->type }}-{{ $entry->transaction?->id }}</x-td>
                <x-td>{{ $entry->post ? __('Posted') : __('Un Posted') }}</x-td>
                <x-td>{{ $entry->locked ? __('Locked') : __('Un Locked') }}</x-td>
            </x-tr>
        @empty
            <x-tr>
                <x-td colspan="10" medium="true">
                    <div class="flex  justify-center items-center">
                        <img class="" style="height: 100%" src="{{ asset('images/empty.png') }}"
                             alt="">
                    </div>
                    <div class="text-center">
                        {{ __('message.empty', ['model' => __('names.entries')]) }}
                    </div>
                </x-td>
            </x-tr>
        @endforelse
    </x-table>
    <div class="" dir="ltr">
        {{ $entries->links() }}
    </div>

</div>
