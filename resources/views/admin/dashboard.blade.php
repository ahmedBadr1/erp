<x-app-layout>
    <div >
        @if (session('status'))
            <x-alert >
                {{ session('status') }}
            </x-alert>
        @endif
        <x-alert type="success">
            {{ __('You are logged in!') }}
        </x-alert>
        <div class="mx-auto w-3/5 overflow-hidden">
            <canvas
                data-te-chart="bar"
                data-te-dataset-label="Traffic"
                data-te-labels="['Monday', 'Tuesday' , 'Wednesday' , 'Thursday' , 'Friday' , 'Saturday' , 'Sunday']"
                data-te-dataset-data="[2112, 2343, 2545, 3423, 2365, 1985, 987]">
            </canvas>
        </div>
    </div>

</x-app-layout>
