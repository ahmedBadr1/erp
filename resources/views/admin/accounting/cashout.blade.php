<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="cashout"/>
    @endsection
        <livewire:cashin cashOut="1" />
</x-app-layout>
