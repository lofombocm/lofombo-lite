<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltytransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class HomeController extends Controller
{
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
        if(Auth::check()){
            $configs = Config::all();
            if (count($configs) === 0){
                $configid = Str::uuid()->toString();
                $initial_loyalty_points = 0;
                $amount_per_point = 5000;
                $currency_name = 'FCFA';
                $levels = [['config' => $configid, 'name' => 'CLASSIC', 'point' => 20],
                    ['config' => $configid, 'name' => 'PREMIUM', 'point' => 30],
                    ['config' => $configid, 'name' => 'GOLD', 'point' => 50]];
                $index = 0;
                $birthdate_bonus_rate = 1;
                /*$classic_threshold = 50;
                $premium_threshold = 80;
                $gold_threshold = 120;*/
                $voucher_duration_in_month = 3;
                $password_recovery_request_duration = 60;
                $enterprise_name = "LOFOMBO";
                $enterprise_email = 'contact@gmail.com';
                $enterprise_phone = '0123456789';
                $enterprise_website = url('/');
                $enterprise_address = '';
                $enterprise_logo = asset('images/logo');

                $data = [
                    'id' => $configid,
                    'initial_loyalty_points' => $initial_loyalty_points,
                    'amount_per_point'=> $amount_per_point,
                    'currency_name' => $currency_name,
                    'levels' => json_encode($levels),
                    /*'classic_threshold' => $request->get('classic_threshold'),
                    'premium_threshold' => $request->get('premium_threshold'),
                    'gold_threshold' => $request->get('gold_threshold'),*/
                    'voucher_duration_in_month' => $voucher_duration_in_month,
                    'password_recovery_request_duration' => $password_recovery_request_duration,
                    'enterprise_name' => $enterprise_name,
                    'enterprise_email' => $enterprise_email,
                    'enterprise_phone' =>  $enterprise_phone,
                    'enterprise_website' => $enterprise_website,
                    'enterprise_address' => $enterprise_address = '',
                    'enterprise_logo' => $enterprise_logo,
                    'is_applicable' => true,
                    'defined_by' => Auth::user()->id,
                    'birthdate_bonus_rate' => $birthdate_bonus_rate,
                ];
                //dd($data);
                Config::create($data);

            }
            return view('home');
        }

        return redirect("auth")->withError('Opps! You do not have access');
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function logout(): RedirectResponse
    {
        Session::flush();
        Auth::logout();
        //return Redirect('login');
        return redirect()->route('authentification');
    }

    public function showLoyaltyTransactions(string $clientId)
    {
        $loyaltyAccount = Loyaltyaccount::where('holderid', $clientId)->first();
        return view('tx-list', ['txs' => Loyaltytransaction::where('loyaltyaccountid', $loyaltyAccount->id)->orderBy('created_at', 'desc')->get()]);
    }
}
