<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\UserFirstTimeConnection;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegisterAdminController extends Controller
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
        //$this->middleware('auth');
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
            'password.required' => __('Le mot de passe est obligatoire.'),
            'email.email' => __('L\'adresse E-Mail n\'est pas valide'),
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

    public function postRegistration(Request $request): RedirectResponse
    {
        if (count(User::all()) !== 0) {
            session()->flash('error', 'Vous n\'etes pas autorise!');
            return back()->withErrors(['error' => 'Vous n\'etes pas autorise!']);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            //'password' => 'required|string|min:8|max:20|confirmed',
            'is_admin' => 'required|string|in:on,off'
        ],[
            'name.required' => __('Le nom est obligatoire.'),
            'username.exists' => __('Le système n\'a pas reconnu votre nom d\'utilisateur.'),
            'username.required' => __('Le nom d\'utilisateur est obligatoire.'),
            'email.required' => __('L\'adresse E-Mail est obligatoire'),
            'email.email' => __('L\'adresse E-Mail n\'est pas valide'),
        ]);

        //dd($request->all());

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        /*$config = Config::where('is_applicable', true)->first();
        if($config === null){
            $msg = 'Aucune configuration active trouvee';
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }*/

        $users = User::where('is_admin', true)->get();

        if (count($users) >= 3 && $request->get('is_admin') === 'on') {
            $msg = __('Désole vous ne pouvez plus enregistrer d\'administrateur.');
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
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


            //$link = url('').'/auth' ;

            //$emaildata = ['email' => $request->get('email'), 'name' => $request->get('name'), 'login_url' => $link, 'enterprise' => $config->enterprise_name,];

            //ProcessSendEMailUserRegisteredJob::dispatch($emaildata);

        }catch (\Exception $exception){
            DB::rollBack();
            session()->flash('error', $exception->getMessage());
            return back()->withErrors(['error' =>  $exception->getMessage()]);
        }

        DB::commit();
        //Auth::login($user);
        $msg = __('Utilisateur enregistré avec succès !');
        session()->flash('status', $msg);

        return back()->with('status', $msg);//->withSuccess('status', 'Great! You have Successfully Registered.');
    }

    protected function createSuperAdmin(array $data)
    {
        $id = Str::uuid()->toString();
        return SuperAdmin::create([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'telephone' => $data['telephone'],
            'password' => Hash::make($data['password']),
            'active' => true,
            'is_super_admin' => true
        ]);
    }

    public function postRegistrationSuperAdmin(Request $request)
    {
        if (count(SuperAdmin::all()) !== 0) {
            session()->flash('error', 'Vous n\'etes pas autorise!');
            return back()->withErrors(['error' => 'Vous n\'etes pas autorise!']);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:super_admins',
            'username' => 'required|string|max:255|unique:super_admins',
            'telephone' => 'required|string|max:255|min:8',
        ],[
            'name.required' => __('Le nom est obligatoire.'),
            'email.required' => __('L\'adresse E-Mail est obligatoire.'),
            'email.email' => __('L\'adresse E-Mail n\'est pas valide'),
            'username.required' => __('Le nom d\'utilisateur est obligatoire.'),
            'telephone.required' => __('Le numéro de téléphone est obligatoire')
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $superAdmin = SuperAdmin::where('is_super_admin', true)->get();

        if (count($superAdmin) >= 2) {
            $msg = __('Désolé vous ne pouvez plus enregistrer de super administrateur. ');
            session()->flash('error', '');
            return back()->withErrors(['error' => $msg]);
        }

        $password = '';
        $secret = fopen('secret', 'r');
        $password = rtrim(fgets($secret));
        fclose($secret);
        //dd($password);
        $filename = "./secret"; // Replace with the actual path to your file
        if (file_exists($filename)) {
            if (!unlink($filename)) {
                session()->flash('error', __('Une erreur est survenue, reessayez de nouveau.'));
                return back()->withErrors(['error' => __('Une erreur est survenue, reessayez de nouveau.')]);
            }
        } else {
            session()->flash('error', __('Une erreur est survenue, reessayez de nouveau.'));
            return back()->withErrors(['error' => __('Une erreur est survenue, reessayez de nouveau.')]);
        }

        $request->merge([
            'password' => $password,
        ]);

        return $this->createSuperAdmin($request->all());
    }


}
