<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\YoutubeVideo;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class YoutubeVideoController extends Controller
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

        $youtube_videos = $this->filtrar_por_curso($request, YoutubeVideo::class)->get();

        return view('youtube_videos.index', compact(['youtube_videos', 'cursos']));
    }

    public function create()
    {
        return view('youtube_videos.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'codigo' => 'required',
        ]);

        $request->merge([
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        YoutubeVideo::create($request->all());

        return retornar();
    }

    public function show(YoutubeVideo $youtube_video)
    {
        return view('youtube_videos.show', compact('youtube_video'));
    }

    public function edit(YoutubeVideo $youtube_video)
    {
        return view('youtube_videos.edit', compact('youtube_video'));
    }

    public function update(Request $request, YoutubeVideo $youtube_video)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'codigo' => 'required',
        ]);

        $youtube_video->update($request->all());

        return retornar();
    }

    public function destroy(YoutubeVideo $youtube_video)
    {
        $youtube_video->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $youtube_videos = $actividad->youtube_videos()->get();

        $subset = $youtube_videos->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(YoutubeVideo::where('curso_id', $curso_actual)->whereNotIn('id', $subset));

        return view('youtube_videos.actividad', compact(['youtube_videos', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = YoutubeVideo::find($recurso_id);
            $actividad->youtube_videos()->attach($recurso, ['orden' => Str::orderedUuid()]);
        }

        return redirect(route('youtube_videos.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, YoutubeVideo $youtube_video)
    {
        $actividad->youtube_videos()->detach($youtube_video);
        return redirect(route('youtube_videos.actividad', ['actividad' => $actividad->id]));
    }

    public function toggle_titulo_visible(Actividad $actividad, YoutubeVideo $youtube_video)
    {
        $pivote = $youtube_video->pivote($actividad);

        $pivote->titulo_visible = !$pivote->titulo_visible;
        $pivote->save();

        return back();
    }

    public function toggle_descripcion_visible(Actividad $actividad, YoutubeVideo $youtube_video)
    {
        $pivote = $youtube_video->pivote($actividad);

        $pivote->descripcion_visible = !$pivote->descripcion_visible;
        $pivote->save();

        return back();
    }

    public function duplicar(YoutubeVideo $youtube_video)
    {
        $youtube_video->duplicar(null);

        return redirect(route('youtube_videos.index'));
    }
}
