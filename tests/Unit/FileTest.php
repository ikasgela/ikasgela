<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FileTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function files_table_has_expected_columns()
    {
        // Auth
        // Given
        // When
        // Then
        $this->assertTrue(
            Schema::hasColumns('files', [
                'id', 'path', 'title', 'size', 'uploadable_id',
                'user_id', 'created_at', 'updated_at',
            ])
        );
    }

    #[Test]
    public function a_file_belongs_to_a_user()
    {
        // Auth
        // Given
        $user = User::factory()->create();
        $file = File::factory()->create([
            'user_id' => $user->id,
            'uploadable_id' => $user->id,
            'uploadable_type' => 'App\Models\User',
        ]);

        // When
        // Then
        $this->assertEquals($file->uploadable->id, $user->id);
    }
}
