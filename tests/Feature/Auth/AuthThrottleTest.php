<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Override;
use Tests\TestCase;

class AuthThrottleTest extends TestCase
{
    use DatabaseTransactions;

    private string $email;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();

        // IDs únicos por test: la key del limiter usa email+IP o IP (Redis compartido)
        $this->email = 'throttle-' . uniqid() . '@gmail.com';

        $this->clearLimiterKeys();
    }

    #[Override]
    protected function tearDown(): void
    {
        $this->clearLimiterKeys();

        parent::tearDown();
    }

    private function clearLimiterKeys(): void
    {
        foreach (['127.0.0.1', '::1'] as $ip) {
            RateLimiter::clear(md5('auth-register' . $ip));
            RateLimiter::clear(md5('auth-tinymce-url' . $ip));
        }
    }

    public function testLoginLockoutIsConfigurable()
    {
        config([
            'ikasgela.auth_throttle.login_max_attempts' => 3,
            'ikasgela.auth_throttle.login_decay_minutes' => 1,
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', ['email' => $this->email, 'password' => 'wrong-password']);
        }

        $response = $this->post('/login', ['email' => $this->email, 'password' => 'wrong-password']);

        $this->assertGuest();
        $response->assertInvalid(['email' => 'Too many login attempts']);
    }

    public function testRegisterIsThrottled()
    {
        config([
            'ikasgela.auth_throttle.register_max_attempts' => 3,
            'ikasgela.auth_throttle.register_decay_minutes' => 60,
        ]);

        // Posts con payload inválido: cuentan para el limiter pero no crean usuarios
        for ($i = 0; $i < 3; $i++) {
            $this->post('/register', [
                'name' => 'Test',
                'email' => $this->email,
                'email_confirmation' => $this->email,
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);
        }

        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => $this->email,
            'email_confirmation' => $this->email,
            'password' => 'password12345',
            'password_confirmation' => 'password12345',
        ]);

        $response->assertInvalid(['email' => 'Too many login attempts']);
        // El usuario no debe crearse con el límite agotado
        $this->assertDatabaseMissing('users', ['email' => $this->email]);
    }

    public function testPasswordResetIsThrottled()
    {
        config([
            'ikasgela.auth_throttle.password_reset_max_attempts' => 3,
            'ikasgela.auth_throttle.password_reset_decay_minutes' => 60,
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/password/email', ['email' => $this->email]);
        }

        $response = $this->post('/password/email', ['email' => $this->email]);

        $response->assertInvalid(['email' => 'Too many login attempts']);
    }

    public function testPasswordResetUpdateIsThrottled()
    {
        config([
            'ikasgela.auth_throttle.password_reset_max_attempts' => 3,
            'ikasgela.auth_throttle.password_reset_decay_minutes' => 60,
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/password/reset', [
                'token' => 'fake-token',
                'email' => $this->email,
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);
        }

        $response = $this->post('/password/reset', [
            'token' => 'fake-token',
            'email' => $this->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertInvalid(['email' => 'Too many login attempts']);
    }

    public function testTinymceUrlStaysPublicButIsThrottled()
    {
        Storage::fake('s3-urls');

        config([
            'ikasgela.auth_throttle.tinymce_url_max_attempts' => 3,
            'ikasgela.auth_throttle.tinymce_url_decay_minutes' => 1,
        ]);

        // Sin autenticación (público): dentro del límite → redirect a la URL firmada
        $response = $this->get(route('tinymce.upload.url') . '?path=images/uuid/file.jpg');
        $response->assertRedirect();

        $this->get(route('tinymce.upload.url') . '?path=images/uuid/file.jpg');
        $this->get(route('tinymce.upload.url') . '?path=images/uuid/file.jpg');

        // Cuarto request: límite agotado → 429
        $response = $this->get(route('tinymce.upload.url') . '?path=images/uuid/file.jpg');
        $response->assertStatus(429);
    }

    public function testLoginAfterLockoutCanSucceedAfterWindowExpires()
    {
        config([
            'ikasgela.auth_throttle.login_max_attempts' => 2,
            'ikasgela.auth_throttle.login_decay_minutes' => 1,
        ]);

        // No queremos email desconocido: el lockout debe dispararse tras 2 fallos
        $this->post('/login', ['email' => $this->email, 'password' => 'wrong-password']);
        $this->post('/login', ['email' => $this->email, 'password' => 'wrong-password']);

        $response = $this->post('/login', ['email' => $this->email, 'password' => 'wrong-password']);
        $response->assertInvalid(['email' => 'Too many login attempts']);
    }
}
