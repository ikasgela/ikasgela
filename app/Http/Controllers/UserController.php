<?php

namespace App\Http\Controllers;

use App\Tarea;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle_help()
    {
        $user = Auth::user();

        $user->tutorial = !$user->tutorial;
        $user->save();

        session(['tutorial' => $user->tutorial]);

        return back();
    }
}
