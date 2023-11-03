<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="actions"/>
    @endsection
{{--    <livewire:clients.clients-table />--}}
</x-app-layout>
