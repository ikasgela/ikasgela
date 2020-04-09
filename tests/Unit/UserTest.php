<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function users_table_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'id', 'name', 'email', 'username', 'email_verified_at',
                'password', 'remember_token', 'tutorial', 'created_at', 'updated_at',
                'last_active', 'blocked_date', 'max_simultaneas', 'tags',
            ])
        );
    }
}
