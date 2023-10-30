<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HerramientasIP;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use function back;
use function config;
use function view;

class ProfileController extends Controller
{
    use HerramientasIP;

    public function show()
    {
        $user = Auth::user();

        $clientIP = $this->clientIP();
        $ip_egibide = $this->ip_in_range($this->clientIP(), ['150.241.173.0/24', '150.241.172.0/24']);

        return view('profile.show', compact([
            'user',
            'ip_egibide', 'clientIP',
        ]));
    }

    public function password()
    {
        $user = Auth::user();

        return view('profile.password', compact('user'));
    }

    public function updateUser(Request $request)
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

        return back();
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'current' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        $user = User::find(Auth::id());

        if (!Hash::check($request->current, $user->password)) {
            return back()->withErrors(['errors' => 'Current password does not match']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Cambiar la contraseña en Gitea
        if (config('ikasgela.gitea_enabled')) {
            GiteaClient::password($user->username, $request->password);
        }

        return back();
    }
}
