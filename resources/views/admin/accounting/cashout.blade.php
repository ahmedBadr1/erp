<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="cashin"/>
    @endsection
    <livewire:accounts.accounts-table />
</x-app-layout>
