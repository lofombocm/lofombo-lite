<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFirstTimeConnection;
use Illuminate\Foundation\Auth\ResetsPasswords;
//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
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

        User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        //DB::table('password_resets')->where(['email'=> $request->email])->delete();
        Session::flush();
        Auth::logout();
        //return Redirect('login');
        session()->flash('status', 'Your password has been changed');
        return redirect()->route('authentification')->with('message', 'Votre mot de passe a ete modifie avec succes!');
        //return redirect('/login')->with('message', 'Votre mot de passe a ete modifie avec succes!');
    }


    public function postResetPasswordFirstConnection(Request $request)  {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|numeric|exists:users,id',
            //'currentpassword' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $user = User::where('id', intval($request->get('userid')))->first();
        $user->update(['password' => Hash::make($request->get('password'))]);
        Session::flush();
        Auth::logout();
        //return Redirect('login');
        session()->flash('status', 'Your password has been changed');

        $request->merge(['username' => $user->username]);

        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            session()->flash('status', 'Mot de passe modifie avec succes!');
            $userFirstTimeConnection = UserFirstTimeConnection::where('id', $user->id)->first();
            $userFirstTimeConnection->update(['has_been_connected' => true]);
            return redirect()->intended('home')->withSuccess('status', 'Mot de passe modifie avec succes!');
        }

        //return back()->withError('message', 'Invalid EMail/username or password');
        return back()->withErrors([
            'error' => 'Oups something went wrong, please try again later.',
        ]);
        //return redirect('/login')->with('message', 'Votre mot de passe a ete modifie avec succes!');
    }

}
