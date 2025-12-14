<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GuestController;
use App\Jobs\ProcessSendEMailPwdForgotJob;
use App\Models\Client;
use App\Models\Config;
use App\Models\PasswordRecoveryRequest;
use App\Models\Reward;
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

class LoginClientController extends Controller
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
    protected $redirectTo = '/home-client';

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
            session()->flash('error', __('Veuillez Patienter la configuration du système'));
            return redirect()->route('welcome');
            //return view('welcome',['rewards' => Reward::where('active', true)->get(), 'error' => 'Veuillez attendre la configuration s\'il vous plait.']);
            //return view('auth.client.login', []);
        }
        return view('auth.client.login');
    }

    public function username()
    {
        return 'telephone';
    }

    public function postLoginClientView(Request $request): RedirectResponse
    {
        $request->validate([
            'telephone' => 'required|string|exists:clients,telephone',
            'password' => 'required|string|min:8|max:20',
        ], [
            'telephone.required' => __('Le numéro de téléphone est obligatoire'),
            'password.required' => __('Le mot de passe est obligatoire'),
            'password.min' => __('Le mot de passe doit avoir au moins 8 caractères'),
            'password.max' => __('Le mot de passe doit avoir au plus 20 caractères')
        ]);

        $client = Client::where('telephone', $request->get('telephone'))->first();
        if (!$client->active) {
            session()->flash('error', __('Désolé, vous n\'avez pas l\'accès'));
            return back()->withErrors([
                'error' => __('Désolé, vous n\'avez pas l\'accès') . '.',
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
            session()->flash('status', __('Connexion réussie !'));
            return redirect()->route('home.client')->withSuccess('status', __('Connexion réussie !'));
        }
        //return redirect("auth/client")->back()->withError(['message' => 'Invalid Telephone or password']);
        session()->flash('error', __('Aucune entrée correspondant aux identifinats fournis'));
        return back()->withErrors([
            'error' => __('Aucune entrée correspondant aux identifinats fournis'),
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
        ],[
            'telephone.required' => __('Le numéro de téléphone est obligatoire'),
            'email.required' => __('L\'adresse E-Mail est obligatoire'),
            'telephone.exists' => __('Numéro de téléphone non reconnu')
        ]);

        $client = Client::where('telephone', $request->get('telephone'))->first();
        if(!$client){
            $msg = __('Aucun client disposant de ce numero de téléphone');
            session()->flash('error', $msg);
            return back()->withInput()->with('error', $msg);
        }

        $id = Str::uuid()->toString();
        $currentTimestamp = Carbon::now();
        $config = Config::where('is_applicable', true)->first();

        if ($config == null) {
            $msg = __('Veuillez Patienter la configuration du système');
            session()->flash('error', $msg);
            return back()->withInput()->with('error', $msg);
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

        $link = url('/' . GuestController::getApplicationLocal() . '/client-password-forgot-form/'. $id) ;
        $data = ['email' => $request->get('email'), 'name' => $client->name, 'passwordRecoveringUrl' => $link];

        //Mail::to($user->email)->send(new MailForPassordForgot($data));
        ProcessSendEMailPwdForgotJob::dispatch($data);

        $client->email = $request->get('email');
        $client->save();

        $msg = __('Vous recevrez un message a l\'adresse ') . $request->get('email') . ' ' . __('contenant le lien vous permettant de creer un nouveau mot de passe.');
        session()->flash('status', $msg);

        return back()->with('status', $msg);
    }


    public function forgotPasswordForm(string $locale, string $requestId): View
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
            $msg = __('Aucun demande de redéfinition de mot de passe trouvée pour vous.');
            session()->flash('error', $msg);
            return back()->withInput()->with('error', $msg);
        }

        $now = Carbon::now();
        $expire_at = Carbon::parse($password_recovery_request->expire_at);
        if ($expire_at->isBefore($now)) {
            $msg = __('Votre demande a expiré le: ');
            session()->flash('error',  $msg . $expire_at);
            return back()->withInput()->with('error', $msg . $expire_at);
        }

        Client::where('telephone', $password_recovery_request->telephone)->first()->update(['password' => Hash::make($request->get('password'))]);
        //Client::where('id', $client->id)->update(['password' => Hash::make($request->get('password'))]);
        $msg  = __('Mot de passe modifié avec succès !');
        session()->flash('status', '');
        return redirect()->route('authentification.client')->with('message', $msg);
    }

}
