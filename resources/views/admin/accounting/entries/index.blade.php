<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="entries"/>
    @endsection
        <livewire:entries.entries-table />
</x-app-layout>
