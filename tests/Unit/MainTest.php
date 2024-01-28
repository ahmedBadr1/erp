<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class MainTest extends TestCase
{

    public function test_login_return_required()
    {
        $httpRequest = $this->json('POST', '/api/login',[],['Accept'=>'application/json']);
        $httpRequest->assertStatus(422);

//        $httpRequest->seeJson();
    }


}
