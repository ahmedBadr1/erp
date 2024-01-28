<?php

test('app redirect to admin login', function () {
$this->get('/')
    ->assertRedirect('/admin/login');
});
