<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" :current="isset($id) ? 'edit-client' : 'create-client'" ></x-breadcrumb>
    @endsection
    <livewire:clients.clients-form :id="$id ?? null" />
</x-app-layout>
