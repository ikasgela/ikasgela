<?php

namespace App\Http\Controllers;

use App\MarkdownText;
use GitLab;
use Illuminate\Http\Request;

class MarkdownTextController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor');
    }

    public function index()
    {
        $markdown_texts = MarkdownText::all();

        return view('markdown_texts.index', compact('markdown_texts'));
    }

    public function create()
    {
        return view('markdown_texts.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'repositorio' => 'required',
            'archivo' => 'required',
        ]);

        MarkdownText::create($request->all());

        return redirect(route('markdown_texts.index'));
    }

    public function show(MarkdownText $markdown_text)
    {
        try {
            $repositorio = $markdown_text->repositorio;
            $rama = isset($markdown_text->rama) ? $markdown_text->rama : 'master';
            $archivo = $markdown_text->archivo;
            $servidor = config('app.debug') ? 'https://gitlab.ikasgela.test/' : 'https://gitlab.ikasgela.com/';

            $proyecto = GitLab::projects()->show($repositorio);

            $texto = GitLab::repositoryfiles()->getRawFile($proyecto['id'], $archivo, $rama);

            // Imagen
            $texto = preg_replace('/(!\[.*\]\((?!http))/', '${1}' . $servidor
                . $repositorio
                . "/raw/$rama//"
                , $texto);

            // Link
            $texto = preg_replace('/(\s+\[.*\]\((?!http))/', '${1}' . $servidor
                . $repositorio
                . "/blob/$rama//"
                , $texto);

        } catch (\Exception $e) {
            $texto = "# " . __('Error') . "\n\n" . __('Repository not found.');
        }

        return view('markdown_texts.show', compact(['markdown_text', 'texto']));
    }

    public function edit(MarkdownText $markdown_text)
    {
        return view('markdown_texts.edit', compact('markdown_text'));
    }

    public function update(Request $request, MarkdownText $markdown_text)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'repositorio' => 'required',
            'archivo' => 'required',
        ]);

        $markdown_text->update($request->all());

        return redirect(route('markdown_texts.index'));
    }

    public function destroy(MarkdownText $markdown_text)
    {
        $markdown_text->delete();

        return redirect(route('markdown_texts.index'));
    }
}
