@props([
    'class' => '',
    'tree' => [url('/admin')=>'dashboard'],
    'current' => '',
     'name' => ''

])

<nav class="w-full rounded-md">
    <ol class="list-reset flex">
        @foreach($tree as $href => $title)
            <li class="text-primary transition duration-150 ease-in-out hover:text-primary-600 focus:text-primary-600 active:text-primary-700 dark:text-primary-400 dark:hover:text-primary-500 dark:focus:text-primary-500 dark:active:text-primary-600">
                <a href="{{$href}}">{{__('names.'.$title)}}</a>
            </li>
            <li>
                <span class="mx-2 text-neutral-500 dark:text-neutral-400">/</span>
            </li>
        @endforeach
        <li class="text-neutral-500 dark:text-neutral-400" aria-current="page">
            @if(!empty($current))
                {{__('names.'.$current)}}
            @endif @if(!empty($name))
                {{$name}}
            @endif </li>
    </ol>
</nav>
