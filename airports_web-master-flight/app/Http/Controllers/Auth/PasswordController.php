<?php

namespace FlyingCalculation\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use FlyingCalculation\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    /* public function __construct()
    {
        $this->middleware('guest');
    } */
		
		protected $redirectPath = '/';
		
    public function __construct(Guard $auth, PasswordBroker $passwords)
    {
			$this->auth = $auth;
			$this->passwords = $passwords;
			$this->subject = 'Reset your Password';
			$this->middleware('guest');
    }
		
		public function getEmail()
		{
			return view('auth.password');
		}

    public function postEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = Password::broker()->sendResetLink($request->only('email'));

        if ($response === Password::RESET_LINK_SENT) {
            return redirect()->back()->with('status', trans($response));
        }

        return redirect()->back()->withErrors(['email' => trans($response)]);
    }

    public function getReset($token = null)
    {
        if (is_null($token)) {
            abort(404);
        }

        return view('auth.reset')->with('token', $token);
    }

    public function postReset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');

        $response = Password::broker()->reset($credentials, function ($user, $password): void {
            $user->password = bcrypt($password);
            $user->setRememberToken(Str::random(60));
            $user->save();

            event(new PasswordReset($user));
        });

        if ($response === Password::PASSWORD_RESET) {
            return redirect('/')->with('status', trans($response));
        }

        return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => trans($response)]);
    }
}
