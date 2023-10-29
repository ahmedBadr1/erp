<div>
    <x-h h="3" class="text-center">
        {{ __('Users Data') }}
    </x-h>
    <div class="flex justify-between">
        <div class="w-3/4	">
            <x-input.text type="search" model="search" :placeholder="__('Search')" />
        </div>

        <div class="col d-flex flex-row-reverse ">
            <x-input.button collapse="1" target="filter">
                <i class='bx bx-filter-alt bx-sm'></i>
                {{ __('names.filter') }}
            </x-input.button>

            @if (havePermissionTo('users.create'))
                <a href="{{ route('admin.users.create') }}">
                    <x-input.button disabled="true">
                        <i class='bx bx-plus-circle bx-sm'></i>
                        {{ __('message.add', ['model' => __('names.user')]) }}
                    </x-input.button>
                </a>
            @endif
        </div>
    </div>

    <div class=" hidden flex justify-between" id="filter" wire:ignore x-data="collapse">

        <x-input.date wire:model="start_date" :label="__('names.date-start')"></x-input.date>

        <x-input.date wire:model.lazy="end_date" :label="__('names.date-end')"></x-input.date>


        <x-input.select wire:model.lazy="status_id" :label="__('names.status')" placeholder="status">
        </x-input.select>


        <x-input.select wire:model.lazy="orderBy" placeholder="order-by">
            <option value="name">{{ __('names.name') }}</option>
            <option value="created_at">{{ __('names.created-at') }}</option>
        </x-input.select>


        <x-input.select wire:model.lazy="orderDesc" placeholder="order-desc">
            <option value="1">{{ __('names.desc') }}</option>
            <option value="0">{{ __('names.asc') }}</option>
        </x-input.select>

        <x-input.select wire:model.lazy="perPage" placeholder="per-page" >
            <option>5</option>
            <option>10</option>
            <option>25</option>
            <option>50</option>
            <option>100</option>
        </x-input.select>

    </div>

    <x-table>
        <x-slot name="thead">
            <tr>
                <th scope="col" class="px-6 py-4">#</th>
                <th scope="col" class="px-6 py-4">{{ __('Name') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Email') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Phone') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Code') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Address') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Status') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Setting') }}</th>
            </tr>
        </x-slot>
        @forelse($clients as $key => $client)
            <x-tr>
                <x-td medium="true">{{ $client->id }}</x-td>
                <x-td>{{ $client->name }}</x-td>
                <x-td>{{ $client->email }}</x-td>
                <x-td>{{ $client->phone }}</x-td>
                <x-td>{{ Str::limit($client->address,40) }}</x-td>
                <x-td>{{ __($client->code) }}</x-td>
                <x-td>{{ $client->active }}</x-td>
                <x-td>
                    <div class="limit-2">
                        <a href="{{ route('admin.clients.edit',$client->id) }}" class="px-1">
                            <x-i name=" bxs-edit" class="text-gray-500"></x-i>
                        </a>
                        <a href="#" class="px-1" wire:click.prevent="delete(1)">
                            <x-i name="bx-trash" class="text-red-800"></x-i>
                        </a>
                    </div>
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
                        {{ __('message.empty', ['model' => __('names.clients')]) }}
                    </div>
                </x-td>
            </x-tr>
        @endforelse
    </x-table>
</div>
