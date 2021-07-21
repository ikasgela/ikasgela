<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileResource;
use App\Models\FileUpload;
use App\Http\Requests\StoreFile;
use App\Http\Requests\StoreImage;
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
        return view('pruebas.ficheros')->with('files', auth()->user()->files);
    }

    public function imageUpload(StoreImage $request)
    {
        $fichero = $request->file;

        $filename = md5(time()) . '_' . $fichero->getClientOriginalName();
        $extension = $fichero->getClientOriginalExtension();

        $imagen = Image::make($fichero)
            ->orientate()
            ->resize(2000, 2000, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 80)
            ->stream();

        $thumbnail = Image::make($fichero)
            ->orientate()
            ->resize(128, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->crop(128, 128)
            ->encode('jpg', 80)
            ->stream();

        Storage::disk('s3')->put('images/' . $filename, $imagen->__toString());
        Storage::disk('s3')->put('thumbnails/' . $filename, $thumbnail->__toString());

        $file_upload = FileUpload::find(request('file_upload_id'));

        $file_upload->files()->create([
            'path' => $filename,
            'title' => $request->file->getClientOriginalName(),
            'size' => Storage::disk('s3')->size('images/' . $filename),
            'user_id' => Auth::user()->id,
            'file_upload_id' => request('file_upload_id')
        ]);

        return back()->with('success', 'File Successfully Saved');
    }

    public function documentUpload(StoreFile $request)
    {
        $fichero = $request->file;

        $filename = md5(time()) . '/' . $fichero->getClientOriginalName();
        $extension = $fichero->getClientOriginalExtension();

        Storage::disk('s3')->put('documents/' . $filename, file_get_contents($fichero));

        $file_resource = FileResource::find(request('file_resource_id'));

        $file_resource->files()->create([
            'path' => $filename,
            'title' => $request->file->getClientOriginalName(),
            'description' => request('description'),
            'size' => $request->file->getSize(),
            'user_id' => Auth::user()->id,
        ]);

        return back()->with('success', 'File Successfully Saved');
    }

    public function postDelete(File $file)
    {
        // Si es un fichero del usuario o tenemos el rol admin, borrarlo
        if ($file->user()->where('id', Auth::user()->id)->exists() || Auth::user()->hasRole('admin')) {
            Storage::disk('s3')->delete('images/' . $file->path);
            Storage::disk('s3')->delete('thumbnails/' . $file->path);
            Storage::disk('s3')->delete('documents/' . $file->path);
            $file->delete();
        } else {
            abort(403, __('Sorry, you are not authorized to access this page.'));
        }

        return back()->with('success', 'File Successfully Deleted');
    }

    public function rotateLeft(File $file)
    {
        $this->rotate($file, true);
        return back();
    }

    public function rotateRight(File $file)
    {
        $this->rotate($file, false);
        return back();
    }

    private function rotate(File $file, bool $left = false)
    {
        // Descargar la imagen y la miniatura
        $ruta_imagen = 'images/' . $file->path;
        $ruta_thumbnail = 'thumbnails/' . $file->path;

        $fichero_imagen = Storage::disk('s3')->get($ruta_imagen);
        $fichero_thumbnail = Storage::disk('s3')->get($ruta_thumbnail);

        // Rotar 90º a derecha o izquierda
        if (!$left) {
            $imagen = Image::make($fichero_imagen)->rotate(-90)->stream();
            $thumbnail = Image::make($fichero_thumbnail)->rotate(-90)->stream();
        } else {
            $imagen = Image::make($fichero_imagen)->rotate(90)->stream();
            $thumbnail = Image::make($fichero_thumbnail)->rotate(90)->stream();
        }

        // Volver a subir los ficheros en el mismo path
        Storage::disk('s3')->put($ruta_imagen, $imagen->__toString());
        Storage::disk('s3')->put($ruta_thumbnail, $thumbnail->__toString());
    }
}
