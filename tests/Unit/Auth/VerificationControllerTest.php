<?php

namespace Tests\Unit\Auth;

use App\Http\Controllers\Auth\VerificationController;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    public function testConstructorRegistersExpectedMiddlewares()
    {
        $controller = new VerificationController();
        $middlewares = $controller->getMiddleware();

        $this->assertTrue(collect($middlewares)->contains(fn($m) => $m['middleware'] === 'auth'));
        $this->assertTrue(collect($middlewares)->contains(fn($m) => $m['middleware'] === 'signed' && ($m['options']['only'] ?? []) === ['verify']));
        $this->assertTrue(collect($middlewares)->contains(fn($m) => $m['middleware'] === 'throttle:6,1' && ($m['options']['only'] ?? []) === ['verify', 'resend']));
    }
}

