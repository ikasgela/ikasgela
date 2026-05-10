<?php

namespace Tests\Feature\Auth;

use App\Models\Organization;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Override;
use Tests\TestCase;

class AuthControllersTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
    }

    // --- LoginController::validateLogin ---

    public function testLoginValidatesEmail()
    {
        // POST to login with valid (non-forbidden domain) email triggers validateLogin
        $user = User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@gmail.com',
            'password' => 'password123',
        ]);

        // Will redirect somewhere after login attempt (validateLogin was executed)
        $response->assertRedirect();
    }

    public function testLoginForbiddenDomainFails()
    {
        // Forbidden domain email fails validateLogin validation
        $response = $this->post('/login', [
            'email' => 'test@egibide.org',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // --- ForgotPasswordController::validateEmail ---

    public function testForgotPasswordValidatesEmail()
    {
        // POST to password/email triggers validateEmail
        $response = $this->post('/password/email', [
            'email' => 'nonexistent@gmail.com',
        ]);

        // Even if user doesn't exist, validateEmail was exercised (no domain error)
        // Response is typically a redirect back with status message
        $response->assertRedirect();
    }

    public function testForgotPasswordForbiddenDomainFails()
    {
        $response = $this->post('/password/email', [
            'email' => 'test@egibide.org',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // --- RegisterController::validator (and create) ---

    public function testRegisterValidatorRejectsShortPassword()
    {
        $org = Organization::factory()->create([
            'slug' => 'ikasgela',
            'registration_open' => true,
            'seats' => 10,
        ]);

        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'newuser@example.com',
            'email_confirmation' => 'newuser@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function testRegisterValidatorRejectsForbiddenDomain()
    {
        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'newuser@egibide.org',
            'email_confirmation' => 'newuser@egibide.org',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function testRegisterCreateUser()
    {
        // Mock Gitea HTTP calls since GITEA_ENABLED=true
        Http::fake();

        // Create org + period (test DB starts empty for these)
        $org = Organization::factory()->create([
            'slug' => 'ikasgela',
            'registration_open' => true,
            'seats' => 10,
        ]);
        $period = Period::factory()->create([
            'organization_id' => $org->id,
            'slug' => 'test-period',
        ]);
        $org->update(['current_period_id' => $period->id]);

        // Use a domain that passes email:rfc,dns validation (example.com fails DNS check)
        $email = 'newreguser' . time() . '@gmail.com';

        $this->withoutExceptionHandling();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'surname' => 'Surname',
            'email' => $email,
            'email_confirmation' => $email,
            'password' => 'password12345',
            'password_confirmation' => 'password12345',
        ]);

        // Should redirect after successful registration
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => $email]);
    }
}
