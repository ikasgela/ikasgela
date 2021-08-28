<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class TinymceUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $fichero = $request->file('image');

        $filename = md5(time()) . '/' . $fichero->getClientOriginalName();

        $imagen = Image::make($fichero)->orientate()
            ->resize(3000, 3000, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->stream();

        Storage::disk('s3')->put('images/' . $filename, $imagen->__toString());

        Auth::user()->files()->create([
            'path' => $filename,
            'title' => $fichero->getClientOriginalName(),
            'description' => request('description'),
            'size' => $fichero->getSize(),
            'user_id' => Auth::user()->id,
        ]);

        return route('tinymce.upload.url', ['path' => 'images/' . $filename]);
    }

    public function getS3(Request $request)
    {
        $path = request('path');

        return isset($path) ? response()->redirectTo(Storage::disk('s3')->temporaryUrl($path, now()->addDays(2))) : '';
    }
}
