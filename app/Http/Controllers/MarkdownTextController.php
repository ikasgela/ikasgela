<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\MarkdownText;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Exception;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MarkdownTextController extends Controller
{
    use PaginarUltima;
    use FiltroCurso;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor|admin');
    }

    public function index(Request $request)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $markdown_texts = $this->filtrar_por_curso($request, MarkdownText::class)->get();

        return view('markdown_texts.index', compact(['markdown_texts', 'cursos']));
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

        $request->merge([
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        MarkdownText::create($request->all());

        return retornar();
    }

    public function show(MarkdownText $markdown_text)
    {
        $texto = $markdown_text->markdown();

        return view('markdown_texts.show', compact(['markdown_text', 'texto']));
    }

    public function edit(MarkdownText $markdown_text)
    {
        try {
            $repositorio = GiteaClient::repo($markdown_text->repositorio);
        } catch (Exception) {
            $repositorio = null;
        }

        return view('markdown_texts.edit', compact(['markdown_text', 'repositorio']));
    }

    public function update(Request $request, MarkdownText $markdown_text)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'repositorio' => 'required',
            'archivo' => 'required',
        ]);

        $markdown_text->update($request->all());

        return retornar();
    }

    public function destroy(MarkdownText $markdown_text)
    {
        $markdown_text->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $markdown_texts = $actividad->markdown_texts()->get();

        $subset = $markdown_texts->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(MarkdownText::where('curso_id', $curso_actual)->whereNotIn('id', $subset));

        return view('markdown_texts.actividad', compact(['markdown_texts', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = MarkdownText::find($recurso_id);
            $actividad->markdown_texts()->attach($recurso, ['orden' => Str::orderedUuid()]);
        }

        return redirect(route('markdown_texts.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, MarkdownText $markdown_text)
    {
        $actividad->markdown_texts()->detach($markdown_text);
        return redirect(route('markdown_texts.actividad', ['actividad' => $actividad->id]));
    }

    public function duplicar(MarkdownText $markdown_text)
    {
        $markdown_text->duplicar(null);

        return redirect(route('markdown_texts.index'));
    }

    public function borrar_cache(MarkdownText $markdown_text)
    {
        $key = $markdown_text->repositorio . '/' . $markdown_text->archivo;
        Cache::forget($key);

        return back();
    }
}
