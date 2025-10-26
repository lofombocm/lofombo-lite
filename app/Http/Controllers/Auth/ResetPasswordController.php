<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ResetPasswordController extends Controller
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

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/auth';

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function resetPassword(): View
    {
        //$thiken = $this->route('token');
        return view('auth.passwords.reset');
    }

    /**
     * Write code on Method
     *
     * @return
     */

    public function postResetPassword(Request $request)  {
        //$reqStr = json_encode($request);
        //return redirect('/test')->with('message', $reqStr);

        $request->validate([
            'email' => ['required', 'string', 'email'],
            'currentpassword' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
        ]);
        /*$updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])->first();

        if(!$updatePassword){
            return back()->withInput()->with('error', 'Invalid token!');
        }*/

        $credentials = ['email' => $request->email, 'password' => $request->currentpassword ];//$request->only('email', 'current-password');
        if (!Auth::attempt($credentials)) {
            return back()->withInput()->with('error', 'Invalid current password');
        }

        $user = User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        //DB::table('password_resets')->where(['email'=> $request->email])->delete();
        Session::flush();
        Auth::logout();
        //return Redirect('login');
        return redirect()->route('authentification')->with('message', 'Votre mot de passe a ete modifie avec succes!');
        //return redirect('/login')->with('message', 'Votre mot de passe a ete modifie avec succes!');
    }
}
