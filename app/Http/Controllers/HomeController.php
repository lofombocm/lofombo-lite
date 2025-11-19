<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSendEMailCampaignJob;
use App\Models\Client;
use App\Models\Config;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltytransaction;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;

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

    public function sendBulkMessageForm(){
        /*$normalClients = Client::where('active', true)->whereNotNull('email')->orderBy('created_at', 'desc')->get();
        $clientWithoutEmail = Client::whereNull('email')->orderBy('created_at', 'desc')->get();*/
        return view('notification.send-bulk-message-form'/*,['clientWithEmail'=>$normalClients,'clientWithoutEmail'=>$clientWithoutEmail]*/);
    }

    public function sendBulkMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'smschanel' => 'required|string|in:off,on',
            'emailchanel' => 'required|string|in:off,on',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:10000',
        ]);

        //dd($request->all());

        if ($validator->fails()) {
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        if ($request->get('emailchanel') == 'on') {
            $normalClients = Client::where('active', true)->whereNotNull('email')->orderBy('created_at', 'desc')->get();
            $emails = [];
            foreach ($normalClients as $client) {
                array_push($emails, ['email' => $client->email, 'name' => $client->name]);
            }
            //dd($emails);
            ProcessSendEMailCampaignJob::dispatch(['subject' => $request->get('subject'), 'message' => $request->get('message'), 'recipients' => $emails, 'sender' => Auth::user()->id]);
        }
        if ($request->get('smschanel') == 'on') {
            $normalClients = Client::where('active', true)->orderBy('created_at', 'desc')->get();
            ///TODO SEND SMS BULK MESSAGE
        }

        session()->flash('status', 'Message envoye avec succes');
        return back()->with(['success' => 'Message envoye avec succes']);
    }

    /**
     * Write code on Method
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View()
     */
    public function dashboard()
    {
        if(Auth::check()){
            if (Auth::user()->is_admin){
                return redirect()->to('/reports')->withSuccess('status', 'OK');
            }else{
                return redirect()->to('/home/purchases')->withSuccess('status', 'You have Successfully loggedin');
            }
        }

        return view('auth.login');

        /*if(Auth::check()){
            $configs = Config::all();
            if (count($configs) === 0){
                $configid = Str::uuid()->toString();
                $initial_loyalty_points = 0;
                $amount_per_point = 5000;
                $currency_name = 'FCFA';
                $classicid = Str::uuid()->toString();
                $premiumid = Str::uuid()->toString();
                $goldid = Str::uuid()->toString();
                $levels = [
                    ['id' => $classicid, 'config' => $configid, 'name' => 'CLASSIC', 'point' => 20],
                    ['id' => $premiumid, 'config' => $configid, 'name' => 'PREMIUM', 'point' => 75],
                    ['id' => $goldid, 'config' => $configid, 'name' => 'GOLD', 'point' => 200]
                ];
                $index = 0;
                $birthdate_bonus_rate = 1;
                $voucher_duration_in_month = 3;
                $password_recovery_request_duration = 60;
                $enterprise_name = "LOFOMBO";
                $enterprise_email = 'contact@gmail.com';
                $enterprise_phone = '0123456789';
                $enterprise_website = url('/');
                //$enterprise_address = '';
                $enterprise_logo = 'images/logo.png';

                $data = [
                    'id' => $configid,
                    'initial_loyalty_points' => $initial_loyalty_points,
                    'amount_per_point'=> $amount_per_point,
                    'currency_name' => $currency_name,
                    'levels' => json_encode($levels),
                    'voucher_duration_in_month' => $voucher_duration_in_month,
                    'password_recovery_request_duration' => $password_recovery_request_duration,
                    'enterprise_name' => $enterprise_name,
                    'enterprise_email' => $enterprise_email,
                    'enterprise_phone' =>  $enterprise_phone,
                    'enterprise_website' => $enterprise_website,
                    'enterprise_address' =>  '',
                    'enterprise_logo' => $enterprise_logo,
                    'is_applicable' => true,
                    'defined_by' => Auth::user()->id,
                    'birthdate_bonus_rate' => $birthdate_bonus_rate,
                ];
                //dd($data);
                Config::create($data);

            }
            return view('home');
        }*/

        //return redirect("auth")->withError('Opps! You do not have access');


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

    public function showClients()
    {
        $clients = Client::all();
        return view('client.list', ['clients' => $clients]);
    }

    public function showLoyaltyTransactions(string $clientId)
    {
        $loyaltyAccount = Loyaltyaccount::where('holderid', $clientId)->first();
        return view('tx-list',
            ['txs' => Loyaltytransaction::where('loyaltyaccountid', $loyaltyAccount->id)->orderBy('created_at', 'desc')->get(),'clientid' => $clientId]);
    }

    public function showLoyaltyTransactionsClientSearch(Request $request, string $clientId){
        //dd($clientId);
        $loyaltyAccount = Loyaltyaccount::where('holderid', $clientId)->first();
        $q = $request->get('q');

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

        return view('tx-list', ['txs' => $txs, 'q' => $q, 'clientid' => $clientId]);
        //return view('welcome')->withDetails($user)->withQuery ( $q );
        //else return view ('welcome')->withMessage('No Details found. Try to search again !');
    }


    public function showLoyaltyTransactionsAll()
    {
        return view('tx-list-all', ['txs' => Loyaltytransaction::orderBy('created_at', 'desc')->get()]);
    }

    public function showLoyaltyTransactionsDetails(string $txid)
    {
        $tx = Loyaltytransaction::where('id', $txid)->first();
        $client = Client::where('id', $tx->clientid)->first();
        return view('tx-details', ['tx' => $tx, 'client' => $client]);
    }



    public function showLoyaltyTransactionsSearch(Request $request){
        $q = $request->get('q');
        /*$txs = Loyaltytransaction::where('transactiondetail','LIKE','%'.$q.'%')
            ->orWhere('transactiontype','LIKE','%'.strtoupper($q).'%')
            ->orWhere('reference','LIKE','%'.$q.'%')
            ->orWhere('date','LIKE','%'.$q.'%')
            ->orderBy('created_at', 'desc')
            ->get();*/

        $all = Loyaltytransaction::orderBy('created_at', 'desc')->get();
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

        return view('tx-list-all', ['txs' => $txs, 'q' => $q]);
            //return view('welcome')->withDetails($user)->withQuery ( $q );
        //else return view ('welcome')->withMessage('No Details found. Try to search again !');
    }

    public function reportPage()
    {
        if(Auth::check()){
            $configs = Config::all();
            if (count($configs) === 0){
                $configid = Str::uuid()->toString();
                $initial_loyalty_points = 0;
                $amount_per_point = 5000;
                $currency_name = 'FCFA';
                $classicid = Str::uuid()->toString();
                $premiumid = Str::uuid()->toString();
                $goldid = Str::uuid()->toString();
                $levels = [
                    ['id' => $classicid, 'config' => $configid, 'name' => 'CLASSIC', 'point' => 20],
                    ['id' => $premiumid, 'config' => $configid, 'name' => 'PREMIUM', 'point' => 75],
                    ['id' => $goldid, 'config' => $configid, 'name' => 'GOLD', 'point' => 200]
                ];
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
                //$enterprise_address = '';
                $enterprise_logo = 'images/logo.png';

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
                    'enterprise_address' =>  '',
                    'enterprise_logo' => $enterprise_logo,
                    'is_applicable' => true,
                    'defined_by' => Auth::user()->id,
                    'birthdate_bonus_rate' => $birthdate_bonus_rate,
                ];
                //dd($data);
                Config::create($data);

            }
            //return view('home');
        }
        //$txs = Loyaltytransaction::all();
        $config = Config::where('is_applicable', true)->first();
        return view('reports-menu', ['config' => $config]);
    }

    public function reportTxs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tx' => 'required|string|in:ALL,PURCHASE_REGISTRATION,VOUCHER_GENERATION,ACCOUNT_INITIALIZATION',
            'period' => 'required|string|in:ALL,MONTHLY,QUATERLY,BIYEARLY,YEARLY',
        ]);


        if ($validator->fails()) {
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }
        $config = Config::where('is_applicable', true)->first();

        $txType = '';// $request->get('tx')=='ALL'?'':'PURCHASE_REGISTRATION';
        if (trim($request->get('tx')) == 'ALL') {
            $txType = '';
        }elseif (trim($request->get('tx')) == 'PURCHASE_REGISTRATION') {
            $txType = 'ENREGISTREMENT ACHAT';
        }elseif (trim($request->get('tx')) == 'VOUCHER_GENERATION') {
            $txType = 'GENERATION DE BON';
        }elseif (trim($request->get('tx')) == 'ACCOUNT_INITIALIZATION') {
            $txType = 'INITIALISATION COMPTE CLIENT';
        }

        if ($request->get('period') == 'ALL'){
            $txs = Loyaltytransaction::where('transactiontype', 'LIKE', '%' . $txType . '%')->orderBy('created_at', 'desc')->get();
            return Pdf::view('reports-templates.txs-templates.txs-template',
                ['txs' => $txs, 'config' => $config])
                ->format('a4')
                ->save('txs.pdf');
        }elseif ($request->get('period') == 'MONTHLY'){
            $aMonthLater = Carbon::now()->startOfMonth()->subMonth();
            $startOfCurrentMonth = Carbon::now()->startOfMonth();
            $txs  = Loyaltytransaction::where('transactiontype', 'LIKE', '%' . $txType . '%')->whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
            return Pdf::view('reports-templates.txs-templates.txs-template',
                ['txs' => $txs, 'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config])
                ->format('a4')
                ->save('txs-from-' . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '.pdf');
        }elseif ($request->get('period') == 'QUATERLY'){
            $_3MonthLater = Carbon::now()->startOfMonth()->subMonths(3);
            $startOfCurrentMonth = Carbon::now()->startOfMonth();
            $txs  = Loyaltytransaction::where('transactiontype', 'LIKE', '%' . $txType . '%')->whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();

            return Pdf::view('reports-templates.txs-templates.txs-template',
                ['txs' => $txs, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config])
                ->format('a4')
                ->save('txs-from-' . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '.pdf');
        }elseif ($request->get('period') == 'BIYEARLY'){
            $_6MonthLater = Carbon::now()->startOfMonth()->subMonths(6);
            $startOfCurrentMonth = Carbon::now()->startOfMonth();
            $txs  = Loyaltytransaction::where('transactiontype', 'LIKE', '%' . $txType . '%')->whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();

            return Pdf::view('reports-templates.txs-templates.txs-template',
                ['txs' => $txs, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config])
                ->format('a4')
                ->save('txs-from-' . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '.pdf');
        }elseif ($request->get('period') == 'YEARLY'){
            $aYearLater = Carbon::now()->startOfYear()->subYear();
            $startOfCurrentYear = Carbon::now()->startOfYear();
            $txs  = Loyaltytransaction::where('transactiontype', 'LIKE', '%' . $txType . '%')->whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();

            return Pdf::view('reports-templates.txs-templates.txs-template',
                ['txs' => $txs, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config])
                ->format('a4')
                ->save('txs-from-' . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentYear->format('d-m-Y') . '.pdf');
        }

        $txs = Loyaltytransaction::where('transactiontype', 'LIKE', '%' . $txType . '%')->orderBy('created_at', 'desc')->get();
        return Pdf::view('reports-templates.txs-templates.txs-template',
            ['txs' => $txs, 'config' => $config])
            ->format('a4')
            ->save('txs.pdf');
        /*$voucher = Voucher::where('id', $voucherId)->first();
        $client = Client::where('id', $voucher->clientid)->first();

        return Pdf::view('mailtemplates.vouchers-templates.voucher-report-template', ['voucher' => $voucher, 'client' => $client])
            ->format('a4')
            ->save($voucher->serialnumber . '.pdf');*/
    }


    public function reportClients(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'etat' => 'required|string|in:ALL,ACTIVATED,DEACTIVATED',
            'period' => 'required|string|in:ALL,MONTHLY,QUATERLY,BIYEARLY,YEARLY',
        ]);


        if ($validator->fails()) {
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }
        $config = Config::where('is_applicable', true)->first();


        if (trim($request->get('period')) == 'ALL'){
            if (trim($request->get('etat')) == 'ALL'){
                $clients = Client::orderBy('created_at', 'desc')->get();
                return Pdf::view('reports-templates.clients-templates.clients-template',
                    ['clients' => $clients, 'state' => trim($request->get('etat')), 'config' => $config])
                    ->format('a4')
                    ->save('ALL-STATE-'. 'clients.pdf');
            }else{
                $active = trim($request->get('etat')) === 'ACTIVATED' ? true : false;
                $clients = Client::where('active', $active) -> orderBy('created_at', 'desc')->get();
                return Pdf::view('reports-templates.clients-templates.clients-template',
                    ['clients' => $clients, 'config' => $config, 'state' => trim($request->get('etat'))])
                    ->format('a4')
                    ->save(trim($request->get('etat')) . '-' . 'clients.pdf');
            }

        }elseif (trim($request->get('period')) == 'MONTHLY'){
            $aMonthLater = Carbon::now()->startOfMonth()->subMonth();
            $startOfCurrentMonth = Carbon::now()->startOfMonth();

            if (trim($request->get('etat')) == 'ALL'){
                $clients = Client::whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                return Pdf::view('reports-templates.clients-templates.clients-template',
                    ['clients' => $clients, 'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => trim($request->get('etat'))])
                    ->format('a4')
                    ->save('ALL-STATE-'. 'clients.pdf');
            }else{
                $active = trim($request->get('etat')) === 'ACTIVATED' ? true : false;
                $clients = Client::where('active', $active) -> whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                return Pdf::view('reports-templates.clients-templates.clients-template',
                    ['clients' => $clients, 'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => trim($request->get('etat'))])
                    ->format('a4')
                    ->save('clients-from-' . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'clients.pdf');
            }

        }elseif (trim($request->get('period')) == 'QUATERLY'){
            $_3MonthLater = Carbon::now()->startOfMonth()->subMonths(3);
            $startOfCurrentMonth = Carbon::now()->startOfMonth();

            if (trim($request->get('etat')) == 'ALL'){
                $clients = Client::whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                return Pdf::view('reports-templates.clients-templates.clients-template',
                    ['clients' => $clients, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => trim($request->get('etat'))])
                    ->format('a4')
                    ->save('ALL-STATE-'. 'clients.pdf');
            }else{
                $active = trim($request->get('etat')) === 'ACTIVATED' ? true : false;
                $clients = Client::where('active', $active) -> whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                return Pdf::view('reports-templates.clients-templates.clients-template',
                    ['clients' => $clients, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => trim($request->get('etat'))])
                    ->format('a4')
                    ->save('clients-from-' . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'clients.pdf');
            }
        }elseif (trim($request->get('period')) == 'BIYEARLY'){
            $_6MonthLater = Carbon::now()->startOfMonth()->subMonths(6);
            $startOfCurrentMonth = Carbon::now()->startOfMonth();

            if (trim($request->get('etat')) == 'ALL'){
                $clients = Client::whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                return Pdf::view('reports-templates.clients-templates.clients-template',
                    ['clients' => $clients, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => trim($request->get('etat'))])
                    ->format('a4')
                    ->save('ALL-STATE-'. 'clients.pdf');
            }else{
                $active = trim($request->get('etat')) === 'ACTIVATED' ? true : false;
                $clients = Client::where('active', $active) -> whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                return Pdf::view('reports-templates.clients-templates.clients-template',
                    ['clients' => $clients, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => trim($request->get('etat'))])
                    ->format('a4')
                    ->save('clients-from-' . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'clients.pdf');
            }

        }elseif (trim($request->get('period')) == 'YEARLY'){
            $aYearLater = Carbon::now()->startOfYear()->subYear();
            $startOfCurrentYear = Carbon::now()->startOfYear();
            if (trim($request->get('etat')) == 'ALL'){
                $clients = Client::whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();
                return Pdf::view('reports-templates.clients-templates.clients-template',
                    ['clients' => $clients, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config, 'state' => trim($request->get('etat'))])
                    ->format('a4')
                    ->save('ALL-STATE-'. 'clients.pdf');
            }else{
                $active = trim($request->get('etat')) === 'ACTIVATED' ? true : false;
                $clients = Client::where('active', $active) -> whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();
                return Pdf::view('reports-templates.clients-templates.clients-template',
                    ['clients' => $clients, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config, 'state' => trim($request->get('etat'))])
                    ->format('a4')
                    ->save('clients-from-' . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentYear->format('d-m-Y') . '-' . 'clients.pdf');
            }

        }

        $clients = Client::orderBy('created_at', 'desc')->get();
        return Pdf::view('reports-templates.clients-templates.clients-template',
            ['clients' => $clients, 'config' => $config, 'state' => trim($request->get('etat'))])
            ->format('a4')
            ->save('ALL-STATE-'. 'clients.pdf');
    }


    public function reportVouchers(Request $request)
    {
        $config = Config::where('is_applicable', true)->first();
        $levels = json_decode($config->levels);
        $ids = [];
        foreach ($levels as $level){
            array_push($ids, $level->id);
        }
        $strLevels = implode(',', $ids);
        $validator = Validator::make($request->all(), [
            'state' => 'required|string|in:ALL,GENERATED,ACTIVATED,USED',
            'level' => 'required|string|in:' . $strLevels . ',ALL',
            'configid' => 'required|string|uuid|exists:configs,id',
            'period' => 'required|string|in:ALL,MONTHLY,QUATERLY,BIYEARLY,YEARLY',
        ]);

        //dd($request->all());

        if ($validator->fails()) {
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $state = 'ALL';
        /*if (trim($request->get('state')) == 'ALL') {
            $state = 'ALL';
        }else*/if (trim($request->get('state')) == 'GENERATED') {
            $state = 'GENERATED';
        }elseif (trim($request->get('state')) == 'ACTIVATED') {
            $state = 'ACTIVATED';
        }elseif (trim($request->get('state')) == 'USED') {
            $state = 'USED';
        }


        //dd($levels);

        if ($request->get('period') == 'ALL'){
            if (trim($request->get('level')) == 'ALL') {
                if($state == 'ALL'){
                    $vourchers = Voucher::orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => 'ALL'])
                        ->format('a4')
                        ->save('vouchers.pdf');
                }elseif ($state == 'GENERATED') {
                    $vourchers = Voucher::where('active', false)->where('is_used', false)->orderBy('created_at', 'desc')->get();
                    //dd($vourchers);
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-generated.pdf');
                }elseif ($state == 'ACTIVATED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', false)->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-activated.pdf');
                }elseif ($state == 'USED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', true)->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-used.pdf');
                }

            }
            foreach ($levels as $level) {

            //else{
                if(trim($request->get('level')) == $level->id){
                    if($state == 'ALL'){
                        $vourchers = Voucher::where('level', $level->name)->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save($level->name . 'vouchers.pdf');
                    }elseif ($state == 'GENERATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', false)->where('is_used', false)->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save($level->name . 'vouchers-generated.pdf');
                    }elseif ($state == 'ACTIVATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', false)->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save($level->name . 'vouchers-activated.pdf');
                    }elseif ($state == 'USED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', true)->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save($level->name . 'vouchers-used.pdf');
                    }
                }
            //}
            }

        }elseif ($request->get('period') == 'MONTHLY'){
            $aMonthLater = Carbon::now()->startOfMonth()->subMonth();
            $startOfCurrentMonth = Carbon::now()->startOfMonth();
            //$txs  = Loyaltytransaction::where('transactiontype', 'LIKE', '%' . $txType . '%')->whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
            /*return Pdf::view('reports-templates.txs-templates.txs-template',
                ['txs' => $txs, 'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config])
                ->format('a4')
                ->save('txs-from-' . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '.pdf');*/

            if (trim($request->get('level')) == 'ALL') {
                if($state == 'ALL'){
                    $vourchers = Voucher::whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers,  'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => 'ALL'])
                        ->format('a4')
                        ->save('vouchers-from-'  . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers.pdf');
                }elseif ($state == 'GENERATED') {
                    $vourchers = Voucher::where('active', false)->where('is_used', false)->whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers,  'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers-generated.pdf');
                }elseif ($state == 'ACTIVATED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', false)->whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers,  'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers-activated.pdf');
                }elseif ($state == 'USED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', true)->whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers,  'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers-used.pdf');
                }

            }
            foreach ($levels as $level) {
                //else{
                if(trim($request->get('level')) == $level->id){
                    if($state == 'ALL'){
                        $vourchers = Voucher::where('level', $level->name)->whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers,  'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers.pdf');
                    }elseif ($state == 'GENERATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', false)->where('is_used', false)->whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers,  'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers-generated.pdf');
                    }elseif ($state == 'ACTIVATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', false)->whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers,  'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers-activated.pdf');
                    }elseif ($state == 'USED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', true)->whereBetween('created_at', [$aMonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers,  'from' => $aMonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $aMonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers-used.pdf');
                    }
                }
                //}

            }


        }elseif ($request->get('period') == 'QUATERLY'){
            $_3MonthLater = Carbon::now()->startOfMonth()->subMonths(3);
            $startOfCurrentMonth = Carbon::now()->startOfMonth();
           /* $txs  = Loyaltytransaction::where('transactiontype', 'LIKE', '%' . $txType . '%')->whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();

            return Pdf::view('reports-templates.txs-templates.txs-template',
                ['txs' => $txs, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config])
                ->format('a4')
                ->save('txs-from-' . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '.pdf');*/

            if (trim($request->get('level')) == 'ALL') {
                if($state == 'ALL'){
                    $vourchers = Voucher::whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => 'ALL'])
                        ->format('a4')
                        ->save('vouchers-from-'  . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers.pdf');
                }elseif ($state == 'GENERATED') {
                    $vourchers = Voucher::where('active', false)->where('is_used', false)->whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers-generated.pdf');
                }elseif ($state == 'ACTIVATED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', false)->whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers-activated.pdf');
                }elseif ($state == 'USED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', true)->whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers-used.pdf');
                }

            }
            foreach ($levels as $level) {
                if(trim($request->get('level')) == $level->id){
                    if($state == 'ALL'){
                        $vourchers = Voucher::where('level', $level->name)->whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers.pdf');
                    }elseif ($state == 'GENERATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', false)->where('is_used', false)->whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers-generated.pdf');
                    }elseif ($state == 'ACTIVATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', false)->whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers-activated.pdf');
                    }elseif ($state == 'USED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', true)->whereBetween('created_at', [$_3MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $_3MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $_3MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers-used.pdf');
                    }
                }
            }
        }elseif ($request->get('period') == 'BIYEARLY'){
            $_6MonthLater = Carbon::now()->startOfMonth()->subMonths(6);
            $startOfCurrentMonth = Carbon::now()->startOfMonth();
            /*$txs  = Loyaltytransaction::where('transactiontype', 'LIKE', '%' . $txType . '%')->whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();

            return Pdf::view('reports-templates.txs-templates.txs-template',
                ['txs' => $txs, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config])
                ->format('a4')
                ->save('txs-from-' . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '.pdf');*/

            if (trim($request->get('level')) == 'ALL') {
                if($state == 'ALL'){
                    $vourchers = Voucher::whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => 'ALL'])
                        ->format('a4')
                        ->save('vouchers-from-'  . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers.pdf');
                }elseif ($state == 'GENERATED') {
                    $vourchers = Voucher::where('active', false)->where('is_used', false)->whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers-generated.pdf');
                }elseif ($state == 'ACTIVATED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', false)->whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers-activated.pdf');
                }elseif ($state == 'USED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', true)->whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . 'vouchers-used.pdf');
                }

            }

            foreach ($levels as $level) {
                if(trim($request->get('level')) == $level->id){
                    if($state == 'ALL'){
                        $vourchers = Voucher::where('level', $level->name)->whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers.pdf');
                    }elseif ($state == 'GENERATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', false)->where('is_used', false)->whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers-generated.pdf');
                    }elseif ($state == 'ACTIVATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', false)->whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers-activated.pdf');
                    }elseif ($state == 'USED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', true)->whereBetween('created_at', [$_6MonthLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $_6MonthLater, 'to' => $startOfCurrentMonth, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $_6MonthLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '-' . $level->name . 'vouchers-used.pdf');
                    }
                }
            }
        }elseif ($request->get('period') == 'YEARLY'){
            $aYearLater = Carbon::now()->startOfYear()->subYear();
            $startOfCurrentYear = Carbon::now()->startOfYear();
            /*$txs  = Loyaltytransaction::where('transactiontype', 'LIKE', '%' . $txType . '%')->whereBetween('created_at', [$aYearLater, $startOfCurrentMonth])->orderBy('created_at', 'desc')->get();

            return Pdf::view('reports-templates.txs-templates.txs-template',
                ['txs' => $txs, 'from' => $aYearLater, 'to' => $startOfCurrentMonth, 'config' => $config])
                ->format('a4')
                ->save('txs-from-' . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentMonth->format('d-m-Y') . '.pdf');*/

            if (trim($request->get('level')) == 'ALL') {
                if($state == 'ALL'){
                    $vourchers = Voucher::whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config, 'state' => $state, 'level' => 'ALL'])
                        ->format('a4')
                        ->save('vouchers-from-'  . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentYear->format('d-m-Y') . '-' . 'vouchers.pdf');
                }elseif ($state == 'GENERATED') {
                    $vourchers = Voucher::where('active', false)->where('is_used', false)->whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentYear->format('d-m-Y') . '-' . 'vouchers-generated.pdf');
                }elseif ($state == 'ACTIVATED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', false)->whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentYear->format('d-m-Y') . '-' . 'vouchers-activated.pdf');
                }elseif ($state == 'USED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', true)->whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-from-'  . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentYear->format('d-m-Y') . '-' . 'vouchers-used.pdf');
                }

            }

            foreach ($levels as $level) {
                if(trim($request->get('level')) == $level->id){
                    if($state == 'ALL'){
                        $vourchers = Voucher::where('level', $level->name)->whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-'  . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentYear->format('d-m-Y') . '-' . $level->name . 'vouchers.pdf');
                    }elseif ($state == 'GENERATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', false)->where('is_used', false)->whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-' . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentYear->format('d-m-Y') . '-' . $level->name . 'vouchers-generated.pdf');
                    }elseif ($state == 'ACTIVATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', false)->whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save('vouchers-from-' . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentYear->format('d-m-Y') . '-' . $level->name . 'vouchers-activated.pdf');
                    }elseif ($state == 'USED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', true)->whereBetween('created_at', [$aYearLater, $startOfCurrentYear])->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'from' => $aYearLater, 'to' => $startOfCurrentYear, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save( 'vouchers-from-' . $aYearLater->format('d-m-Y') . '-to-' . $startOfCurrentYear->format('d-m-Y') . '-' . $level->name . 'vouchers-used.pdf');
                    }
                }
            }
        }


        /*foreach ($levels as $level) {
            if (trim($request->get('level')) == 'ALL') {
                if($state == 'ALL'){
                    $vourchers = Voucher::orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers.pdf');
                }elseif ($state == 'GENERATED') {
                    $vourchers = Voucher::where('active', false)->where('is_used', false)->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-generated.pdf');
                }elseif ($state == 'ACTIVATED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', false)->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-activated.pdf');
                }elseif ($state == 'USED') {
                    $vourchers = Voucher::where('active', true)->where('is_used', true)->orderBy('created_at', 'desc')->get();
                    return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                        ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                        ->format('a4')
                        ->save('vouchers-used.pdf');
                }

            }else{
                if(trim($request->get('level')) == $level->id){
                    if($state == 'ALL'){
                        $vourchers = Voucher::where('level', $level->name)->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save($level->name . 'vouchers.pdf');
                    }elseif ($state == 'GENERATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', false)->where('is_used', false)->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save($level->name . 'vouchers-generated.pdf');
                    }elseif ($state == 'ACTIVATED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', false)->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save($level->name . 'vouchers-activated.pdf');
                    }elseif ($state == 'USED') {
                        $vourchers = Voucher::where('level', $level->name)->where('active', true)->where('is_used', true)->orderBy('created_at', 'desc')->get();
                        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
                            ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => $level->name])
                            ->format('a4')
                            ->save($level->name . 'vouchers-used.pdf');
                    }
                }
            }

        }*/

        $vourchers = Voucher::orderBy('created_at', 'desc')->get();
        return Pdf::view('reports-templates.vouchers-templates.voucher-report-template',
            ['vouchers' => $vourchers, 'config' => $config, 'state' => $state, 'level' => 'ALL'])
            ->format('a4')
            ->save('vouchers.pdf');
    }


}
