<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\MarkdownText;
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
        $texto = $markdown_text->markdown();
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

    public function actividad(Actividad $actividad)
    {
        $markdown_texts = $actividad->markdown_texts()->get();

        $subset = $markdown_texts->pluck('id')->unique()->flatten()->toArray();
        $disponibles = MarkdownText::whereNotIn('id', $subset)->get();

        return view('markdown_texts.actividad', compact(['markdown_texts', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = MarkdownText::find($recurso_id);
            $actividad->markdown_texts()->attach($recurso);
        }

        return redirect(route('markdown_texts.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, MarkdownText $markdown_text)
    {
        $actividad->markdown_texts()->detach($markdown_text);
        return redirect(route('markdown_texts.actividad', ['actividad' => $actividad->id]));
    }
}
