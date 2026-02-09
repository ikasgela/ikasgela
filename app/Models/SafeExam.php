<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSafeExam
 */
class SafeExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'token', 'quit_password', 'curso_id',
        'full_screen', 'show_toolbar',
        '__import_id',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function allowed_apps()
    {
        return $this->hasMany(AllowedApp::class);
    }

    public function allowed_urls()
    {
        return $this->hasMany(AllowedUrl::class);
    }

    public static function new_token()
    {
        return bin2hex(openssl_random_pseudo_bytes(config('safe_exam.token_bytes')));
    }

    public static function new_quit_password()
    {
        return bin2hex(openssl_random_pseudo_bytes(config('safe_exam.quit_password_bytes')));
    }
}
