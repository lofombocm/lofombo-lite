<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GuestController;
use App\Jobs\ProcessSendEMailPwdForgotJob;
use App\Models\Client;
use App\Models\Config;
use App\Models\PasswordRecoveryRequest;
use App\Models\Reward;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginSuperAdminController extends Controller
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
    protected $redirectTo = '/home-super-admin';

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


    public function loginClientView()
    {

        $configs = Config::where('is_applicable', true)->get();
        if(count($configs) == 0){
            session()->flash('error', 'Veuillez attendre la configuration s\'il vous plait.');
            return redirect()->route('welcome');
            //return view('welcome',['rewards' => Reward::where('active', true)->get(), 'error' => 'Veuillez attendre la configuration s\'il vous plait.']);
            //return view('auth.client.login', []);
        }
        return view('auth.client.login');
    }

    public function username()
    {
        return 'username';
    }

    public function postLoginClientView(Request $request): RedirectResponse
    {
        $request->validate([
            'telephone' => 'required|string|exists:clients,telephone',
            'password' => 'required|string|min:8',
        ]);

        $client = Client::where('telephone', $request->get('telephone'))->first();
        if (!$client->active) {
            return back()->withErrors([
                'error' => 'The client is not active.',
            ]);
        }

        /*$credentials = array();
        $credentials['email'] = $request->get('telephone');
        $credentials['password'] = $request->get('password');*/

        //$request->all()['email'] =  $request->get('telephone');
        $credentials = $request->only('telephone', 'password');

        //$client = $client->save();
        //return json_encode($credentials);



        if (Auth::guard('client')->attempt($credentials)) {
            /*$h = fopen('test.txt', 'w+');
            fwrite($h, json_encode( json_encode($credentials)));
            fclose($h);*/
            //return redirect()->route('home.client')->withSuccess('status', 'You have Successfully loggedin');
            return redirect()->intended('home-client')->withSuccess('status', 'You have Successfully loggedin');
        }
        //return redirect("auth/client")->back()->withError(['message' => 'Invalid Telephone or password']);
        return back()->withErrors([
            'error' => 'The provided credentials do not match our records.',
        ]);
    }


    public function forgotPassword(): View
    {
        //$thiken = $this->route('token');
        return view('auth.passwords.email-client');
    }


    public function postForgotPassword(Request $request){
        //return json_encode($request);
        $request->validate([
            'telephone' => 'required|string|exists:clients,telephone',
            'email' => 'required|email|max:255',
        ]);

        $client = Client::where('telephone', $request->get('telephone'))->first();
        if(!$client){
            session()->flash('error', 'No client with this phone number');
            return back()->withInput()->with('error', 'No client with this phone number');
        }

        $id = Str::uuid()->toString();
        $currentTimestamp = Carbon::now();
        $config = Config::where('is_applicable', true)->first();

        if ($config == null) {
            session()->flash('error', 'Aucune configuration du systeme n\'est definie.');
            return back()->withInput()->with('error', 'Aucune configuration du systeme n\'est definie.');
        }

        $pwdRecoverDuation = intval(strval($config->password_recovery_request_duration));
        /*if (!($config === null)){
            $pwdRecoverDuation = $config->password_recovery_request_duration;
        }*/
        $expire_at = $currentTimestamp->addMinutes($pwdRecoverDuation);

        $pwdRequest = PasswordRecoveryRequest::create(
            [
                'id' => $id,
                'email' => $request->get('email'),
                'telephone' => $request->get('telephone'),
                'expire_at' => $expire_at
            ]);

        $link = url('/' . GuestController::getApplicationLocal() . '/client-password-forgot-form/'. $id);
        $data = ['email' => $request->get('email'), 'name' => $client->name, 'passwordRecoveringUrl' => $link];

        //Mail::to($user->email)->send(new MailForPassordForgot($data));
        ProcessSendEMailPwdForgotJob::dispatch($data);

        $client->email = $request->get('email');
        $client->save();

        session()->flash('status', 'Vous recevrez un email a l\'adresse ' . $request->get('email') . ' contenant le lien vous permettant de creer un nouveau mot de passe.');

        return back()->with('status', 'Vous recevrez un email a l\'adresse ' . $request->get('email') . ' contenant le lien vous permettant de creer un nouveau mot de passe.');
    }


    public function forgotPasswordForm($requestId): View
    {
        //$thiken = $this->route('token');
        $req = PasswordRecoveryRequest::where('id', $requestId)->first();
        $client = Client::where('telephone', $req->telephone)->first();
        return view('auth.passwords.client-recover-password', ['requestId' => $requestId, 'client' => $client]);
    }


    public function postForgotPasswordForm(Request $request){
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
        ],[
            'password.required' => __('Le mot de passe est obligatoire'),
            'password.min' => __('Le mot de passe doit avoir au moins 8 caractères'),
            'password.max' => __('Le mot de passe doit avoir au plus 20 caractères'),
            'password.confirmed' => __('Les mots de passe ne correspondent pas'),
        ]);
        $password_recovery_request = PasswordRecoveryRequest::where('id', $request->get('requestid'))->first();

        if(!$password_recovery_request){
            session()->flash('error', 'Aucun demande de redefinition de mot de passe trouver pour vous.');
            return back()->withInput()->with('error', 'Aucun demande de redefinition de mot de passe trouver pour vous.');
        }

        $now = Carbon::now();
        $expire_at = Carbon::parse($password_recovery_request->expire_at);
        if ($expire_at->isBefore($now)) {
            session()->flash('error',  'Votre demande a expire le: ' . $expire_at);
            return back()->withInput()->with('error', 'Votre demande a expire le: ' . $expire_at);
        }

        Client::where('telephone', $password_recovery_request->telephone)->first()->update(['password' => Hash::make($request->get('password'))]);
        //Client::where('id', $client->id)->update(['password' => Hash::make($request->get('password'))]);
        session()->flash('status', 'Votre mot de passe a ete redefini avec succes!');
        return redirect()->route('authentification.client')->with('message', 'Votre mot de passe a ete redefini avec succes!');
    }






    public function indexSuperAdmin()
    {
        return view('auth.login-super-admin');
    }


    public function postLoginSuperAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:super_admins,username',
            'password' => 'required|string|min:8|max:20',
        ]);

        //dd($request->all());

        $superAdmin = SuperAdmin::where('username', $request->get('username'))->first();

        if (!$superAdmin->active) {
            session()->flash('error', 'Votre compte est inactif.');
            return back()->withErrors([
                'error' => 'Votre compte est inactif.',
            ]);
        }

        $credentials = $request->only('username', 'password');

        //dd(Hash::check($credentials['password'], $superAdmin->password));

        if (Auth::guard('super_admin')->attempt($credentials)) {
            $request->session()->regenerate();
            session()->flash('status', 'Connexion réussie !');
            if (Auth::guard('super_admin')->user()->is_super_admin) {
                session()->flash('status', 'Vous ets connecte avec succes!.');
                //dd(Auth::guard('super_admin')->user());
                return redirect()->route('home-super-admin')->withSuccess('status', 'Vous etes connecte avec succes!.');
            }/*else{
                return redirect()->to('/home/purchases')->withSuccess('status', 'You have Successfully loggedin');
            }*/
        }

        //return back()->withError('message', 'Invalid EMail/username or password');
        session()->flash('error', 'Nom d\'utilisateur ou mot de passe incorrect ou vous n\'etes pas autorise.');
        return back()->withErrors([
            'error' => 'Nom d\'utilisateur ou mot de passe incorrect ou vous n\'etes pas autorise.',
        ]);
    }


}
