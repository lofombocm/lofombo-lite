<?php

namespace App\Http\Controllers;

use App\Http\Controllers\GuestController;
use App\Jobs\ProcessSendEMailClientInvitationJob;
use App\Jobs\ProcessSendEMailVoucherUsageCodeJob;
use App\Models\Client;
use App\Models\Config;
use App\Models\FriendInvitatin;
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
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;
//use App\Http\Controllers\GuestController;

class HomeClientController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('client-is-activated');
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

    public function updateClientForm(string $locale, string $clientid)
    {
        $msg = __("Désolé, vous n'avez pas l'accès");
        if(Auth::guard('client')->check()){
            if ($clientid !== Auth::guard('client')->user()->id) {
                session()->flash('error', $msg);
                return redirect("auth/clent")->withError($msg);
            }
            return view('client.update-client-form', ['client' =>  Auth::guard('client')->user()]);
        }

        session()->flash('error', $msg);
        return redirect("auth/client")->withError($msg);
    }


    public function updateClient(Request $request, string $locale, string $clientId){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'telephone' => 'required|string|max:255',
        ],
            [
                'telephone.required' => __('Le numéro de téléphone est obligatoire'),
                'name.required' => __('Le nom est obligatoire.'),
            ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $client = Client::where('id', $clientId)->first();
        if(!$client){

            session()->flash('error', __("Le client n'est pas reconnu"));
            return back()->withErrors(['error' => __("Le client n'est pas reconnu")]);
        }

        $otherClient = Client::where('telephone', $request->get('telephone'))->first();
        if(!($otherClient === null) && $otherClient->id != $clientId){
            session()->flash('error', __("Le client n'est pas reconnu"));
            return back()->withErrors(['error' => 'Numero de telephone deja utilise par le client ' . $otherClient->name . '.']);
        }

        $client->name = $request->get('name');
        $client->telephone = $request->get('telephone');

        if ($request->filled('email')){
            $validatorEmail = Validator::make($request->all(), [
                'email' => 'string|email|max:255',
            ],[
                'email.required' => __("L'adresse E-Mail est obligatoire"),
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
            ],[
                'day.required' => __('Jour obligatoire.'),
                'day.in' => __("Jour invalide"),
                'month.required' => __('Mois obligatoire.'),
                'month.in' => __("Mois invalide"),
            ]);
            if($validatorBirthdate->fails()){
                session()->flash('error', $validatorBirthdate->errors()->first());
                return back()->withErrors(['error' => $validatorBirthdate->errors()->first()]);
            }

            if (intval(strval($request->get('month'))) == 2 && intval(strval($request->get('day'))) > 29) {
                //if($validatorBirthdate->fails()){
                    session()->flash('error', __('Date invalide'));
                    return redirect()->back()->withErrors(['error' => __('Date invalide')]);
                //}
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
            ],[
                'gender.required' => __('Le sexe est obligatoire.'),
                'gender.in' => __("Le sexe est invalide."),
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
            ],[
                'quarter.string' => __('Lieu de résidence invalide'),
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
            ],[
                'city.string' => __('Ville invalide'),
            ]);
            if($validatorCity->fails()){
                session()->flash('error', $validatorCity->errors()->first());
                return back()->withErrors(['error' => $validatorCity->errors()->first()]);
            }
            $client->city = $request->get('city');
        }

        $client->save();

        $msg =  __("Client ajourné avec succès.");
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

    public function showLoyaltyTransactionsClientSearch(Request $request, string $locale, string $clientId){
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

    public function showLoyaltyTransactionsAllPerClient(string $locale, string $clientId)
    {
        $loyaltyAccount = Loyaltyaccount::where('holderid', $clientId)->first();
        return view('tx-list-client',
            ['txs' => Loyaltytransaction::where('loyaltyaccountid', $loyaltyAccount->id)->orderBy('created_at', 'desc')->get(), 'clientid' => $clientId]);
    }

    public function downloadVoucher(string $locale, string $voucherId){

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

    public function resendUsageCodeForm(string $locale, string $voucherId){
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

    public function resendUsageCode(Request $request, string $locale, string $voucherId)
    {
        if (!Auth::guard('client')->check()){
            $msg = __("Désolé, vous n'avez pas l'accès");
            session()->flash('error', $msg);
            return back()->withErrors(['error' =>  $msg]);
        }

        $client = Auth::guard('client')->user();

        $validator = Validator::make($request->all(), [
            'serialnumber' => 'required|string|max:255|exists:vouchers,serialnumber',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|max:20|min:8',
        ], [
            'serialnumber.required' => __("Numéro de série obligatoire."),
            'serialnumber.exists' => __("Numéro de série non reconnu."),
            'email.required' => __("L'adresse E-Mail est obligatoire"),
            'email.email' => __("L'adresse E-Mail n'est pas valide"),
            'password.required' => __("Le mot de passe est obligatoire"),
            'password.min' =>  __("Le mot de passe doit avoir au moins 8 caractères"),
            'password.max' => __("Le mot de passe doit avoir au plus 20 caractères")
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $voucher = Voucher::where('id', $voucherId)->first();
        if ($voucher == null){
            $msg = __("Bon non reconnu.");
            session()->flash('error', $msg);
            return back()->withErrors(['error' =>  $msg]);
        }

        if ($voucher->serialnumber != $request->get('serialnumber')){
            $msg = __("Numéro de série non reconnu.");
            session()->flash('error', $msg);
            return back()->withErrors(['error' =>  $msg]);
        }

        $request->merge([
            'telephone' => $client->telephone,
        ]);
        $credentials = $request->only('telephone', 'password');
        if (!Auth::guard('client')->attempt($credentials)) {
            $msg = __("Désolé, vous n'avez pas l'accès");
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

        $voucher_usage_codes = Voucherusagecode::where('voucherid', $voucherId)->orderBy('created_at', 'desc')->get();
        if (count($voucher_usage_codes) == 0){
            $msg = __("Une erreur est survenue, reessayez de nouveau.");
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

        $voucher_usage_code = $voucher_usage_codes[0];

        $link = url('/'.GuestController::getApplicationLocal().'/client/'. $client->id . '/vouchers') ;
        $config = Config::where('is_applicable', true)->first();
        $message = [$client->gender . ' ' . $client->name . ', ' . __("vous avez demander à la plateforme de fidélité") . ' ' . $config->enterprise_name .
         __("de vous envoyer un code d'utilisation d'un bon. Si jamais vous n'avez pas fait cette demande, nous vous prions de bien vouloir ignorer ce message")  .  '.'];
        $emaildata = ['email' =>trim($request->get('email')), 'name' => $client->name, 'clientLoginUrl' => $link, 'msg' => $message,
            'code' => decrypt($voucher_usage_code->code)];
        //dd($emaildata);
        ProcessSendEMailVoucherUsageCodeJob::dispatch($emaildata);

        $msg = __("Le code demandé a été envoyé au mail") . ' ' . $emaildata['email'] . '.';
        session()->flash('status', $msg);
        return redirect()->back()->with('status', $msg);
    }


    public function showLoyaltyTransactionsDetails(string $locale, string $txid)
    {
        $tx = Loyaltytransaction::where('id', $txid)->first();
        $client = Client::where('id', $tx->clientid)->first();
        $rewards = Reward::where('active', true)->get();
        return view('tx-client-details', ['tx' => $tx, 'client' => $client, 'rewards' => $rewards]);
    }


    public function getFriendInvitationForm(string $locale, string $clientid)
    {
        $client = Client::where('id', $clientid)->first();
        return view('client.invitation.form', ['clientid' => $clientid, 'client' => $client]);
    }

    public function getFriendInvitationList(string $locale, string $clientid)
    {
        $client = Client::where('id', $clientid)->first();
        //$invitedClients = Client::where('invited_by', $clientid)->get();

        //dd($client);
        $friendInvitations = FriendInvitatin::where('inviter_id', $clientid)->orderBy('created_at', 'desc')->get();

        return view('client.invitation.list', ['clientid' => $clientid, 'client' => $client, 'friendInvitations' => $friendInvitations]);
    }

    public function postFriendInvitationForm(Request $request, string $locale, string $clientid)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|min:2',
        ],
        [
            'email.required' => __("L'adresse E-Mail est obligatoire"),
            'email.email' => __("L'adresse E-Mail n'est pas valide"),
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $client = Client::where('id', $clientid)->first();
        if(!$client){

            session()->flash('error', __("Le client n'est pas reconnu"));
            return back()->withErrors(['error' => __("Le client n'est pas reconnu")]);
        }

        $id = Str::uuid()->toString();

        $invitationLink = url('/'.GuestController::getApplicationLocal().'/client/' . $clientid . '/friend-invitations/' . $id) ;
        FriendInvitatin::create(
            [
                'id' => $id,
                'name' => $request->get('name'),
                'email' => trim($request->get('email')),
                'telephone' => $request->get('telephone'),
                'inviter_id' => $client->id,
                'state' => 'PENDING',
                'active' => true,
                'invitation_link' => $invitationLink,
            ]
        );

        $config = Config::where('is_applicable', true)->first();

        $emaildata = [
            'email' => trim($request->get('email')),
            'inviter' => $client->name,
            'enterprise' => $config->enterprise_name,
            'invitation_url' => $invitationLink,
            'name' => $request->get('name'),
        ];
        ProcessSendEMailClientInvitationJob::dispatch($emaildata);

        $msg =  __("Invitation envoyé avec succès.");
        session()->flash('status', $msg);
        return redirect()->back()->with('status', $msg);

    }

}
