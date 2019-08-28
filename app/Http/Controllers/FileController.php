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

        $this->file->create([
            'path' => $path,
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
            Storage::disk('s3')->delete($file->path);
            $file->delete();
        } else {
            abort(403, __('Sorry, you are not authorized to access this page.'));
        }

        return back()->with('success', 'File Successfully Deleted');
    }
}
