<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the root redirects to the Fortify login page', function () {
    $this->get('/')
        ->assertRedirect('/login');

    $this->get('/login')
        ->assertOk()
        ->assertSee('Entrar');

    $this->get('/register')
        ->assertOk()
        ->assertSee('Registrar Novo Usuário');
});

test('guests cannot access the home page', function () {
    $this->get('/home')
        ->assertRedirect('/login');
});

test('a user can log in and see the home dashboard', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'remember' => true,
    ])->assertRedirect('/home');

    $this->assertAuthenticatedAs($user);

    $this->get('/home')
        ->assertOk()
        ->assertSee('Resumo de '.now()->year)
        ->assertSee($user->name);
});
