<?php

namespace App\Http\Controllers;

use App\User;
use GrahamCampbell\GitLab\Facades\GitLab;
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
