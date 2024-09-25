<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // modifica la autenticacion del password a SHA1
    public function login(Request $request)
    {
        $usernameinput = $request->input('username');
        $password = $request->input('password');
        $field = filter_var($usernameinput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        error_log("sin login update: ".$usernameinput);
        if (\Auth::attempt([$field => $usernameinput, 'password' => sha1($password)])) {
            
            return redirect()->intended('/');
        } else {
            return $this->sendFailedLoginResponse($request);
        }
    }
    public function username()
    {
        return 'username';
    }
}
