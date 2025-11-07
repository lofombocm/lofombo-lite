<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSendEMailUserInvitationJob;
use App\Models\Config;
use App\Models\RegistratIoninvitation;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'active' => true,
            'is_admin' => (env('ADMIN_NAME') == $data['name']) || ($data['is_admin'] == 'on')
        ]);
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registration(): View
    {
        return view('auth.register');
    }

    public function putRegistrationIndex(int $userid): View
    {
        $user  = User::where('id', $userid)->first();
        return view('auth.registration-update', ['user' => $user]);
    }



    public function registrationInvitation(): View
    {
        return view('auth.registration-invitation');
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postRegistration(Request $request): RedirectResponse
    {

        /*$request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
            'is_admin' => ['required', 'string'],
        ])*/

        //return $request;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:20|confirmed',
            'is_admin' => 'required|string|in:on,off'
        ]);

        //session()->flash('error', $request->get('is_admin'));
        //return back()->withErrors(['error' => $request->get('is_admin')]);
        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            //session()->flash('request', json_encode($request->all()));
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $user = $this->create($request->all());

        //Auth::login($user);
        $msg = 'Bien! l\'utilisateur ' . $request->get('name') . ' a ete enregistre avec succes.';
        session()->flash('status', $msg);

        return back()->with('status', $msg);//->withSuccess('status', 'Great! You have Successfully Registered.');
    }

    public function putRegistration(Request $request, string $userid): RedirectResponse
    {

        /*$request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
            'is_admin' => ['required', 'string'],
        ])*/

        //return $request;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            //'email' => 'required|string|email|max:255|unique:users',
            'is_admin' => 'required|string|in:on,off'
        ]);

        //session()->flash('error', $request->get('is_admin'));
        //return back()->withErrors(['error' => $request->get('is_admin')]);
        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            //session()->flash('request', json_encode($request->all()));
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $userVerif = User::where('email', $request->get('email'))->first();
        $user = User::where('id', $userid)->first();
        if($userVerif != null){
            if($userVerif->id !== $user->id){
                $msg = 'The email has already been taken.';
                session()->flash('error', $msg);
                //session()->flash('request', json_encode($request->all()));
                return back()->withErrors(['error' => $msg]);
            }
            //dd($userVerif);
        }

        $data = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'is_admin' => (env('ADMIN_NAME') == $request->get('name')) || ($request->get('is_admin') == 'on')
        ];

        $user->update($data);


        //Auth::login($user);
        $msg = 'Bien! l\'utilisateur ' . $user->name . ' a ete modifier avec succes.';
        session()->flash('status', $msg);

        return back()->with('status', $msg);//->withSuccess('status', 'Great! You have Successfully Registered.');
    }




    public function postRegistratioInvitation(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users',
            'is_admin' => 'required|string|in:on,off'
        ]);
        if($validator->fails()){
            $msg = $validator->errors()->first();
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

        $config = Config::where('is_applicable', true)->first();
        if($config === null){
            $msg = 'Aucune configuration active trouvee';
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

        $invitationId = Str::uuid()->toString();
        $invitationDuration = intval(env('USER_INVITATION_DURATION_IN_DAY'));
        $expirationdate = Carbon::now()->addMinutes(1440 * $invitationDuration);
        $link = env('HOST_WEB_CLIENT_DOMAIN').'/registration-invitations/'. $invitationId ;

        ///registration/invitations/
        $data = [
            'id' => $invitationId,
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'invited_by' => Auth::user()->id,
            'enterprise_name' => $config->enterprise_name,
            'invited_at' => Carbon::now(),
            'expire_at' => $expirationdate,
            'invitation_url' => $link,
            'active' => true,
            'is_admin'=> (env('ADMIN_NAME') == $request->get('name')) || ($request->get('is_admin') === 'on')
        ];

        RegistratIoninvitation::create($data);

        $emaildata = ['email' => $request->get('email'), 'name' => $request->get('name'), 'invitation_url' => $link, 'enterprise' => $config->enterprise_name,];


        ProcessSendEMailUserInvitationJob::dispatch($emaildata);

        $msg = 'Bien! l\'utilisateur ' . $request->get('name') . ' a ete invite avec succes.';
        session()->flash('status', $msg);
        return back()->with('status', $msg);

    }



}
