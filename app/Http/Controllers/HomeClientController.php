<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSendEMailVoucherGeneratedJob;
use App\Jobs\ProcessSendEMailVoucherUsageCodeJob;
use App\Models\Client;
use App\Models\Config;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltytransaction;
use App\Models\Reward;
use App\Models\Voucher;
use App\Models\VoucherUsageCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\LaravelPdf\Facades\Pdf;

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

        return redirect("auth/client")->withError('Opps! You do not have access');
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
        if (!$request->filled('day') || !$request->filled('month')){
            //$secret = "12345678";
        }else{
            //dd($request->all());
            $validatorBirthdate = Validator::make($request->all(), [
                'day' => 'string|in:01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31',
                'month' => 'string|in:01,02,03,04,05,06,07,08,09,10,11,12',
                //'year' => 'integer|between:1900,'.date('Y'),
            ]);
            if($validatorBirthdate->fails()){
                session()->flash('error', $validatorBirthdate->errors()->first());
                return back()->withErrors(['error' => $validatorBirthdate->errors()->first()]);
            }

            if (!$request->filled('year')){
                $year = 1900;
            }else{
                $year = intval(trim($request->get('year')));
                if (!($year >= 1900 && $year <= Carbon::now()->year)){
                    $year = 1900;
                }
            }
            $birthdate = $year . '-'.trim($request->get('month')).'-'.trim($request->get('day'));

            //$birthdate = $request->get('year').'-'.$request->get('month').'-'.$request->get('day');
            //$birthdateFormatedArr = explode('-', $birthdate);
            //$secret = $birthdateFormatedArr[2] . $birthdateFormatedArr[1] . $birthdateFormatedArr[0];
            $client->birthdate = $birthdate;
        }

        if ($request->filled('gender')){
            $validatorGender = Validator::make($request->all(), [
                'gender' => 'string|in:MONSIEUR,MADAME,MADEMOISELLE',
            ]);
            if($validatorGender->fails()){
                session()->flash('error', $validatorGender->errors()->first());
                return back()->withErrors(['error' => $validatorGender->errors()->first()]);
            }
            $client->gender = $request->get('gender');
        }

        if ($request->filled('quarter')){
            $validatorQuarter = Validator::make($request->all(), [
                'quarter' => 'string|max:255',
            ]);
            if($validatorQuarter->fails()){
                session()->flash('error', $validatorQuarter->errors()->first());
                return back()->withErrors(['error' => $validatorQuarter->errors()->first()]);
            }
            $client->quarter = $request->get('quarter');
        }


        if ($request->filled('city')){
            $validatorCity = Validator::make($request->all(), [
                'city' => 'string|max:255',
            ]);
            if($validatorCity->fails()){
                session()->flash('error', $validatorCity->errors()->first());
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

    public function showLoyaltyTransactionsClientSearch(Request $request, string $clientId){
        //dd($clientId);
        $loyaltyAccount = Loyaltyaccount::where('holderid', $clientId)->first();
        $q = $request->get('q');
        /*$txs = Loyaltytransaction::where('transactiondetail','LIKE','%'.$q.'%')
            ->orWhere('transactiontype','LIKE','%'.strtoupper($q).'%')
            ->orWhere('reference','LIKE','%'.$q.'%')
            ->orWhere('date','LIKE','%'.$q.'%')
            ->orderBy('created_at', 'desc')
            ->get();*/

        $all = Loyaltytransaction::where('loyaltyaccountid', $loyaltyAccount->id)->orderBy('created_at', 'desc')->get();
        $txs = [];
        foreach ($all as $tx) {
            $date = Carbon::parse($tx->date)->format('d-m-Y H:i:s');

            if (str_contains(strtolower($tx->transactiondetail), strtolower($q))
                || str_contains(strtolower($tx->transactiontype), strtolower($q))
                || str_contains(strtolower($tx->reference), strtolower($q))
                || str_contains(strtolower($date), strtolower($q))
                || str_contains(strtolower(sprintf('%lf', $tx->amount)), strtolower($q))
                || str_contains(strtolower(sprintf('%lf', $tx->point)), strtolower($q))){
                array_push($txs, $tx);
            }
        }

        return view('tx-list-client', ['txs' => $txs, 'q' => $q, 'clientid' => $clientId]);
        //return view('welcome')->withDetails($user)->withQuery ( $q );
        //else return view ('welcome')->withMessage('No Details found. Try to search again !');
    }

    public function showLoyaltyTransactionsAllPerClient(string $clientId)
    {
        $loyaltyAccount = Loyaltyaccount::where('holderid', $clientId)->first();
        return view('tx-list-client',
            ['txs' => Loyaltytransaction::where('loyaltyaccountid', $loyaltyAccount->id)->orderBy('created_at', 'desc')->get(), 'clientid' => $clientId]);
    }

    public function downloadVoucher(string $voucherId){

        $voucher = Voucher::where('id', $voucherId)->first();
        $client = Client::where('id', $voucher->clientid)->first();

        return Pdf::view('reports-templates.vouchers-templates.voucher-download-template', ['voucher' => $voucher, 'client' => $client])
            ->format('a4')
            ->save($voucher->serialnumber . '.pdf');

        //$config = Config::where('is_applicable', true)->first();
        /*$voucher = Voucher::where('id', $voucherId)->first();
        $client = Client::where('id', $voucher->clientid)->first();
        $pdf = PDF::loadView('mailtemplates.vouchers-templates.pdf', ['voucher' => $voucher, 'client' => $client]);
        return $pdf->download($voucher->serialnumber . '.pdf');*/
    }

    public function resendUsageCodeForm(string $voucherId){
        if (!Auth::guard('client')->check()){
            session()->flash('error', 'Veillez-vous connecter s\'il vous plait.');
            return back()->withErrors(['error' =>  'Veillez-vous connecter s\'il vous plait.']);
        }

        $client = Auth::guard('client')->user();

        $voucher = Voucher::where('id', $voucherId)->first();
        if ($voucher == null){
            session()->flash('error', 'Bon inexistant.');
            return back()->withErrors(['error' =>  'Bon inexistant.']);
        }
        return view('client.resend-usage-code', ['voucherid' => $voucherId]);

        //$config = Config::where('is_applicable', true)->first();
        /*$voucher = Voucher::where('id', $voucherId)->first();
        $client = Client::where('id', $voucher->clientid)->first();
        $pdf = PDF::loadView('mailtemplates.vouchertemplates.pdf', ['voucher' => $voucher, 'client' => $client]);
        return $pdf->download($voucher->serialnumber . '.pdf');*/
    }

    public function resendUsageCode(Request $request, string $voucherId)
    {
        if (!Auth::guard('client')->check()){
            session()->flash('error', 'Veillez-vous connecter s\'il vous plait.');
            return back()->withErrors(['error' =>  'Veillez-vous connecter s\'il vous plait.']);
        }

        $client = Auth::guard('client')->user();

        $validator = Validator::make($request->all(), [
            'serialnumber' => 'required|string|max:255|exists:vouchers,serialnumber',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|max:20|min:8',
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $voucher = Voucher::where('id', $voucherId)->first();
        if ($voucher == null){
            session()->flash('error', 'Bon inexistant.');
            return back()->withErrors(['error' =>  'Bon inexistant.']);
        }

        if ($voucher->serialnumber != $request->get('serialnumber')){
            session()->flash('error', 'Le numero de serie est incorrect.');
            return back()->withErrors(['error' =>  'Le numero de serie est incorrect.']);
        }

        $request->merge([
            'telephone' => $client->telephone,
        ]);
        $credentials = $request->only('telephone', 'password');
        if (!Auth::guard('client')->attempt($credentials)) {
            session()->flash('error', 'Le mot de passe est incorrect.');
            return back()->withErrors(['error' => 'Le mot de passe est incorrect.']);
        }

        $voucher_usage_codes = Voucherusagecode::where('voucherid', $voucherId)->orderBy('created_at', 'desc')->get();
        if (count($voucher_usage_codes) == 0){
            session()->flash('error', 'Quelque chose d\'annormale est survenue.');
            return back()->withErrors(['error' => 'Quelque chose d\'annormale est survenue.']);
        }

        $voucher_usage_code = $voucher_usage_codes[0];

        $link = url('').'/client/'. $client->id . '/vouchers' ;
        $config = Config::where('is_applicable', true)->first();
        $message = [$client->gender . ' ' . $client->name . ', vous avez demander au systeme de fidelite de ' . $config->enterprise_name .
            ' de vous envoyer un code d\'utilisation d\'un bon. Si jamais vous n\'avez pas fait cette demande, nous vous prions de bien vouloir ignorer ce message.'];
        $emaildata = ['email' =>trim($request->get('email')), 'name' => $client->name, 'clientLoginUrl' => $link, 'msg' => $message,
            'code' => decrypt($voucher_usage_code->code)];
        //dd($emaildata);
        ProcessSendEMailVoucherUsageCodeJob::dispatch($emaildata);

        $msg = 'Le code demande a ete envoye au mail ' . $emaildata['email'] . '.';
        session()->flash('status', $msg);
        return redirect()->back()->with('status', 'Bon genere avec succes.');
    }


    public function showLoyaltyTransactionsDetails(string $txid)
    {
        $tx = Loyaltytransaction::where('id', $txid)->first();
        $client = Client::where('id', $tx->clientid)->first();
        $rewards = Reward::where('active', true)->get();
        return view('tx-client-details', ['tx' => $tx, 'client' => $client, 'rewards' => $rewards]);
    }



}
