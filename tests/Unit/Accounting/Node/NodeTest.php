<?php

use App\Models\User;

it('adds Code when creating Node',function (){
   $node = \App\Models\Accounting\Node::factory()->create();
//    dd(User::all());
   expect($node->code)->not()->toBeNull();
});
