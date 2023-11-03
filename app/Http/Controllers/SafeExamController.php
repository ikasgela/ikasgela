<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SafeExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['config_seb']);
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

    public function config_seb(Curso $curso)
    {
        $ruta = Storage::disk('seb')->path("/");

        $path = $ruta . "/template.xml";
        $xml = file_get_contents($path);

        $xml = Str::replace("IKASGELA_TOKEN", $curso->token, $xml);
        $xml = Str::replace("IKASGELA_URL", route('portada'), $xml);

        return response()->streamDownload(function () use ($xml) {
            echo $xml;
        }, 'config.seb');
    }
}
