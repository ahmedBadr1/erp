<div>
    <x-h h="3">
        {{ __('Users Data') }}
    </x-h>
    <div class="flex justify-between" >
        <div class="">
            <x-input.search />
            <!-- <x-input.float model="search"  :label="__('Search')"></x-input.float> -->
        </div>

        <div class="col d-flex flex-row-reverse ">
            <x-input.button collapse="1" target="filter">
                <i class='bx bx-filter-alt bx-sm'></i>
                {{ __('names.filter') }}
            </x-input.button>

            @if (havePermissionTo('users.create'))
                <a href="{{ route('admin.users.create') }}"  >
                    <x-input.button disabled="true">
                        <i class='bx bx-plus-circle bx-sm'></i>
                        {{ __('message.add', ['model' => __('names.user')]) }}
                    </x-input.button>
                </a>
            @endif
        </div>
    </div>

    <div class="!visible hidden flex justify-between" id="filter" data-te-collapse-item  wire:ignore>
        <div class="">
            <x-input.date :model="'start_date'" :label="__('names.date-start')"></x-input.date>
        </div>
        <div class="">
            <x-input.label :value="__('names.date-end')"></x-input.label>
            <input type="date" wire:model="end_date" class="form-control"/>
        </div>

        <div class="">
            <x-input.label :value="__('names.status')"></x-input.label>
            <select wire:model="status_id" class="form-select">
                <option value="">{{ __('names.all') }}</option>
            </select>
        </div>
        <div class="">
            <x-input.label :value="__('names.order-by')"></x-input.label>
            <select wire:model="orderBy" class="form-select">
                <option value="name">{{ __('names.name') }}</option>
                <option value="created_at">{{ __('names.created-at') }}</option>
            </select>
        </div>
        <div class="">
            <x-input.label :value="__('names.order-desc')"></x-input.label>
            <select wire:model="orderDesc" class="form-select">
                <option value="1">{{ __('names.desc') }}</option>
                <option value="0">{{ __('names.asc') }}</option>
            </select>
        </div>
        <div class="">
            <x-input.label :value="__('names.per-page')"></x-input.label>
            <select wire:model="perPage" class="form-select">
                <option>5</option>
                <option>10</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
    </div>

    <x-table >
        <x-slot name="thead">
            <tr>
                <th scope="col" class="px-6 py-4">#</th>
                <th scope="col" class="px-6 py-4">{{ __('Name') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Email') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Phone') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Language') }}</th>
                <th scope="col" class="px-6 py-4">{{ __('Status') }}</th>
            </tr>
        </x-slot>
        @forelse($users as $key => $user)
            <x-tr >
                <x-td medium="true">{{ $key }}</x-td>
                <x-td >{{ $user->name }}</x-td>
                <x-td >{{ $user->email }}</x-td>
                <x-td >{{ $user->phone }}</x-td>
                <x-td >{{ __($langs[$user->lang]) }}</x-td>
                <x-td >{{ $user->active }}</x-td>
            </x-tr>
        @empty
            <x-tr >
                <x-td colspan="10" medium="true" >
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
</div>
