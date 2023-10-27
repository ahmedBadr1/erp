<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="roles"/>
    @endsection
    <div >
        <livewire:roles.roles-table />
    </div>
</x-app-layout>
