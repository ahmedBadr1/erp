<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="profile" />
    @endsection

    @if (session('status'))
        <x-alert >
            {{ session('status') }}
        </x-alert>
    @endif
    <x-h h="1" >{{ __('User Profile') }}</x-h>

</x-app-layout>
