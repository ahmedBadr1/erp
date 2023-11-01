<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" :current="isset($id) ? 'edit-account' : 'create-account'" ></x-breadcrumb>
    @endsection
    <livewire:accounts.accounts-form :id="$id ?? null" />
</x-app-layout>
