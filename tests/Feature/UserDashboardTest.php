<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected from home to login', function () {
    $this->get('/')
        ->assertRedirect('/login');
});

test('user dashboard page loads', function () {
    $user = User::factory()->create([
        'location' => 'Yangon',
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Marketly');
});

test('authenticated users are sent to the user dashboard', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'location' => 'Yangon',
    ]);

    $response = $this->post('/login', [
        'email' => 'user@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('authenticated users can log out from the dashboard', function () {
    $user = User::factory()->create([
        'location' => 'Yangon',
    ]);

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/login');

    $this->assertGuest();
});
