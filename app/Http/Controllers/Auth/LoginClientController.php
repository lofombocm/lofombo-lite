<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSendEMailPwdForgotJob;
use App\Models\Client;
use App\Models\Config;
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
            session()->flash('error', 'Veuillez attendre la configuration s\'il vous plait.');
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
            return back()->withInput()->with('error', 'No client with this email');
        }

        $id = Str::uuid()->toString();
        $currentTimestamp = Carbon::now();
        $configs = Config::all();
        $config = null;
        if (count($configs) > 0) {
            $config = $configs->first();
        }

        $pwdRecoverDuation = intval(env('PASSWORD_RECOVER_REQUEST_DURATION'));
        if (!($config === null)){
            $pwdRecoverDuation = $config->password_recovery_request_duration;
        }
        $expire_at = $currentTimestamp->addMinutes($pwdRecoverDuation);

        DB::table('password_recovery_requests')->insert(['id' => $id, 'email' => $request->get('email'), 'created_at' => $currentTimestamp, 'expire_at' => $expire_at]);

        $link = env('HOST_WEB_CLIENT_DOMAIN').'/client-password-forgot-form/'. $id ;
        $data = ['email' => $request->get('email'), 'name' => $client->name, 'passwordRecoveringUrl' => $link];

        //Mail::to($user->email)->send(new MailForPassordForgot($data));
        ProcessSendEMailPwdForgotJob::dispatch($data);

        $client->email = $request->get('email');
        $client->save();

        return back()->with('message', 'Vous recevrez un email a l\'adresse ' . $request->get('email') . ' contenant le lien vous permettant de creer un nouveau mot de passe.');
    }


    public function forgotPasswordForm($requestId): View
    {
        //$thiken = $this->route('token');
        return view('auth.passwords.client-recover-password', ['requestId' => $requestId]);
    }


    public function postForgotPasswordForm(Request $request){
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
        ]);
        $password_recovery_request = DB::table('password_recovery_requests')
            ->where([
                'id' => $request->get('requestid'),
            ])->first();

        if(!$password_recovery_request){
            return back()->withInput()->with('error', 'Aucune demande de redefinition mot de passe');
        }

        $now = Carbon::now();
        $expire_at = Carbon::parse($password_recovery_request->expire_at);
        if ($expire_at->isBefore($now)) {
            return back()->withInput()->with('error', 'Votre demande a expire le: ' . $expire_at);
        }

        Client::where('email', $password_recovery_request->email)->update(['password' => Hash::make($request->get('password'))]);
        return redirect('/auth/client')->with('message', 'Votre mot de passe a ete redefini avec succes!');
    }

}
