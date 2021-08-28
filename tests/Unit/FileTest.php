<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FileTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function files_table_has_expected_columns()
    {
        // Auth
        // Given
        // When
        // Then
        $this->assertTrue(
            Schema::hasColumns('files', [
                'id', 'path', 'title', 'size', 'file_upload_id',
                'user_id', 'created_at', 'updated_at',
            ])
        );
    }

    /** @test */
    public function a_file_belongs_to_a_user()
    {
        // Auth
        // Given
        $user = User::factory()->create();
        $file = File::factory()->create([
            'user_id' => $user->id,
            'file_upload_id' => $user->id,
            'file_upload_type' => 'App\Models\User',
        ]);

        // When
        // Then
        $this->assertEquals($file->file_upload->id, $user->id);
    }
}
