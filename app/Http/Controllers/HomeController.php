<?php

namespace App\Http\Controllers;

use App\Organization;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function getAuthUser()
    {
        return Auth::user();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    public function index()
    {
        if (!is_null($this->getAuthUser())) {

            $organization_id = Organization::where('slug', subdominio())->first()->id;

            setting()->setExtraColumns(['user_id' => Auth::user()->id]);

            setting(['organization_actual' => $organization_id]);
            setting()->save();

            if ($this->getAuthUser()->hasRole('profesor')) {
                return redirect(route('profesor.index'));
            } else {
                return redirect(route('users.home'));
            }
        } else {
            return view('welcome');
        }
    }
}
