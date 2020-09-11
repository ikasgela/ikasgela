<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class TinymceUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $fichero = $request->file('image');

        $filename = md5(time()) . '/' . $fichero->getClientOriginalName();

        $imagen = Image::make($fichero)->orientate()->stream();

        Storage::disk('s3-test')->put('pruebas/' . $filename, $imagen->__toString());

        return mce_back(route('tinymce.upload.url', ['path' => 'pruebas/' . $filename]));
    }

    public function getS3(Request $request)
    {
        $path = request('path');

        return response()->redirectTo(Storage::disk('s3-test')->temporaryUrl($path, now()->addDays(1)));
    }
}
