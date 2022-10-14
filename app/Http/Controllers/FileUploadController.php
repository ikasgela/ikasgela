<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\FileUpload;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileUploadController extends Controller
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

        $file_uploads = $this->filtrar_por_curso($request, FileUpload::class)->plantilla()->get();

        return view('file_uploads.index', compact(['file_uploads', 'cursos']));
    }

    public function create()
    {
        return view('file_uploads.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'max_files' => 'required',
        ]);

        FileUpload::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'max_files' => $request->input('max_files'),
            'plantilla' => $request->has('plantilla'),
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        return retornar();
    }

    public function show(FileUpload $file_upload)
    {
        return view('file_uploads.show', compact(['file_upload']));
    }

    public function edit(FileUpload $file_upload)
    {
        return view('file_uploads.edit', compact('file_upload'));
    }

    public function update(Request $request, FileUpload $file_upload)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'max_files' => 'required',
        ]);

        $file_upload->update([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'max_files' => $request->input('max_files'),
            'plantilla' => $request->has('plantilla'),
        ]);

        return retornar();
    }

    public function destroy(FileUpload $file_upload)
    {
        $file_upload->delete_with_files();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $file_uploads = $actividad->file_uploads()->get();

        $subset = $file_uploads->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(FileUpload::where('curso_id', $curso_actual)->where('plantilla', true)->whereNotIn('id', $subset));

        return view('file_uploads.actividad', compact(['file_uploads', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = FileUpload::find($recurso_id);
            $actividad->file_uploads()->attach($recurso, ['orden' => Str::orderedUuid()]);
        }

        return redirect(route('file_uploads.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, FileUpload $file_upload)
    {
        $actividad->file_uploads()->detach($file_upload);
        return redirect(route('file_uploads.actividad', ['actividad' => $actividad->id]));
    }

    public function toggle_titulo_visible(Actividad $actividad, FileUpload $file_upload)
    {
        $pivote = $file_upload->pivote($actividad);

        $pivote->titulo_visible = !$pivote->titulo_visible;
        $pivote->save();

        return back();
    }

    public function toggle_descripcion_visible(Actividad $actividad, FileUpload $file_upload)
    {
        $pivote = $file_upload->pivote($actividad);

        $pivote->descripcion_visible = !$pivote->descripcion_visible;
        $pivote->save();

        return back();
    }

    public function duplicar(FileUpload $file_upload)
    {
        $clon = $file_upload->duplicate();
        $clon->titulo = $clon->titulo . " (" . __("Copy") . ')';
        $clon->plantilla = $file_upload->plantilla;
        $clon->save();

        return back();
    }
}
