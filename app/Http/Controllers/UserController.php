<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Unidad;
use App\User;
use GrahamCampbell\GitLab\Facades\GitLab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle_help()
    {
        $user = Auth::user();

        $user->tutorial = !$user->tutorial;
        $user->save();

        session(['tutorial' => $user->tutorial]);

        return back();
    }

    public function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'username' => 'required',
        ]);

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
        ]);

        return redirect(route('users.index'));
    }

    public function destroy(User $user)
    {
        // Borrar el usuario de GitLab
        try {
            $usuarios = GitLab::users()->all([
                'search' => $user->email
            ]);
            foreach ($usuarios as $borrar) {
                GitLab::users()->remove($borrar['id']);
            }
        } catch (\Exception $e) {
        }

        $user->delete();

        return back();
    }
}
