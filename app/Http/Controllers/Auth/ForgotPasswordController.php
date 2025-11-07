<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\User;
//use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $request->validate([
            'email' => ['required', 'email'/*, 'exists:users'*/],
        ]);


        $user = User::where('email', $request->email)->first();
        if(!$user){
            return back()->withInput()->with('error', 'No user with this email');

        }
//password_recovery_request

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
        DB::table('password_recovery_requests')->insert(['id' => $id, 'email' => $user->email, 'created_at' => $currentTimestamp, 'expire_at' => $expire_at]);

        $link = env('HOST_WEB_CLIENT_DOMAIN').'/password-forgot-form/'. $id ;
        $data = ['email' => $user->email, 'name' => $user->name, 'passwordRecoveringUrl' => $link];

        //Mail::to($user->email)->send(new MailForPassordForgot($data));
        ProcessSendEMailPwdForgotJob::dispatch($data);

        return back()->with('message', 'Vous recevrez un email a l\'adresse ' . $user->email . ' contenant le lien vous permettant de creer un nouveau mot de passe.');
    }


    public function forgotPasswordForm($requestId): View
    {
        //$thiken = $this->route('token');
        return view('auth.passwords.recover-password', ['requestId' => $requestId]);
    }


    public function postForgotPasswordForm(Request $request){
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
        ]);
        $password_recovery_request = DB::table('password_recovery_requests')
            ->where([
                'id' => $request->requestid,
            ])->first();

        if(!$password_recovery_request){
            return back()->withInput()->with('error', 'Aucune demande de redeinir le mot de passe');
        }

        $now = Carbon::now();
        $expire_at = Carbon::parse($password_recovery_request->expire_at);
        if ($expire_at->isBefore($now)) {
            return back()->withInput()->with('error', 'Votre demande a expire le: ' . $expire_at);
        }

        $user = User::where('email', $password_recovery_request->email)->update(['password' => Hash::make($request->password)]);
        return redirect('/auth')->with('message', 'Votre mot de passe a ete redefini avec succes!');
    }

}
