<?php

test('guests are redirected from the home route to login', function () {
    $this->get('/')
        ->assertRedirect('/login');
});
