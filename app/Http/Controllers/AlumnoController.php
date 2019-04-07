<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Tarea;
use App\Unidad;
use App\User;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

}
