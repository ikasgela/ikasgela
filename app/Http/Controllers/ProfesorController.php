<?php

namespace App\Http\Controllers;

use App\Tarea;
use App\User;
use Illuminate\Http\Request;

class ProfesorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
}
