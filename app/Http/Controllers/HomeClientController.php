<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class HomeClientController extends Controller
{
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    /* public function index()
     {
         return view('home');
     }*/


    /**
     * Write code on Method
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View()
     */
    public function dashboard()
    {
        if(Auth::guard('client')->check()){
            return view('home-client');
        }

        return redirect("auth/clent")->withError('Opps! You do not have access');
    }

    public function updateClientForm(string $clientid)
    {
        if(Auth::guard('client')->check()){
            if ($clientid !== Auth::guard('client')->user()->id) {
                return redirect("auth/clent")->withError('Opps! You do not have access');
            }
            return view('client.update-client-form', ['client' =>  Auth::guard('client')->user()]);
        }

        return redirect("auth/clent")->withError('Opps! You do not have access');
    }


    public function updateClient(Request $request, string $clientId){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'telephone' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $client = Client::where('id', $clientId)->first();
        if(!$client){
            return back()->withErrors(['error' => 'Client introuvable']);
        }

        $otherClient = Client::where('telephone', $request->get('telephone'))->first();
        if(!($otherClient === null) && $otherClient->id != $clientId){
            return back()->withErrors(['error' => 'Numero de telephone deja utilise par le client ' . $otherClient->name . '.']);
        }

        $client->name = $request->get('name');
        $client->telephone = $request->get('telephone');

        if ($request->filled('email')){
            $validatorEmail = Validator::make($request->all(), [
                'email' => 'string|email|max:255',
            ]);
            if($validatorEmail->fails()){
                return back()->withErrors(['error' => $validatorEmail->errors()->first()]);
            }
            $client->email = $request->get('email');
        }

        $secret = null;
        $birthdate = "";
        if (!$request->filled('day') || !$request->filled('month') || !$request->filled('year')){
            //$secret = "12345678";
        }else{

            $validatorBirthdate = Validator::make($request->all(), [
                'day' => 'string|in:01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31',
                'month' => 'string|in:01,02,03,04,05,06,07,08,09,10,11,12',
                'year' => 'integer|between:1900,'.date('Y'),
            ]);
            if($validatorBirthdate->fails()){
                return back()->withErrors(['error' => $validatorBirthdate->errors()->first()]);
            }
            $birthdate = $request->get('year').'-'.$request->get('month').'-'.$request->get('day');
            //$birthdateFormatedArr = explode('-', $birthdate);
            //$secret = $birthdateFormatedArr[2] . $birthdateFormatedArr[1] . $birthdateFormatedArr[0];
            $client->birthdate = $birthdate;
        }

        if ($request->filled('gender')){
            $validatorGender = Validator::make($request->all(), [
                'gender' => 'string|in:MONSIEUR,MADAME,MADEMOISELLE',
            ]);
            if($validatorGender->fails()){
                return back()->withErrors(['error' => $validatorGender->errors()->first()]);
            }
            $client->gender = $request->get('gender');
        }

        if ($request->filled('quarter')){
            $validatorQuarter = Validator::make($request->all(), [
                'quarter' => 'string|max:255',
            ]);
            if($validatorQuarter->fails()){
                return back()->withErrors(['error' => $validatorQuarter->errors()->first()]);
            }
            $client->quarter = $request->get('quarter');
        }


        if ($request->filled('city')){
            $validatorCity = Validator::make($request->all(), [
                'city' => 'string|max:255',
            ]);
            if($validatorCity->fails()){
                return back()->withErrors(['error' => $validatorCity->errors()->first()]);
            }
            $client->city = $request->get('city');
        }

        $client->save();

        $msg =  'Bien! Vous avez ajourne le client ' . $client->name . ' avec succes.';
        session()->flash('status', $msg);
        return redirect()->back()->with('status', $msg);
        //return redirect("home");//->withSuccess(['status' => 'Great! You have Successfully Registered.', 'client' => $client]);
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function logout(): RedirectResponse
    {
        Session::flush();
        Auth::guard('client')->logout();
        //return Redirect('login');
        return redirect()->route('authentification.client');
    }
}
