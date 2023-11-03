<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="cashin"/>
    @endsection
    <livewire:cashin />
</x-app-layout>
