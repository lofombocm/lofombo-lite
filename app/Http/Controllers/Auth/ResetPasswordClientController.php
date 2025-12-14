<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ResetPasswordClientController extends Controller
{
    public function resetPassword() : View{
        return view('auth.client.reset');
    }

    public function postResetPassword(Request $request)  {
        $validator = Validator::make($request->all(), [
            'telephone' => 'required|string|max:255|exists:clients,telephone',
            'currentpassword' => 'required|string|min:8|max:20',
            'password' => 'required|string|min:8|max:20|confirmed',
        ],[
            'telephone.required' => __('Le numéro de téléphone est obligatoire'),
            'telephone.exists' => __('Numéro de téléphone non reconnu'),
            'currentpassword.required' => __('Le mot de passe actuel est obligatoire'),
            'password.required' => __('Le mot de passe est obligatoire'),
            'password:max' => __('Le mot de passe doit avoir au plus 20 caractères'),
            'password.min' => __('Le mot de passe doit avoir au moins 8 caractères'),
            'password.confirmed' => __('Les mots de passe ne correspondent pas'),
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }


       /* $request->validate([
            'telephone' => 'required|string|max:255|exists:clients,telephone',
            'currentpassword' => 'required|string',
            'password' => 'required|string|min:8|max:20|confirmed',
        ]);*/
        /*$updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])->first();

        if(!$updatePassword){
            return back()->withInput()->with('error', 'Invalid token!');
        }*/

        $credentials = ['telephone' => $request->get('telephone'), 'password' => $request->get('currentpassword') ];//$request->only('email', 'current-password');
        if (!Auth::guard('client')->attempt($credentials)) {

            $msg = __("Mot de passe actuel invalide");
            session()->flash('error',  $msg);
            return back()->withInput()->with('error', $msg);
        }

        Client::where('telephone', $request->get('telephone'))->update(['password' => Hash::make($request->get('password'))]);
        //DB::table('password_resets')->where(['email'=> $request->email])->delete();
        Session::flush();
        Auth::guard('client')->logout();
        //return Redirect('login');
        $msg = __('Mot de passe modifié avec succès !');
        session()->flash('status', $msg);
        return redirect()->route('authentification.client')->with('message', $msg);
        //return redirect('/login')->with('message', 'Votre mot de passe a ete modifie avec succes!');
    }

}
