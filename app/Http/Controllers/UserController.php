<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use GrahamCampbell\GitLab\Facades\GitLab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $roles_disponibles = Role::all();

        return view('users.edit', compact(['user', 'roles_disponibles']));
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'username' => 'required',
            'roles_seleccionados' => 'required'
        ]);

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
        ]);

        $user->roles()->sync($request->input('roles_seleccionados'));

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
