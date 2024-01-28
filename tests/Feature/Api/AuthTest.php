<?php

//\Database\Seeders\ConstantSeeder::seedUsers();


use App\Models\User;

describe('Login Features', function () {

    $credentials = [
        'email' => 'admin@erp.com',
        'password' => 'password',
    ];

    it("can't login without credentials", function () {
        $this->post('/api/login', [], ['Accept' => 'application/json'])
            ->assertStatus(422);
    });

    it('send error for Wrong password', function ()  {
        $this->post('/api/login', ['email' => 'admin@erp.com' ,'password'=>'1234'], ['Accept' => 'application/json'])
//            ->assertSimilarJson('The Password field is required')
            ->assertStatus(422);
    });

    it('return suspended if user not active', function ()  use ($credentials)  {
        $user = User::find(1);

        $user->update(['active'=>0]);

        $this->post('/api/login',$credentials, ['Accept' => 'application/json'])
            ->assertStatus(409);
    });

    it('can Login', function () use ($credentials) {
        $user = User::find(1);
        $this->post('/api/login', $credentials)
            ->assertStatus(200);
    });


    it('Only One User Can Login', function () use ($credentials) {
        $user = User::find(1);
        $this->post('/api/login', $credentials);
        $this->post('/api/login', $credentials);
        expect($user->tokens()->count())->toEqual(1);
    });

});



describe('Register Features', function () {

    $credentials = [
        'name' => 'ahmed',
        'username' => 'ahmed',
        'email' => 'ahmed@erp.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    it("can't register without invitations", function () use ($credentials) {
        $this->post('/api/register', $credentials)
            ->assertStatus(404);
    });

    it("can't register with used email", function () use ($credentials) {
        $credentials['email'] = 'admin@erp.com' ;
        $this->post('/api/register', $credentials)
        ->assertStatus(422);

//        ->assertJson(['data' => [], 'message' => 'Account Created Successfully']);

    });

    it("can only register with invitation", function () use ($credentials) {
        \App\Models\System\Invitation::factory()->create([
            'email'=>$credentials['email']
        ]);
        $this->post('/api/register', $credentials)
            ->assertStatus(200)
        ->assertJson(['data' => [], 'message' => 'Account Created Successfully']);

    });

});
