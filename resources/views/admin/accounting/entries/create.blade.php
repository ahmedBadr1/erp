<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="entries"/>
    @endsection
    <livewire:entries.entries-form />
</x-app-layout>
