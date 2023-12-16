<div class="mx-2 w-full">
    @foreach($tree as $chart)
        <div class="mb-2 w-full  p-1 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
          <div class="flex justify-between">
              <a href="{{ route('admin.accounting.category.show',$chart->slug) }}" class="">
                  {{ $chart->code }}
                  {{ $chart->name }}
              </a>
              <div class="flex">
                  @if($chart->parent_id)
                      <x-input.button wire:click.prevent="duplicateCategory({{$chart->id}})">
                          {{ __('Duplicate') }}
                      </x-input.button>
                  @endif
                  <x-input.button collapse="1" target="{{$chart->code ?? 'no'}}">
                      {{ __('>>') }}
                  </x-input.button>
              </div>
          </div>
            <div class="mx-1 hidden" id="{{$chart->code}}">
                @if(!empty($chart->children))
                    <x-tree :tree="$chart->children" ></x-tree>
                    <br>
                @endif

                @forelse($chart->accounts as $char)
                    <div class="flex">
                        <a href="{{ route('admin.accounting.accounts.show',$char->code) }}" class="">
                            {{ $char->code }} - {{ $char->name }}
                        </a>
                        <x-input.button wire:click.prevent="duplicateAccount({{$char->id}})">
                            {{ __('Duplicate') }}
                        </x-input.button>
                    </div>
                @empty
                    @if($chart->children_count  == 0)
                            <div class="">{{ __("No Accounts") }}</div>
                    @endif
                @endforelse
            </div>

        </div>
    @endforeach
</div>
