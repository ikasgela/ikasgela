<?php

namespace App\Http\Controllers\Profile;

use App\Gitea\GiteaClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;

use GitLab;

class ProfileController extends Controller
{
    public function getAuthUser()
    {
        return Auth::user();
    }

    public function updateAuthUser(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string'
        ]);

        $user = User::find(Auth::id());

        $user->name = $request->name;
        $user->save();

        if (config('ikasgela.gitlab_enabled')) {
            $gitlab = GitLab::users()->all([
                'search' => $user->email
            ]);

            foreach ($gitlab as $usuario) {
                GitLab::users()->update($usuario['id'], [
                    'name' => $request->name,
                ]);
            }
        }

        if (config('ikasgela.gitea_enabled')) {
            GiteaClient::full_name($user->email, $user->username, $request->name);
        }

        return $user;
    }

    public function updateAuthUserPassword(Request $request)
    {
        $this->validate($request, [
            'current' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        $user = User::find(Auth::id());

        if (!Hash::check($request->current, $user->password)) {
            return response()->json(['errors' => ['current' => ['Current password does not match']]], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Cambiar la contraseña en GitLab
        // REF: Parche para que GitLab no pida cambiarla de nuevo: https://stackoverflow.com/a/50278167
        if (config('ikasgela.gitlab_enabled')) {
            $gitlab = GitLab::users()->all([
                'search' => $user->email
            ]);

            foreach ($gitlab as $usuario) {
                GitLab::users()->update($usuario['id'], [
                    'password' => $request->password
                ]);
            }
        }

        // Cambiar la contraseña en Gitea
        if (config('ikasgela.gitea_enabled')) {
            GiteaClient::password($user->email, $user->username, $request->password);
        }

        return $user;
    }
}
