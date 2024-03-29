<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\SafeExam;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SafeExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['config_seb']);
        $this->middleware('role:admin')->except(['config_seb', 'exit_seb']);
    }

    public function index()
    {
        $cursos = Curso::all();

        return view('safe_exam.index', compact('cursos'));
    }

    public function reset_token(Curso $curso)
    {
        $safe_exam = SafeExam::firstOrNew(['curso_id' => $curso->id]);

        $safe_exam->token = bin2hex(openssl_random_pseudo_bytes(8));
        $safe_exam->curso_id = $curso->id;
        $safe_exam->save();

        return back();
    }

    public function delete_token(Curso $curso)
    {
        $safe_exam = SafeExam::firstOrNew(['curso_id' => $curso->id]);

        $safe_exam->token = "";
        $safe_exam->curso_id = $curso->id;
        $safe_exam->save();

        return back();
    }

    public function reset_quit_password(Curso $curso)
    {
        $safe_exam = SafeExam::firstOrNew(['curso_id' => $curso->id]);

        $safe_exam->quit_password = bin2hex(openssl_random_pseudo_bytes(2));
        $safe_exam->curso_id = $curso->id;
        $safe_exam->save();

        return back();
    }

    public function delete_quit_password(Curso $curso)
    {
        $safe_exam = SafeExam::firstOrNew(['curso_id' => $curso->id]);

        $safe_exam->quit_password = "";
        $safe_exam->curso_id = $curso->id;
        $safe_exam->save();

        return back();
    }

    public function config_seb(Curso $curso)
    {
        $ruta = Storage::disk('seb')->path("/");

        $path = $ruta . "/template.xml";
        $xml = file_get_contents($path);

        $xml = Str::replace("IKASGELA_TOKEN", $curso->safe_exam?->token, $xml);
        $xml = Str::replace("IKASGELA_URL", "https://" . request()->getHost(), $xml);
        $xml = Str::replace("IKASGELA_QUIT_PASSWORD", hash("sha256", $curso->safe_exam?->quit_password), $xml);
        $xml = Str::replace("IKASGELA_EXIT_URL", LaravelLocalization::getNonLocalizedURL(route('safe_exam.exit_seb', hash("sha256", $curso->safe_exam?->quit_password))), $xml);

        return response()->streamDownload(function () use ($xml) {
            echo $xml;
        }, 'config.seb');
    }

    public function exit_seb()
    {
        return view('safe_exam.exit');
    }
}
