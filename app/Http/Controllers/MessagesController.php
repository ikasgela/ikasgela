<?php

namespace App\Http\Controllers;

use App\Mail\Alerta;
use App\Mail\NuevoMensaje;
use App\Models\Curso;
use App\Models\Hilo;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class MessagesController extends Controller
{
    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {
        // All threads that user is participating in, with new messages
        $threads = Hilo::forUserWithNewMessages(Auth::id())->cursoActual()->latest('updated_at')->get();

        // All threads that user is participating in
        $threads_all = Hilo::forUser(Auth::id())->cursoActual()->latest('updated_at')->paginate(10);
        $threads_all_count = Hilo::forUser(Auth::id())->cursoActual()->count();

        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        return view('messenger.index', compact(['threads', 'threads_all', 'threads_all_count', 'curso_actual']));
    }

    public function all()
    {
        // All threads that user is participating in
        $threads = Hilo::forUser(Auth::id())->cursoActual()->latest('updated_at')->get();

        return view('messenger.index', compact('threads'));
    }

    /**
     * Shows a message thread.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            $thread = Hilo::forUser(Auth::id())->cursoActual()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', __('Thread not found.'));

            return redirect()->route('messages');
        }

        // show current user in list if not a current participant
        // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();

        // don't show the current user in list
        $userId = Auth::id();
        $users = User::whereNotIn('id', $thread->participantsUserIds($userId))->get();

        $thread->markAsRead($userId);

        return view('messenger.show', compact('thread', 'users'));
    }

    private function enviarEmails($thread)
    {
        $userId = Auth::id();

        $users = User::whereNotIn('id', [$userId])
            ->whereIn('id', $thread->participantsUserIds())
            ->get();

        foreach ($users as $user) {
            if ($thread->alert || setting_usuario('notificacion_mensaje_recibido', $user)) {

                if (!$thread->alert) {
                    Mail::to($user)->queue(new NuevoMensaje($thread->latestMessage));
                } else {
                    Mail::to($user)->queue(new Alerta($thread->latestMessage));
                }
            }
        }
    }

    /**
     * Creates a new message thread.
     *
     * @return mixed
     */
    public function create(Request $request)
    {
        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        $users = $curso_actual->users()->noBloqueado()->where('users.id', '!=', Auth::id())->orderBy('surname')->orderBy('name')->get();

        $profesores = $curso_actual->users()->noBloqueado()->rolProfesor()->orderBy('surname')->orderBy('name')->get();

        $titulo = request('titulo');

        $selected_users = [request('user_id')];

        return view('messenger.create', compact(['users', 'profesores', 'titulo', 'selected_users']));
    }

    public function create_team(Request $request)
    {
        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        $users = $curso_actual->users()->noBloqueado()->where('users.id', '!=', Auth::id())->orderBy('surname')->orderBy('name')->get();

        $profesores = $curso_actual->users()->rolProfesor()->orderBy('surname')->orderBy('name')->get();

        $titulo = request('titulo');

        $selected_user = request('user_id');

        $team = Team::find(request('team_id'));
        $selected_users = $team->users->pluck('id')->toArray();

        return view('messenger.create', compact(['users', 'profesores', 'titulo', 'selected_users']));
    }

    /**
     * Stores a new message thread.
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required',
            'message' => 'required',
            'recipients' => 'required',
        ]);

        $mensaje_filtrado = $this->filtrarParrafosVacios($request['message']);

        $thread = Hilo::create([
            'subject' => $request['subject'],
            'owner_id' => Auth::id(),
            'noreply' => $request->has('noreply'),
            'alert' => $request->has('alert'),
            'curso_id' => setting_usuario('curso_actual'),
        ]);

        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => $mensaje_filtrado,
        ]);

        // Sender
        Participant::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'last_read' => new Carbon,
        ]);

        // Recipients
        if ($request->has('recipients')) {
            $thread->addParticipant($request['recipients']);
        }

        $this->enviarEmails($thread, Auth::id());

        return retornar();
    }

    /**
     * Adds a new message to a current thread.
     *
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'message' => 'required',
        ]);

        try {
            $thread = Hilo::forUser(Auth::id())->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', __('Thread not found.'));

            return redirect()->route('messages');
        }

        $thread->activateAllParticipants();

        $mensaje_filtrado = $this->filtrarParrafosVacios($request['message']);

        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => $mensaje_filtrado
        ]);

        // Add replier as a participant
        $participant = Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
        ]);
        $participant->last_read = new Carbon;
        $participant->save();

        // Recipients
        if ($request->has('recipients')) {
            $thread->addParticipant($request['recipients']);
        }

        $this->enviarEmails($thread, Auth::id());

        return redirect()->route('messages.show', $id);
    }

    public function destroy($id)
    {
        $thread = Hilo::findOrFail($id);
        $thread->delete();

        return back();
    }

    public function destroyMessage($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return back();
    }

    private function filtrarParrafosVacios($mensaje)
    {
        return preg_replace('/<p[^>]*>&nbsp;<\\/p[^>]*>/', '', $mensaje);
    }
}
