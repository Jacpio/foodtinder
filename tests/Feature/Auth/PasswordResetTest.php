<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('reset password link can be requested via API', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->postJson('/api/forgot-password', ['email' => $user->email]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'We have emailed your password reset link.',
        ]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('password can be reset via API with valid token', function () {
    Notification::fake();

    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $this->postJson('/api/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->postJson('/api/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'Your password has been reset.',
            ]);

        $user->refresh();
        expect(Hash::check('new-password', $user->password))->toBeTrue();

        return true;
    });
});
