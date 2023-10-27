<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="usersgit"/>
    @endsection
    <livewire:users.users-table />
</x-app-layout>
