<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GuestController;
use App\Jobs\ProcessSendEMailUserInvitationJob;
use App\Jobs\ProcessSendEMailUserRegisteredJob;
use App\Models\Config;
use App\Models\Notification;
use App\Models\RegistratIoninvitation;
use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\UserFirstTimeConnection;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;


class RegisterSuperAdminController extends Controller
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
        $this->middleware('user-is-super-admin');
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
        ],[
            'name.required' => __('Le nom est obligatoire.'),
            'email.required' => __('L\'adresse E-Mail est obligatoire'),
            'email.email' => __('L\'adresse E-Mail n\'est pas valide'),
            'password.required' => __('Le mot de passe est obligatoire'),
            'password.min' => __('Le mot de passe doit avoir au moins 8 caractères'),
            'password.max' => __('Le mot de passe doit avoir au plus 20 caractères'),
            'password.confirmed' => __('Les mots de passe ne correspondent pas'),
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
            'username' => $data['username'],
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

    public function putRegistrationIndex(string $locale, string  $superAdminid): View
    {
        $superAdmin  = SuperAdmin::where('id', $superAdminid)->first();
        return view('auth.registration-update-super-admin', ['superadmin' => $superAdmin]);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            //'password' => 'required|string|min:8|max:20|confirmed',
            'is_admin' => 'required|string|in:on,off'
        ]);

        //dd($request->all());

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $config = Config::where('is_applicable', true)->first();
        if($config === null){
            $msg = 'Aucune configuration active trouvee';
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

        $users = User::where('is_admin', true)->get();

        if (count($users) >= 3 && $request->get('is_admin') === 'on') {
            session()->flash('error', 'Desole vous ne pouvez plus enregistrer d\'administrateur. ');
            return back()->withErrors(['error' => 'Desole vous ne pouvez plus enregistrer d\'administrateur. ']);
        }


        $request->merge([
            'password' => '12345678',
        ]);

        DB::beginTransaction();
        try {
            $user = $this->create($request->all());
            UserFirstTimeConnection::create([
                'id' => $user->id,
                'has_been_connected' => false,
            ]);


            $link = url('/' . GuestController::getApplicationLocal() .'/auth') ;

            $emaildata = ['email' => $request->get('email'), 'name' => $request->get('name'), 'login_url' => $link, 'enterprise' => $config->enterprise_name,];

            ProcessSendEMailUserRegisteredJob::dispatch($emaildata);

        }catch (\Exception $exception){
            DB::rollBack();
            session()->flash('error', $exception->getMessage());
            return back()->withErrors(['error' =>  $exception->getMessage()]);
        }

        DB::commit();
        //Auth::login($user);
        $msg = __("Utilisateur enregistré avec succès !");
        session()->flash('status', $msg);

        return back()->with('status', $msg);//->withSuccess('status', 'Great! You have Successfully Registered.');
    }

    public function getAllUser(){
        return view('auth.all-users', ['users' => User::all()]);
    }

    public function getAllUserForadministrationm(){
        $users = User::where('id', '!=', Auth::user()->id)->get();
        return view('auth.all-users-admin', ['users' => $users]);
    }

    public function removeOrAddToAdminRole(Request $request, string $userid): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'operation' => 'required|string|in:remove,add',
        ]);
        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }
        $user =User::where('id', $userid)->first();
        if ($user == null) {
            session()->flash('error', 'L\'Utilisateur n\'existe pas.');
            return back()->withErrors(['error' => 'L\'Utilisateur n\'existe pas.']);
        }
        $msg = '';
        if (trim($request->get('operation')) == 'remove') {
            $user->is_admin = false;
            $user->save();
            $msg = __("Utilisateur retiré avec succès.");
        }else{
            $users = User::where('is_admin', true)->get();
            if (count($users) >= 3) {
                session()->flash('error', __("Désolé vous ne pouvez plus ajouter d'administrateur."));
                return back()->withErrors(['error' => __("Désolé vous ne pouvez plus ajouter d'administrateur.")]);
            }
            $user->is_admin = true;
            $user->save();
            $msg = __("Utilisateur ajouté avec succès.");
        }

        session()->flash('status', $msg);
        return back()->with('status', $msg);
    }



    public function putRegistration(Request $request, string $locale, string $superadminid): RedirectResponse
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
            'email' => 'required|string|email|max:255',
            'telephone' => 'required|phone|max:255|min:8',
            //'is_admin' => 'required|string|in:on,off'
        ],[
            'telephone.required' => __('Le numéro de téléphone est obligatoire'),
            'telephone.phone' => __("Le numéro de téléphone est invalide"),
        ]);

        //session()->flash('error', $request->get('is_admin'));
        //return back()->withErrors(['error' => $request->get('is_admin')]);
        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            //session()->flash('request', json_encode($request->all()));
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        /*if ($request->filled('email')) {
            $validatorEmail = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|exists:users,email',
                //'is_admin' => 'required|string|in:on,off'
            ]);

            //session()->flash('error', $request->get('is_admin'));
            //return back()->withErrors(['error' => $request->get('is_admin')]);
            if($validatorEmail->fails()){
                session()->flash('error', $validatorEmail->errors()->first());
                //session()->flash('request', json_encode($request->all()));
                return redirect()->back()->withErrors(['error' => $validatorEmail->errors()->first()]);
            }
        }*/



        $superAdminVerif = SuperAdmin::where('email', $request->get('email'))->where('id', '!=', $superadminid)->first();
        $superAdmin = SuperAdmin::where('id', $superadminid)->first();
        if($superAdminVerif != null){
            if($superAdminVerif->id !== $superAdmin->id){
                $msg = 'E-Mail deja en utilisation. Veuillez choisir un autre e-mail.';
                session()->flash('error', $msg);
                //session()->flash('request', json_encode($request->all()));
                return back()->withErrors(['error' => $msg]);
            }
            //dd($userVerif);
        }

        $data = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'telephone' => $request->get('telephone'),
            //'username' => $request->get('username'),
            //'is_admin' => (env('ADMIN_NAME') == $request->get('name')) || ($request->get('is_admin') == 'on')
        ];

        $superAdmin->update($data);


        //Auth::login($user);
        $msg = __("Utilisateur enregistré avec succès !");
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
        $link = url('/' . GuestController::getApplicationLocal() .'/registration-invitations/'. $invitationId );


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

        $registrationInvitation = RegistratIoninvitation::create($data);

        $emaildata = ['email' => $request->get('email'), 'name' => $request->get('name'), 'invitation_url' => $link, 'enterprise' => $config->enterprise_name,];


        ProcessSendEMailUserInvitationJob::dispatch($emaildata);


        $message = ['Mme/M. ' . $request->get('name') . ' Vous etes invite a rejoindre le systeme de fidelite de ' . $config->enterprise_name . '.'];
        $donnee = ['email' => $request->get('email'), 'name' => $request->get('name'), 'clientLoginUrl' => $link, 'msg' => $message];

        $notifid = Str::uuid()->toString();
        $notifgenerator = '' . Auth::user()->id;
        $notifsubject = 'Invitation a rejoindre le systeme de fidelite de ' . $config->enterprise_name . '.';
        $notifsentat = Carbon::now();
        $notifbody = json_encode($message);
        $notifdata = json_encode($donnee);
        $notifsender = Auth::user()->name;
        $notifrecipient = $registrationInvitation->id;
        $notifsenderaddress = Auth::user()->email;
        $notifrecipientaddress = $registrationInvitation->email;
        $notifread = false;

        //dd($notifdata);
        Notification::create(
            [
                'id' => $notifid,
                'generator' => $notifgenerator,
                'subject' => $notifsubject,
                'sent_at' => $notifsentat,
                'body' => $notifbody,
                'data' => $notifdata,
                'sender' => $notifsender,
                'recipient' => $notifrecipient,
                'sender_address' => $notifsenderaddress,
                'recipient_address' => $notifrecipientaddress,
                'read' => $notifread,
            ]
        );

        $msg = __("Utilisateur invité avec succès");
        session()->flash('status', $msg);
        return back()->with('status', $msg);

    }



}
