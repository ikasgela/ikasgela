<?php

namespace App\Http\Controllers\Profile;

use Ikasgela\Gitea\GiteaClient;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $user->surname = $request->surname;
        $user->save();

        if (config('ikasgela.gitea_enabled')) {
            $nombre_completo = $request->name . ' ' . $request->surname;
            GiteaClient::full_name($user->email, $user->username, $nombre_completo);
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

        // Cambiar la contraseña en Gitea
        if (config('ikasgela.gitea_enabled')) {
            GiteaClient::password($user->username, $request->password);
        }

        return $user;
    }
}
