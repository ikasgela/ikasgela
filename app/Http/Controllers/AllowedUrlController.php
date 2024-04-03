<?php

namespace App\Http\Controllers;

use App\Models\AllowedUrl;
use App\Models\SafeExam;
use Illuminate\Http\Request;

class AllowedUrlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function create(SafeExam $safe_exam)
    {
        return view('allowed_urls.create', compact('safe_exam'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|max:255',
        ]);

        $allowed_url = AllowedUrl::create([
            'url' => request('url'),
            'disabled' => request()->has('disabled'),
            'safe_exam_id' => request('safe_exam_id'),
        ]);

        return redirect(route('safe_exam.allowed', [$allowed_url->safe_exam->id]));
    }

    public function edit(AllowedUrl $allowed_url)
    {
        return view('allowed_urls.edit', compact('allowed_url'));
    }

    public function update(Request $request, AllowedUrl $allowed_url)
    {
        $this->validate($request, [
            'url' => 'required|max:255',
        ]);

        $allowed_url->update([
            'url' => request('url'),
            'disabled' => request()->has('disabled'),
        ]);

        return redirect(route('safe_exam.allowed', [$allowed_url->safe_exam->id]));
    }

    public function destroy(AllowedUrl $allowed_url)
    {
        $allowed_url->delete();

        return back();
    }

    public function duplicate(AllowedUrl $allowed_url)
    {
        $clon = $allowed_url->duplicate();
        $clon->save();

        return back();
    }
}
