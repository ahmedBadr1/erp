<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="clients"/>
    @endsection
    <livewire:clients.clients-table />
</x-app-layout>
