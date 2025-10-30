<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validateEmail(Request $request)
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

        $request->validate(['email' => "required|string|email:rfc,dns|$validator:$dominios|max:255"]);
    }
}
