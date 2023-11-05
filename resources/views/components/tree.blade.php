<div class="mx-2">
    @foreach($tree as $chart)
        <br>
        <x-input.button collapse="1" target="{{$chart->code ?? 'no'}}">
            {{ $chart->code }}
            {{ $chart->name }}
        </x-input.button>
            <div class="hidden" id="{{$chart->code}}">
                @if(!empty($chart->children))
                    <x-tree :tree="$chart->children" ></x-tree>
                @endif
                    <br>
                @forelse($chart->accounts as $char)
                    <div>{{ $char->code }} - {{ $char->name }}</div>
                @empty
                    @if($chart->ancestors_count  == 2)
                            <div class="">{{ __("No Accounts") }}</div>
                    @endif
                @endforelse
            </div>


    @endforeach


</div>
