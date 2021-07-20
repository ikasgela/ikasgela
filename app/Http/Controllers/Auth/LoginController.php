<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        switch (subdominio()) {
            case 'egibide':
                $dominios = 'egibide.org,ikasle.egibide.org';
                $validator = 'allowed_domains';
                break;
            case 'deusto':
                $dominios = 'deusto.es,opendeusto.es';
                $validator = 'allowed_domains';
                break;
            default:
                $dominios = 'egibide.org,ikasle.egibide.org,deusto.es,opendeusto.es';
                $validator = 'forbidden_domains';
                break;
        }

        $request->validate([
            $this->username() => "required|string|email" . config('app.env') == 'production' ? "|$validator:$dominios" : "",
            'password' => 'required|string',
        ]);
    }
}
