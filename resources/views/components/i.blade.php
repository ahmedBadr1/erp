@props([
    'name' => '' , // visit https://boxicons.com/?query=
        'class' => '' , // Any Css Class
    'size' =>'sm' , // xs|sm|md|lg|cssSize
   'type'=> 'regular' ,//regular|solid|logo
   'rotate' => null , // 90|180|270
   'border' => null , //bx-border bx-border-circle
    'animate' => null , // bx-spin,bx-tada,bx-flashing,bx-burst,bx-fade-left and bx-fade-right
     // Hover Animate bx-spin-hover,bx-tada-hover,bx-flashing-hover,bx-burst-hover,bx-fade-left-hover and bx-fade-right-hover
    ])

<i class='bx {{ $name }}  {{$class}}
@if($size) bx-{{$size}} @endif
@if($rotate)  bx-rotate-{{$rotate}} @endif
@if($border) bx-{{$border}} @endif
@if($animate) bx-{{$animate}} @endif'></i>
