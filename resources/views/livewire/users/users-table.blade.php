<div>
    <x-h h="3" class="text-center">
        {{ __('Users Data') }}
    </x-h>
    <div class="flex justify-between">
        <div class="w-3/4	">
            <x-input.text type="search" wire:model.lazy="search" :placeholder="__('Search')"/>
        </div>

        @if (havePermissionTo('users.create'))

            <x-a  href="{{ route('admin.users.create') }}" >
                <i class='bx bx-plus-circle bx-sm'></i>
                {{ __('message.add', ['model' => __('names.user')]) }}
            </x-a>
        @endif
        <x-input.button collapse="1" target="filter">
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
            <option value="name">{{ __('names.name') }}</option>
            <option value="created_at">{{ __('names.created-at') }}</option>
        </x-input.select>

        <x-input.select wire:model.lazy="orderDesc" label="Order Desc" placeholder="order-desc">
            <option value="1">{{ __('names.desc') }}</option>
            <option value="0">{{ __('names.asc') }}</option>
        </x-input.select>
        <x-input.select wire:model.lazy="perPage" label="Per Page"  placeholder="per-page">
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
                <th scope="col" class="px-6 py-4">{{ __('Name') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Email') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Phone') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Language') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Status') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Setting') }}</th>
            </tr>
        </x-slot>
        @forelse($users as $key => $user)
            <x-tr>
                <x-td medium="true">{{ $user->id }}</x-td>
                <x-td>{{ $user->name }}</x-td>
                <x-td>{{ $user->email }}</x-td>
                <x-td>{{ $user->phone }}</x-td>
                <x-td>{{ __($langs[$user->lang]) }}</x-td>
                <x-td>{{ $user->active }}</x-td>
                <x-td>
                    <div class="limit-2">
                        <a href="{{ route('admin.users.edit',$user->id) }}" class="px-1">
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
                        {{ __('message.empty', ['model' => __('names.users')]) }}
                    </div>
                </x-td>
            </x-tr>
        @endforelse
    </x-table>
    <div class="" dir="ltr">
        {{ $users->links() }}
    </div>
</div>
