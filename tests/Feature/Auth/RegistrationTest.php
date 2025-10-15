<?php

test('new users can register', function () {
    $response = $this->post('api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'token',
            'user'
        ]);
});
