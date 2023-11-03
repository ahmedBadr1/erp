<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" current="help" />
    @endsection

        @if (session('status'))
            <x-alert >
                {{ session('status') }}
            </x-alert>
        @endif
    <x-h h="1" >{{ __('Help Center') }}</x-h>

</x-app-layout>
