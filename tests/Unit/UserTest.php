<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function users_table_has_expected_columns()
    {
        // Auth
        // Given
        // When
        // Then
        $this->assertTrue(
            Schema::hasColumns('users', [
                'id', 'name', 'email', 'username', 'email_verified_at',
                'password', 'remember_token', 'tutorial', 'created_at', 'updated_at',
                'last_active', 'blocked_date', 'max_simultaneas', 'tags',
            ])
        );
    }

    #[Test]
    public function a_user_has_many_files()
    {
        // Auth
        // Given
        $user = User::factory()->create();
        $file = File::factory()->create([
            'user_id' => $user->id,
            'uploadable_id' => $user->id,
            'uploadable_type' => User::class,
        ]);

        // When
        // Then
        $this->assertTrue($user->files->contains($file));
    }
}
