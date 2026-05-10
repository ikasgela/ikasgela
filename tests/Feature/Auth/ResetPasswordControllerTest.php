<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testResetPassword()
    {
        config(['ikasgela.gitea_enabled' => false]);

        // Create a user
        $user = User::factory()->create([
            'email' => 'resettest@gmail.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        // Create a valid password reset token in the DB
        $token = Str::random(60);
        \DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        Event::fake([PasswordReset::class]);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/home');

        // Verify password was changed
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function testResetPasswordConstructorMiddleware()
    {
        // Verify guest middleware is applied by checking that authenticated users are redirected
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/password/reset/sometoken');
        $response->assertRedirect('/home');
    }
}
