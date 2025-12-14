<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GuestController;
use App\Models\Config;
use App\Models\PasswordRecoveryRequest;
use App\Models\User;
//use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Jobs\ProcessSendEMailPwdForgotJob;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    //use SendsPasswordResetEmails;

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function forgotPassword(): View
    {
        //$thiken = $this->route('token');
        return view('auth.passwords.email');
    }


    /**
     * Write code on Method
     *
     * @return response()
     */

    public function postForgotPassword(Request $request){
        //return json_encode($request);
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email',
        ],[
            'email.required' => __('L\'adresse email est obligatoire.'),
            'email.email' => __('L\'adresse email n\'est pas valide.'),
        ]);

        if ($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            //session()->flash('request', json_encode($request->all()));
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }


        $user = User::where('email', $request->email)->first();
        if(!$user){
            session()->flash('error', 'No user with this email');
            return redirect()->back()->withErrors(['error'=> 'No user with this email']);

        }
//password_recovery_request

        $id = Str::uuid()->toString();
        $currentTimestamp = Carbon::now();

        $config = Config::where('is_applicable', true)->first();

        if ($config == null) {
            session()->flash('error', 'Aucune configuration du systeme n\'est definie.');
            return back()->withInput()->with('error', 'Aucune configuration du systeme n\'est definie.');
        }

        $pwdRecoverDuation = intval(strval($config->password_recovery_request_duration));

        $expire_at = $currentTimestamp->addMinutes($pwdRecoverDuation);
        //DB::table('password_recovery_requests')->insert(['id' => $id, 'email' => $user->email, 'created_at' => $currentTimestamp, 'expire_at' => $expire_at]);

        $pwdRequest = PasswordRecoveryRequest::create(
            [
                'id' => $id,
                'email' => $user->email,
                'telephone' => $request->get('telephone'),
                'expire_at' => $expire_at
            ]);
        $link = url('/' . GuestController::getApplicationLocal() .'/password-forgot-form/'. $id) ;
        $data = ['email' => $user->email, 'name' => $user->name, 'passwordRecoveringUrl' => $link];

        //Mail::to($user->email)->send(new MailForPassordForgot($data));
        ProcessSendEMailPwdForgotJob::dispatch($data);
        session()->flash('status', 'Vous recevrez un email a l\'adresse ' . $user->email . ' contenant le lien vous permettant de creer un nouveau mot de passe.');
        return redirect()->back()->with('status', 'Vous recevrez un email a l\'adresse ' . $user->email . ' contenant le lien vous permettant de creer un nouveau mot de passe.');
    }


    public function forgotPasswordForm(string $locale, string $requestId): View
    {
        //$thiken = $this->route('token');
        $req = PasswordRecoveryRequest::where('id', $requestId)->first();
        $user = User::where('email', $req->email)->first();
        return view('auth.passwords.recover-password', ['requestId' => $requestId, 'user' => $user]);
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
            session()->flash('error', 'Aucune demande de redeinir le mot de passe');
            return redirect()->back()->withInput()->with('error', 'Aucune demande de redeinir le mot de passe');
        }

        $now = Carbon::now();
        $expire_at = Carbon::parse($password_recovery_request->expire_at);
        if ($expire_at->isBefore($now)) {
            session()->flash('error', 'Votre demande a expire le: ' . $expire_at);
            return back()->withInput()->with('error', 'Votre demande a expire le: ' . $expire_at);
        }

        User::where('email', $password_recovery_request->email)->update(['password' => Hash::make($request->password)]);
        session()->flash('status', 'Votre mot de passe a ete redefini avec succes!');
        return redirect('/auth')->with('status', 'Votre mot de passe a ete redefini avec succes!');
    }

}
