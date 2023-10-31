<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="accounts"/>
    @endsection
    <livewire:accounts.accounts-table />
</x-app-layout>
