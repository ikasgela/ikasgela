<?php

namespace App\Http\Controllers;

use App\File;
use App\FileUpload;
use App\Http\Requests\StoreFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{
    private $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function getFiles()
    {
        return view('tarjetas.ficheros')->with('files', auth()->user()->files);
    }

    public function postUpload(StoreFile $request)
    {
        $fichero = $request->file;

        $filename = md5(time()) . '_' . $fichero->getClientOriginalName();
        $extension = $fichero->getClientOriginalExtension();

        $imagen = Image::make($fichero)->orientate()->stream();
        $thumbnail = Image::make($fichero)->orientate()
            ->resize(128, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->crop(128, 128)
            ->stream();

        Storage::disk('s3')->put('images/' . $filename, $imagen->__toString());
        Storage::disk('s3')->put('thumbnails/' . $filename, $thumbnail->__toString());

        $this->file->create([
            'path' => $filename,
            'title' => $request->file->getClientOriginalName(),
            'size' => $request->file->getClientSize(),
            'user_id' => Auth::user()->id,
            'file_upload_id' => request('file_upload_id')
        ]);

        return back()->with('success', 'File Successfully Saved');
    }

    public function postDelete(File $file)
    {
        // Si es un fichero del usuario o tenemos el rol admin, borrarlo
        if ($file->user()->where('id', Auth::user()->id)->exists() || Auth::user()->hasRole('admin')) {
            Storage::disk('s3')->delete('images/' . $file->path);
            Storage::disk('s3')->delete('thumbnails/' . $file->path);
            $file->delete();
        } else {
            abort(403, __('Sorry, you are not authorized to access this page.'));
        }

        return back()->with('success', 'File Successfully Deleted');
    }

    public function rotate(File $file)
    {
        // Descargar la imagen y la miniatura
        $ruta_imagen = 'images/' . $file->path;
        $ruta_thumbnail = 'thumbnails/' . $file->path;

        $fichero_imagen = Storage::disk('s3')->get($ruta_imagen);
        $fichero_thumbnail = Storage::disk('s3')->get($ruta_thumbnail);

        // Rotar 90º a la derecha
        $imagen = Image::make($fichero_imagen)->rotate(-90)->stream();
        $thumbnail = Image::make($fichero_thumbnail)->rotate(-90)->stream();

        // Volver a subir los ficheros en el mismo path
        Storage::disk('s3')->put($ruta_imagen, $imagen->__toString());
        Storage::disk('s3')->put($ruta_thumbnail, $thumbnail->__toString());

        return back();
    }
}
