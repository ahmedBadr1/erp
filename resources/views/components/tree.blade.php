<div class="mx-2">
    @foreach($tree as $chart)
        <br>
        <x-input.button collapse="1" target="{{$chart->id ?? 'no'}}">
            {{ $chart->id }}
            {{ $chart->name }}
        </x-input.button>
            <div class="hidden" id="{{$chart->id}}">
                @if(!empty($chart->children))
                    <x-tree :tree="$chart->children" ></x-tree>
                @endif
                    <br>
                @forelse($chart->accounts as $char)
                    <div>{{ $char->id }} - {{ $char->name }}</div>
                @empty
                    <div class="">{{ __("No Accounts") }}</div>
                @endforelse
            </div>


    @endforeach


</div>
