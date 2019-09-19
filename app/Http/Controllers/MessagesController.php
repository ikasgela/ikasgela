<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Hilo;
use App\Mail\NuevoMensaje;
use App\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
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
        memorizar_ruta();

        // All threads that user is participating in, with new messages
        $threads = Hilo::forUserWithNewMessages(Auth::id())->latest('updated_at')->get();

        return view('messenger.index', compact('threads'));
    }

    public function all()
    {
        memorizar_ruta();

        // All threads that user is participating in
        $threads = Hilo::forUser(Auth::id())->latest('updated_at')->get();

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
            $thread = Hilo::forUser(Auth::id())->findOrFail($id);
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
            if (!in_array($user->email, ['ikasgela@egibide.org', 'ikasgela@deusto.es']))
                Mail::to($user->email)->queue(new NuevoMensaje());
        }
    }

    /**
     * Creates a new message thread.
     *
     * @return mixed
     */
    public function create()
    {
        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        $users = $curso_actual->users()->where('users.id', '!=', Auth::id())->get();

        $profesores = $curso_actual->users()->rolProfesor()->get();

        return view('messenger.create', compact(['users', 'profesores']));
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

        $input = Input::all();

        $mensaje_filtrado = $this->filtrarParrafosVacios($input['message']);

        $thread = Hilo::create([
            'subject' => $input['subject'],
            'owner_id' => Auth::id(),
            'noreply' => Input::has('noreply')
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
        if (Input::has('recipients')) {
            $thread->addParticipant($input['recipients']);
        }

        $this->enviarEmails($thread, Auth::id());

        return redirect()->route('messages');
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

        $mensaje_filtrado = $this->filtrarParrafosVacios(Input::get('message'));

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
        if (Input::has('recipients')) {
            $thread->addParticipant(Input::get('recipients'));
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

    private function filtrarParrafosVacios($mensaje)
    {
        return preg_replace('/<p[^>]*>&nbsp;<\\/p[^>]*>/', '', $mensaje);
    }
}
