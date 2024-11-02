<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\SafeExam;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Pdp\Rules;

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

        $safe_exam->token = SafeExam::new_token();
        $safe_exam->save();

        return back();
    }

    public function delete_token(Curso $curso)
    {
        $safe_exam = SafeExam::firstOrNew(['curso_id' => $curso->id]);

        $safe_exam->token = "";
        $safe_exam->save();

        return back();
    }

    public function reset_quit_password(Curso $curso)
    {
        $safe_exam = SafeExam::firstOrNew(['curso_id' => $curso->id]);

        $safe_exam->quit_password = SafeExam::new_quit_password();
        $safe_exam->save();

        return back();
    }

    public function delete_quit_password(Curso $curso)
    {
        $safe_exam = SafeExam::firstOrNew(['curso_id' => $curso->id]);

        $safe_exam->quit_password = "";
        $safe_exam->save();

        return back();
    }

    public function config_seb(Curso $curso)
    {
        $safe_exam = $curso->safe_exam;

        if (is_null($safe_exam))
            abort('404');

        $ruta = Storage::disk('seb')->path("/");

        $path = $ruta . "/template.xml";
        $xml = file_get_contents($path);

        $xml = Str::replace("IKASGELA_TOKEN", $safe_exam->token, $xml);
        $xml = Str::replace("IKASGELA_URL", "https://" . request()->getHost(), $xml);
        $xml = Str::replace("IKASGELA_QUIT_PASSWORD", hash("sha256", $safe_exam->quit_password), $xml);
        $xml = Str::replace("IKASGELA_EXIT_URL", LaravelLocalization::getNonLocalizedURL(route('safe_exam.exit_seb', hash("sha256", $safe_exam->quit_password))), $xml);

        $xml = Str::replace("SAFE_EXAM_HOST_REGEX", preg_quote($this->get_domain(request()->getHost())), $xml);
        $xml = Str::replace("SAFE_EXAM_HOST", $this->get_domain(request()->getHost()), $xml);

        $xml_allowed_apps = '';
        foreach ($safe_exam->allowed_apps()->get() as $app) {
            if (!$app->disabled) {
                $xml_fragment = file_get_contents($ruta . "/allowed_app_template.xml");
                $xml_fragment = Str::replace("SAFE_EXAM_APP_TITLE", $app->title, $xml_fragment);
                $xml_fragment = Str::replace("SAFE_EXAM_APP_EXECUTABLE", $app->executable, $xml_fragment);
                $xml_fragment = Str::replace("SAFE_EXAM_APP_PATH", $app->path, $xml_fragment);
                $xml_fragment = Str::replace("SAFE_EXAM_APP_ICON", $app->show_icon ? 'true' : 'false', $xml_fragment);
                $xml_fragment = Str::replace("SAFE_EXAM_APP_FORCE_CLOSE", $app->force_close ? 'true' : 'false', $xml_fragment);
                $xml_fragment = Str::replace("SAFE_EXAM_APP_IDENTIFIER", $app->identifier, $xml_fragment);
                $xml_fragment = Str::replace("SAFE_EXAM_APP_OS", $app->os, $xml_fragment); // 1 Windows, 0 macOS

                $xml_allowed_apps .= $xml_fragment;
            }
        }
        $xml = Str::replace("SAFE_EXAM_ALLOWED_APPS", $xml_allowed_apps, $xml);

        $xml_allowed_urls_regex = '';
        foreach ($safe_exam->allowed_urls()->get() as $url) {
            if (!$url->disabled) {

                $protocol = parse_url($url->url, PHP_URL_SCHEME);
                $host = parse_url($url->url, PHP_URL_HOST);
                $path = parse_url($url->url, PHP_URL_PATH);

                $path = Str::replaceStart('/', '', $path);
                $path = Str::replaceEnd('/*', '', $path);

                if (!empty($protocol) && !Str::startsWith($protocol, 'http') && $host == '*') {
                    // app://*
                    $regex_template = '^PROTOCOL:\/\/((((.*?)|(.*?\..*?)))|(((.*?)|(.*?\..*?))\/.*?))(()|(\?.*?))$;';
                    $regex_template = Str::replace("PROTOCOL", $protocol, $regex_template);
                    $xml_allowed_urls_regex .= $regex_template;
                } else if (!empty($host) && !empty($path)) {
                    // https://host/path/*
                    $regex_template = '^.*?:\/\/((HOST)|(.*?\.HOST))\/((PATH.*?)|(PATH.*?))(()|(\?.*?))$;';
                    $regex_template = Str::replace("HOST", preg_quote($host), $regex_template);
                    $regex_template = Str::replace("PATH", preg_quote($path), $regex_template);
                    $xml_allowed_urls_regex .= $regex_template;
                } else if (!empty($host)) {
                    // https://host/*
                    $regex_template = '^.*?:\/\/((((HOST)|(.*?\.HOST)))|(((HOST)|(.*?\.HOST))\/.*?))(()|(\?.*?))$;';
                    $regex_template = Str::replace("HOST", preg_quote($host), $regex_template);
                    $xml_allowed_urls_regex .= $regex_template;
                }
            }
        }
        $xml = Str::replace("SAFE_EXAM_ALLOWED_URLS_REGEX", $xml_allowed_urls_regex, $xml);

        $xml_allowed_urls = '';
        foreach ($safe_exam->allowed_urls()->get() as $url) {
            if (!$url->disabled) {
                $xml_fragment = file_get_contents($ruta . "/allowed_url_filter_template.xml");

                $xml_fragment = Str::replace("SAFE_EXAM_URL", $url->url, $xml_fragment);

                $xml_allowed_urls .= $xml_fragment;
            }
        }
        $xml = Str::replace("SAFE_EXAM_ALLOWED_URLS", $xml_allowed_urls, $xml);

        return response()->streamDownload(function () use ($xml) {
            echo $xml;
        }, 'config.seb');
    }

    public function exit_seb()
    {
        return view('safe_exam.exit');
    }

    public function allowed(SafeExam $safe_exam)
    {
        return view('safe_exam.allowed', compact('safe_exam'));
    }

    public function get_domain($host)
    {
        $ruta = Storage::disk('seb')->path("/");
        $publicSuffixList = Rules::fromPath($ruta . '/public_suffix_list.dat.txt');
        return $publicSuffixList->resolve($host)->registrableDomain()->toString();
    }
}
