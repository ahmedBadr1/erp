<x-app-layout>
    @section('breadcrumb')
        <x-breadcrumb :tree="$tree" />
    @endsection

        @if (session('status'))
            <x-alert >
                {{ session('status') }}
            </x-alert>
        @endif
        <x-alert type="success">
            {{ __('You are logged in!') }}
        </x-alert>
        <x-dashboard.dashboard-avatars  ></x-dashboard.dashboard-avatars>
        <x-dashboard.welcome-banner dataFeed="[1,3,5,8,1,5]" ></x-dashboard.welcome-banner>

        <x-grid cols="12" gap="6" >
            <x-dashboard.dashboard-card-01  ></x-dashboard.dashboard-card-01>
            <x-dashboard.dashboard-card-02  ></x-dashboard.dashboard-card-02>
            <x-dashboard.dashboard-card-03  ></x-dashboard.dashboard-card-03>
            <x-dashboard.dashboard-card-12  ></x-dashboard.dashboard-card-12>
            <x-dashboard.dashboard-card-13  ></x-dashboard.dashboard-card-13>
            <x-dashboard.dashboard-card-11  ></x-dashboard.dashboard-card-11>
            <x-dashboard.dashboard-card-10  ></x-dashboard.dashboard-card-10>
            <x-dashboard.dashboard-card-04  ></x-dashboard.dashboard-card-04>
            <x-dashboard.dashboard-card-05  ></x-dashboard.dashboard-card-05>
            <x-dashboard.dashboard-card-06  ></x-dashboard.dashboard-card-06>
            <x-dashboard.dashboard-card-07  ></x-dashboard.dashboard-card-07>
            <x-dashboard.dashboard-card-08  ></x-dashboard.dashboard-card-08>
            <x-dashboard.dashboard-card-09  ></x-dashboard.dashboard-card-09>


        </x-grid>



</x-app-layout>
