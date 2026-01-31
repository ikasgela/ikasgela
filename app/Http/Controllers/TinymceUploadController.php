<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class TinymceUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $fichero = $request->file('image');

        $filename = Str::uuid() . '/' . $fichero->getClientOriginalName();

        $imagen = Image::read($fichero)
            ->scaleDown(2000, 2000)
            ->encode(new WebpEncoder(quality: 80));

        Storage::disk('s3')->put('images/' . $filename, $imagen);

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

        if (!isset($path) || Str::contains($path, '..'))
            abort(404);

        return isset($path) ? response()->redirectTo(Storage::disk('s3-urls')->temporaryUrl($path, now()->addDays(2))) : '';
    }
}
