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
use Illuminate\Support\Facades\Route;
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
        //dd(Route::current()->parameters());
        //dd(array_merge(Route::current()->parameters(),[/*'locale' => $locale*/]));
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

        //dd($request);
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'currentpassword' => 'required|string|min:8|max:20',
            'password' => 'required|string|min:8|max:20|confirmed',
        ], [
            'email.required' => __('L\'adresse E-Mail est obligatoire'),
            'email.email' => __('L\'adresse E-Mail n\'est pas valide'),
            'email.exists' => __("L'adresse E-Mail n'existe pas"),
            'currentpassword.required' => __('Le mot de passe actuel est obligatoire'),
            'password.required' => __('Le mot de passe est obligatoire'),
            'password:max' => __('Le mot de passe doit avoir au plus 20 caractères'),
            'password.min' => __('Le mot de passe doit avoir au moins 8 caractères'),
            'password.confirmed' => __('Les mots de passe ne correspondent pas'),
        ]);
        /*$updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])->first();

        if(!$updatePassword){
            return back()->withInput()->with('error', 'Invalid token!');
        }*/

        $user = User::where('email', $request->get('email'))->first();

        $credentials = ['username' => $user->username, 'password' => $request->get('currentpassword') ];//$request->only('email', 'current-password');
        if (!Auth::attempt($credentials)) {
            //dd($request->get('password'));
            $msg = __("Mot de passe actuel invalide");
            session()->flash('error', );
            return back()->withInput()->with('error', $msg);
        }

        User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        //DB::table('password_resets')->where(['email'=> $request->email])->delete();
        Session::flush();
        Auth::logout();
        //return Redirect('login');
        $msg = __("Modification reussie !");
        session()->flash('status', $msg);
        return redirect()->route('authentification')->with('message', $msg);
        //return redirect('/login')->with('message', 'Votre mot de passe a ete modifie avec succes!');
    }

}
