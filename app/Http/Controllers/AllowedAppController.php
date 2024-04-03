<?php

namespace App\Http\Controllers;

use App\Models\AllowedApp;
use App\Models\SafeExam;
use Illuminate\Http\Request;

class AllowedAppController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function create(SafeExam $safe_exam)
    {
        return view('allowed_apps.create', compact('safe_exam'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'executable' => 'required|max:255',
            'path' => 'required|max:255',
        ]);

        $allowed_app = AllowedApp::create([
            'title' => request('title'),
            'executable' => request('executable'),
            'path' => request('path'),
            'show_icon' => request()->has('show_icon'),
            'force_close' => request()->has('force_close'),
            'disabled' => request()->has('disabled'),
            'safe_exam_id' => request('safe_exam_id'),
        ]);

        return redirect(route('safe_exam.allowed', [$allowed_app->safe_exam->id]));
    }

    public function edit(AllowedApp $allowed_app)
    {
        return view('allowed_apps.edit', compact('allowed_app'));
    }

    public function update(Request $request, AllowedApp $allowed_app)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'executable' => 'required|max:255',
            'path' => 'required|max:255',
        ]);

        $allowed_app->update([
            'title' => request('title'),
            'executable' => request('executable'),
            'path' => request('path'),
            'show_icon' => request()->has('show_icon'),
            'force_close' => request()->has('force_close'),
            'disabled' => request()->has('disabled'),
        ]);

        return redirect(route('safe_exam.allowed', [$allowed_app->safe_exam->id]));
    }

    public function destroy(AllowedApp $allowed_app)
    {
        $allowed_app->delete();

        return back();
    }

    public function duplicate(AllowedApp $allowed_app)
    {
        $clon = $allowed_app->duplicate();
        $clon->save();

        return back();
    }
}
