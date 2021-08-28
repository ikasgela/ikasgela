<?php

namespace App\Http\Controllers;

use App\Gitea\GiteaClient;
use App\Models\Curso;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->has('filtro_etiquetas')) {
            if (request('filtro_etiquetas') == 'N') {
                session(['profesor_filtro_etiquetas' => '']);
                session(['tags' => []]);
            }
        }

        $organizations = Organization::orderBy('name')->get();
        $cursos = Curso::organizacionActual()->orderBy('nombre')->get();

        $request->validate([
            'organization_id' => 'numeric|integer',
        ]);

        if (request('organization_id') >= -1) {
            session(['filtrar_organization_actual' => request('organization_id')]);
        } else if (empty(session('filtrar_organization_actual'))) {
            session(['filtrar_organization_actual' => setting_usuario('_organization_id')]);
        }

        if (session('filtrar_organization_actual') == -1 || empty(session('filtrar_organization_actual'))) {
            $users = User::query();
        } else {
            $users = Organization::find(session('filtrar_organization_actual'))->users();
        }

        if ($request->has('tag')) {
            session(['profesor_filtro_etiquetas' => 'S']);
            session()->push('tags', request('tag'));
        }

        if (!is_null(session('tags'))) {
            $users = $users->tags(session('tags'));
        }

        $users = $users->get();

        $ids = $users->pluck('id')->toArray();

        return view('users.index', compact(['users', 'organizations', 'ids', 'cursos']));
    }

    public function edit(User $user)
    {
        $roles_disponibles = Role::all();

        $cursos_seleccionados = $user->cursos()->orderBy('nombre')->get();

        $curso_actual = !is_null($user->curso_actual()) ? $user->curso_actual()->id : null;

        $filtro = $user->cursos()->pluck('curso_id')->unique()->flatten()->toArray();
        $cursos_disponibles = Curso::whereNotIn('id', $filtro)->orderBy('nombre')->get();

        $organizations_seleccionados = $user->organizations()->orderBy('name')->get();

        $filtro = $user->organizations()->pluck('organization_id')->unique()->flatten()->toArray();
        $organizations_disponibles = Organization::whereNotIn('id', $filtro)->orderBy('name')->get();

        return view('users.edit', compact(['user', 'roles_disponibles',
            'cursos_disponibles', 'cursos_seleccionados', 'curso_actual',
            'organizations_disponibles', 'organizations_seleccionados'
        ]));
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'username' => 'required',
            'roles_seleccionados' => 'required',
        ]);

        $user->update([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'last_active' => $request->input('last_active'),
            'blocked_date' => $request->input('blocked_date'),
            'max_simultaneas' => request('max_simultaneas'),
            'tags' => request('tags'),
        ]);

        $user->roles()->sync($request->input('roles_seleccionados'));

        $user->cursos()->sync($request->input('cursos_seleccionados'));

        $user->organizations()->sync($request->input('organizations_seleccionados'));

        setting()->setExtraColumns(['user_id' => $user->id]);
        if (!is_null($request->input('curso_id')) && $request->has('cursos_seleccionados')) {
            setting(['curso_actual' => $request->input('curso_id')]);
        } else {
            setting()->forget('curso_actual');
        }
        setting()->save();

        return retornar();
    }

    public function destroy(User $user)
    {
        // Borrar el usuario de Gitea
        if (config('ikasgela.gitea_enabled')) {
            try {
                GiteaClient::borrar_usuario($user->username);
            } catch (\Exception $e) {
                Log::error('Gitea: Error al borrar el usuario.', [
                    'username' => $user->username,
                    'exception' => $e->getMessage()
                ]);
            }
        }

        $user->delete();

        return back();
    }

    public function manualActivation(Request $request)
    {
        $user = User::findOrFail(request('user_id'));

        $user->markEmailAsVerified();

        GiteaClient::unblock($user->email, $user->username);

        return back();
    }

    public function toggle_help()
    {
        $user = Auth::user();

        $user->tutorial = !$user->tutorial;
        $user->save();

        session(['tutorial' => $user->tutorial]);

        return back();
    }

    public function toggleBlocked(Request $request)
    {
        $user = User::findOrFail(request('user_id'));

        if (!is_null($user->blocked_date)) {
            $this->block($user);
        } else {
            $this->unblock($user);
        }

        return back();
    }

    private function unblock($user): void
    {
        GiteaClient::unblock($user->email, $user->username);
        $user->blocked_date = null;
        $user->save();
    }

    private function block($user): void
    {
        GiteaClient::block($user->email, $user->username);
        $user->blocked_date = now();
        $user->save();
    }

    public function matricular(Request $request)
    {
        $this->validate($request, [
            'usuarios_seleccionados' => 'required',
            'curso_id' => 'required',
        ]);

        $curso = Curso::findOrFail(request('curso_id'));

        foreach (request('usuarios_seleccionados') as $user_id) {

            $user = User::find($user_id);

            $curso->users()->syncWithoutDetaching($user);

            setting_usuario(['curso_actual' => $curso->id], $user);
            $user->clearCache();
        }
    }

    public function bloquear_grupo(Request $request)
    {
        $this->validate($request, [
            'usuarios_seleccionados' => 'required',
        ]);

        foreach (request('usuarios_seleccionados') as $user_id) {
            $user = User::findOrFail($user_id);
            $this->block($user);
        }
    }

    public function desbloquear_grupo(Request $request)
    {
        $this->validate($request, [
            'usuarios_seleccionados' => 'required',
        ]);

        foreach (request('usuarios_seleccionados') as $user_id) {
            $user = User::findOrFail($user_id);
            $this->unblock($user);
        }
    }

    public function borrar_grupo(Request $request)
    {
        $this->validate($request, [
            'usuarios_seleccionados' => 'required',
        ]);

        foreach (request('usuarios_seleccionados') as $user_id) {
            $user = User::findOrFail($user_id);
            $this->destroy($user);
        }
    }

    public function acciones_grupo(Request $request)
    {
        $this->validate($request, [
            'action' => 'required|in:enroll,block,unblock,delete',
        ]);

        switch (request('action')) {
            case 'enroll':
                $this->matricular($request);
                break;
            case 'block':
                $this->bloquear_grupo($request);
                break;
            case 'unblock':
                $this->desbloquear_grupo($request);
                break;
            case 'delete':
                $this->borrar_grupo($request);
                break;
            default:
                break;
        }

        return back();
    }
}
