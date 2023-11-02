<?php

namespace App\Http\Controllers;

use App\Models\Curso;

class SafeExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor');
    }

    public function index()
    {
        $cursos = Curso::all();

        return view('safe_exam.index', compact('cursos'));
    }

    public function reset_token(Curso $curso)
    {
        $curso->token = bin2hex(openssl_random_pseudo_bytes(8));
        $curso->save();

        return back();
    }

    public function delete_token(Curso $curso)
    {
        $curso->token = "";
        $curso->save();

        return back();
    }
}
