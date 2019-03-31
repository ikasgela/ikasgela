<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $usuarios = User::all()->filter(function ($usuario) {
            return $usuario->hasRole('alumno');
        });

        return view('users.index', compact('usuarios'));
    }
}
