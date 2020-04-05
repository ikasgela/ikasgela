<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\FileUpload;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor');
    }

    public function index()
    {
        $file_uploads = FileUpload::plantilla()->get();

        return view('file_uploads.index', compact('file_uploads'));
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
        $file_upload->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $file_uploads = $actividad->file_uploads()->get();

        $subset = $file_uploads->pluck('id')->unique()->flatten()->toArray();
        $disponibles = FileUpload::where('plantilla', true)->whereNotIn('id', $subset)->get();

        return view('file_uploads.actividad', compact(['file_uploads', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = FileUpload::find($recurso_id);
            $actividad->file_uploads()->attach($recurso);
        }

        return redirect(route('file_uploads.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, FileUpload $file_upload)
    {
        $actividad->file_uploads()->detach($file_upload);
        return redirect(route('file_uploads.actividad', ['actividad' => $actividad->id]));
    }
}
