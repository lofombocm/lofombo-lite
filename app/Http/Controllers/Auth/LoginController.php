<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\UserFirstTimeConnection;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
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
        ], [
            'username.exists' => __('Le système n\'a pas reconnu votre nom d\'utilisateur.'),
            'username.required' => __('Le nom d\'utilisateur est obligatoire.'),
            'password.required' => __('Le mot de passe est obligatoire.'),
            'password.min' => __('Le mot de passe doit avoir au moins 8 caractères'),
            'password.max' => __('Le mot de passe doit avoir au plus 20 caractères')
        ]);

        //dd($request->all());

        $user = User::where('username', $request->get('username'))->first();
        $userFirstTimeConnection = UserFirstTimeConnection::where('id', $user->id)->first();

        if (!$user->active) {
            session()->flash('error', __('Désolé, vous n\'avez pas l\'accès'));
            return back()->withErrors([
                'error' => __('Désolé, vous n\'avez pas l\'accès'),
            ]);
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->remember)) {

            if (!$userFirstTimeConnection->has_been_connected){
                //Session::flush();
                Auth::logout();
                session()->flash('status', __('Bien vouloir choisir un nouveau mot de passe'));
                return view('auth.change-pwd',
                    ['user'=>$user, 'status' => __('Bien vouloir choisir un nouveau mot de passe')]);
                //return redirect()->route('login')->with(['status' => 'Vous etes invite a choisir un nouveau mot de passe.']);
            }

            $request->session()->regenerate();
            session()->flash('status', __('Connexion réussie !'));
            //
            if (Auth::user()->is_admin) {
                session()->flash('status', __('Connexion réussie !'));
                return redirect()->route('bi.menu')->withSuccess('status', __('Connexion réussie !'));
            }else{
                session()->flash('status', __('Connexion réussie !'));
                return redirect()->route('home.purchases.index')->withSuccess('status', __('Connexion réussie !'));
            }
        }

        //return back()->withError('message', 'Invalid EMail/username or password');
        $msg = __('Nom Utilisateur et/ou Mot de passe incorrects !');
        session()->flash('error', $msg);
        return back()->withErrors([
            'error' => $msg,
        ]);
    }




    public function postResetPasswordFirstConnection(Request $request)  {
        //dd($request->all());
        if ($request->filled('password')) {
            if ($request->get('password') === '12345678'){
                $msg = __("Le mot de passe doit être obligatoirement modifié.");
                session()->flash('error', $msg);
                return back()->withErrors(['error' => $msg]);
            }
        }
        $validator = Validator::make($request->all(), [
            'userid' => 'required|numeric|exists:users,id',
            //'currentpassword' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
        ],[
            'userid.required' => __('Utilisateur non reconnu!'),
            'userid.exists' => __('Utilisateur non reconnu!'),
            'password.required' => __('Le mot de passe est obligatoire.'),
            'password.min' => __('Le mot de passe doit avoir au moins 8 caractères'),
            'password.max' => __('Le mot de passe doit avoir au plus 20 caractères'),
            'password.confirmed' => __('Les mots de passe ne correspondent pas'),
        ]);

        //dd($validator);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $user = User::where('id', intval($request->get('userid')))->first();
        $user->update(['password' => Hash::make($request->get('password'))]);
        Session::flush();
        Auth::logout();
        //return Redirect('login');
        $msg = __('Modification reussie !');
        session()->flash('status', $msg);

        $request->merge(['username' => $user->username]);

        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            $msg = __('Modification reussie !');
            session()->flash('status', $msg);
            $userFirstTimeConnection = UserFirstTimeConnection::where('id', $user->id)->first();
            $userFirstTimeConnection->update(['has_been_connected' => true]);
            return redirect()->route('dashboard')->withSuccess('status', $msg);
        }

        //return back()->withError('message', 'Invalid EMail/username or password');
        return back()->withErrors([
            'error' => __('Nom Utilisateur et/ou Mot de passe incorrects !'),
        ]);
        //return redirect('/login')->with('message', 'Votre mot de passe a ete modifie avec succes!');
    }

}
