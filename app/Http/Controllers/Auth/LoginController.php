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
     * Max attempts before lockout (configurable via .env).
     *
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * Minutes during which attempts are locked out.
     *
     * @var int
     */
    protected $decayMinutes = 1;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        $this->maxAttempts = config('ikasgela.auth_throttle.login_max_attempts', 5);
        $this->decayMinutes = config('ikasgela.auth_throttle.login_decay_minutes', 1);
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
            case 'backend':
                $dominios = 'ikasle.egibide.org,opendeusto.es';
                $validator = 'forbidden_domains';
                break;
            default:
                $dominios = 'egibide.org,ikasle.egibide.org,deusto.es,opendeusto.es';
                $validator = 'forbidden_domains';
                break;
        }

        $request->validate([
            $this->username() => "required|string|email|$validator:$dominios",
            'password' => 'required|string',
        ]);
    }
}
