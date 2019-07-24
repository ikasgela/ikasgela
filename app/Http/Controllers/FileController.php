<?php

namespace App\Http\Controllers;

use App\File;
use App\FileUpload;
use App\Http\Requests\StoreFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $path = Storage::disk('s3')->put('files', $request->file);

        $file_upload = factory(FileUpload::class)->create();

        $this->file->create([
            'path' => $path,
            'title' => $request->file->getClientOriginalName(),
            'size' => $request->file->getClientSize(),
            'user_id' => Auth::user()->id,
            'file_upload_id' => $file_upload->id
        ]);

        return back()->with('success', 'File Successfully Saved');
    }
}
