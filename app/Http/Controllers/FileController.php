<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFile;
use App\Http\Requests\StoreImage;
use App\Models\File;
use App\Models\FileResource;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class FileController extends Controller
{
    public function getFiles()
    {
        return view('pruebas.ficheros')->with('files', auth()->user()->files);
    }

    public function imageUpload(StoreImage $request)
    {
        $fichero = $request->file;

        $filename = md5(time()) . '/' . $fichero->getClientOriginalName();

        $imagen = Image::read($fichero)
            ->scaleDown(2000, 2000)
            ->encode(new WebpEncoder(quality: 80));

        $thumbnail = Image::read($fichero)
            ->coverDown(128, 128)
            ->encode(new WebpEncoder(quality: 80));

        Storage::disk('s3')->put('images/' . $filename, $imagen);
        Storage::disk('s3')->put('thumbnails/' . $filename, $thumbnail);

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

        Storage::disk('s3')->put('documents/' . $filename, file_get_contents($fichero));

        $file_resource = FileResource::find(request('file_resource_id'));

        $file = File::create([
            'path' => $filename,
            'title' => $fichero->getClientOriginalName(),
            'extension' => $fichero->extension(),
            'description' => request('description'),
            'size' => $fichero->getSize(),
            'user_id' => Auth::user()->id,
        ]);

        $file->orden = $file->id;

        $file_resource->files()->save($file);

        return back()->with('success', 'File Successfully Saved');
    }

    public function postDelete(File $file)
    {
        // Si es un fichero del usuario o tenemos el rol admin, borrarlo
        if ($file->user()->where('id', Auth::user()->id)->exists() || Auth::user()->hasRole('admin')) {
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
        $this->rotate($file);
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
            $imagen = Image::read($fichero_imagen)->rotate(-90);
            $thumbnail = Image::read($fichero_thumbnail)->rotate(-90);
        } else {
            $imagen = Image::read($fichero_imagen)->rotate(90);
            $thumbnail = Image::read($fichero_thumbnail)->rotate(90);
        }

        // Volver a subir los ficheros en el mismo path
        Storage::disk('s3')->put($ruta_imagen, $imagen->encode());
        Storage::disk('s3')->put($ruta_thumbnail, $thumbnail->encode());
    }

    public function reordenar(File $a1, File $a2)
    {
        $temp = $a1->orden;
        $a1->orden = $a2->orden;
        $a2->orden = $temp;

        $a1->save();
        $a2->save();

        return back();
    }
}
