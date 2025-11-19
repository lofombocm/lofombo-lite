<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFirstTimeConnection;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
    protected $redirectTo = '/reports';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function username()
    {
        return 'username';
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(): View
    {
        return view('auth.login');
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:users,username',
            'password' => 'required|string|min:8|max:20',
        ]);

        $user = User::where('username', $request->get('username'))->first();
        $userFirstTimeConnection = UserFirstTimeConnection::where('id', $user->id)->first();

        if (!$user->active) {
            session()->flash('error', 'Votre compte est inactif.');
            return back()->withErrors([
                'error' => 'Votre compte est inactif.',
            ]);
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->remember)) {

            if (!$userFirstTimeConnection->has_been_connected){
                session()->flash('status', 'Vous etes invite a choisir un nouveau mot de passe.');
                return view('auth.change-pwd',
                    ['user'=>$user, 'status' => 'Vous etes invite a choisir un nouveau mot de passe.']);
                //return redirect()->route('login')->with(['status' => 'Vous etes invite a choisir un nouveau mot de passe.']);
            }

            $request->session()->regenerate();
            session()->flash('status', 'Authentifie avec succes!');
            //
            if (Auth::user()->is_admin) {
                return redirect()->intended('/reports')->withSuccess('status', 'You have Successfully loggedin');
            }else{
                return redirect()->to('/home/purchases')->withSuccess('status', 'You have Successfully loggedin');
            }
        }

        //return back()->withError('message', 'Invalid EMail/username or password');
        return back()->withErrors([
            'error' => 'The provided credentials do not match our records.',
        ]);
    }

}
