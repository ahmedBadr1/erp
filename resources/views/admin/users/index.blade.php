<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="users"/>
    @endsection
    <livewire:users.users-table />
</x-app-layout>
