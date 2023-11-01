<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" :current="isset($id) ? 'edit-transaction' : 'create-transaction'" ></x-breadcrumb>
    @endsection
    <livewire:entries.entries-form :id="$id ?? null" />
</x-app-layout>
