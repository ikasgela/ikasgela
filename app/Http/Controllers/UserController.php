<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Organization;
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
        memorizar_ruta();

        $users = User::all();

        return view('users.index', compact('users'));
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

        return redirect(ruta_memorizada());
    }

    public function destroy(User $user)
    {
        // Borrar el usuario de GitLab
        // TODO: Gitea
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
