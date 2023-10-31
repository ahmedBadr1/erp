<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="accounts"/>
    @endsection
    <livewire:users.users-table />
</x-app-layout>
