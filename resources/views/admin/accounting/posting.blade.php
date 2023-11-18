<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="Posting"/>
    @endsection
        <livewire:posting posting="1" />
</x-app-layout>
