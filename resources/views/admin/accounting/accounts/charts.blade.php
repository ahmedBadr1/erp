<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="charts"/>
    @endsection
    <livewire:accounts.charts />
</x-app-layout>
