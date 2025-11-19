<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSendEMailClientCredentialsJob;
use App\Jobs\ProcessSendEMailVoucherAvailableJob;
use App\Jobs\ProcessSendEMailVoucherGeneratedJob;
use App\Jobs\ProcessSendEMailVoucherUsedJob;
use App\Models\Client;
use App\Models\Config;
use App\Models\Conversion;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltyewalet;
use App\Models\Loyaltytransaction;
use App\Models\Notification;
use App\Models\Reward;
use App\Models\Threshold;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsageCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('client.index');
    }

    protected function create(array $data)
    {
        $pwd =  Hash::make($data['password']);
        return Client::create([
            'id' => $data['id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'birthdate' => $data['birthdate'],
            'gender' => $data['gender'],
            'quarter' => $data['quarter'],
            'city' => $data['city'],
            'password' => $pwd,
            'registered_by' => $data['registered_by'],
            'active' => true,
        ]);
    }

    public function registerClient(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'telephone' => 'required|string|max:255|unique:clients',
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $clientEmail = '';

        if ($request->filled('email')){
            $validatorEmail = Validator::make($request->all(), [
                'email' => 'string|email|max:255',
            ]);
            if($validatorEmail->fails()){
                session()->flash('error', $validatorEmail->errors()->first());
                return back()->withErrors(['error' => $validatorEmail->errors()->first()]);
            }
            $clientEmail = $request->get('email');
        }

        $secret = null;
        $birthdate = "";
        if (!$request->filled('day') || $request->filled('month') === null) {
            $secret = "12345678";
        }else{

            $year = 1900;
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
            $birthdateFormatedArr = explode('-', $birthdate);
            $secret = $birthdateFormatedArr[2] . $birthdateFormatedArr[1] . $birthdateFormatedArr[0];
        }

        if ($request->filled('gender')){
            $validatorGender = Validator::make($request->all(), [
                'gender' => 'required|string|in:MONSIEUR,MADAME,MADEMOISELLE',
            ]);
            if($validatorGender->fails()){
                session()->flash('error', $validatorGender->errors()->first());
                return back()->withErrors(['error' => $validatorGender->errors()->first()]);
            }
        }

        if ($request->filled('quarter')){
            $validatorQuarter = Validator::make($request->all(), [
                'quarter' => 'string|max:255',
            ]);
            if($validatorQuarter->fails()){
                session()->flash('error', $validatorQuarter->errors()->first());
                return back()->withErrors(['error' => $validatorQuarter->errors()->first()]);
            }
        }


        if ($request->filled('city')){
            $validatorCity = Validator::make($request->all(), [
                'city' => 'string|max:255',
            ]);
            if($validatorCity->fails()){
                session()->flash('error', $validatorCity->errors()->first());
                return back()->withErrors(['error' => $validatorCity->errors()->first()]);
            }
        }

        $id = Str::uuid()->toString();

        $data =  [
            'id' => $id,
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'telephone' => $request->get('telephone'),
            'birthdate' => $birthdate,
            'gender' => $request->get('gender'),
            'quarter' => $request->get('quarter'),
            'city' => $request->get('city'),
            'password' => $secret,
            'registered_by' => Auth::user()->id,
            'active' => true
        ];

        $configs = Config::where('is_applicable', true)->get();
        if (count($configs) === 0) {
            // = $configs[0]->enterprise_logo;
            //dd('Yes');
            $msg = 'Aucune confguration de conversion montant point trouve. Merci de definir au prealable une configuration du systeme';
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

        DB::beginTransaction();
        try {
            $client = $this->create($data);

            $loyaltyaccountId = Str::uuid()->toString();
            $loyaltyaccountnumber = $this->generateLoyaltyAccountNumber();
            $holder = $client->id;


            $config = $configs[0];

            $point_balance = $config->initial_loyalty_points;
            $amount_balance = $config->amount_per_point * $point_balance;
            /*$amount_from_converted_point = $this->convertPointToAmount($point_balance);
            if ($amount_from_converted_point == null){
                DB::rollback();
                return back()->withErrors(['error' => 'Aucune regle de conversion trouvee']);
            }*/
            //$amount_balance = $amountConverted;
            $current_point = 0;
            $photo = '';
            $issuer = Auth::user()->id;
            $currency_name = $config->currency_name; //env('CURRENCY_NAME');


            Loyaltyaccount::create([
                'id' => $loyaltyaccountId,
                'loyaltyaccountnumber' => $loyaltyaccountnumber,
                'holderid' => $holder,
                'amount_balance' => $amount_balance,
                'point_balance' => $point_balance,
                //'amount_from_converted_point' => $amount_from_converted_point,
                'current_point' => $current_point,
                'photo' => $photo,
                'issuer' => $issuer,
                'active' => true,
                'currency_name' => $currency_name,
            ]);

            $loyaltyWalletId = Str::uuid()->toString();
            $accounts = [];
            array_push($accounts, $loyaltyaccountId);
            Loyaltyewalet::create([
                'id' => $loyaltyWalletId,
                'holderid' => $client->id,
                'accountids' => json_encode($accounts),
                'issuer' => $issuer,
                'active' => true,
            ]);

            $transactionid = Str::uuid()->toString();

            Loyaltytransaction::create([
                'id' => $transactionid,
                'date' => Carbon::now(),
                'loyaltyaccountid' => $loyaltyaccountId,
                'configid' => $config->id,
                'madeby' => $issuer,
                'reference' => 'Transaction Initiale donnant les points initiaux au client',
                'amount' => $amount_balance,
                'point' => $point_balance,
                'old_amount' => 0.0,
                'old_point' => $current_point,
                'transactiontype' => 'INITIALISATION COMPTE CLIENT',
                'transactiondetail' => 'Transaction Initiale donnant les points initiaux au client',
                'clientid' => $client->id,
                'products' => '[]'
            ]);

            if (strlen($clientEmail)){
                /// TODO: Send SMS and email notification to client.
                $message = ['Enregistrement au systeme de fidelite de ' . $config->enterprise_name . ' de ' . $client->gender. ' ' . $client->name,
                    'Vous avez ete enregistre avec succes dans le systeme de fidelite de l\'entreprise ' . $config->enterprise_name,
                    'Vous pouvez acceder au systeme en utilisant les identifiants suivants: ', 'Numero de telephone: ' . $client->telephone,
                    'Mot de passe: '. $secret];
                $link = url('').'/auth/client' ;
                $data = ['email' => $clientEmail, 'name' => $client->name, 'clientLoginUrl' => $link, 'telephone' => $client->telephone, 'secret' => $secret,
                    'enterprise' => $config->enterprise_name, 'gender' => $client->gender, 'msg' => $message];
                ProcessSendEMailClientCredentialsJob::dispatch($data);

                $notifid = Str::uuid()->toString();
                $notifgenerator = '' . Auth::user()->id . '';
                $notifsubject = $message[0];
                $notifsentat = Carbon::now();
                $notifbody = json_encode($message);
                $notifdata = json_encode($data);
                $notifsender = Auth::user()->name;
                $notifrecipient = $client->name;
                $notifsenderaddress = Auth::user()->email;
                $notifrecipientaddress = $clientEmail;
                $notifread = false;

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
            }

        }catch (\Exception $e){
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        //Auth::guard('client')->login($client);
        DB::commit();

        session()->flash('status', 'Bien! Vous avez enregistre le client ' . $client->name . ' avec succes.');

        return redirect("home");//->withSuccess(['status' => 'Great! You have Successfully Registered.', 'client' => $client]);
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
                'gender' => 'required|string|in:MONSIEUR,MADAME,MADEMOISELLE',
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


    public function clientDetails($clientId){
        $client = Client::where('id', $clientId)->first();
        /*if(!$client){
            return back()->withErrors(['error' => 'Client introuvable']);
        }*/
        $user = User::where('id', $client->registered_by)->first();
        $loyaltyAccount = Loyaltyaccount::where('holderid', $client->id)->first();
        $config = Config::where('is_applicable', true)->first();
        return view('client.client-details', ['client' => $client, 'user' => $user, 'loyaltyAccount' => $loyaltyAccount, 'configuration' => $config]);
    }

    public function getVouchers(string $clientId)
    {
        $vouchers = Voucher::where('clientid', $clientId)->orderBy('created_at', 'desc')->get();
        $client = Client::where('id', $clientId)->first();
        $user = Auth::user();
        $config = Config::where('is_applicable', true)->first();
        $rewards = Reward::where('active', true)->orderBy('created_at', 'desc')->get();
        return view('client.client-vouchers', ['client' => $client, 'user' => $user, 'vouchers' => $vouchers,
            'config' => $config, 'rewards' => $rewards]);

    }

    public function getVouchersAll()
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->get();
        return view('vouchers', ['vouchers' => $vouchers, ]);
    }




    public function generateLoyaltyAccountNumber():string
    {
        $numberFormated = null;
        do {
            $number = random_int(100000000, 999999999);
            $numberStr = (string) $number;
            $numberFormated = implode("-", str_split($numberStr, 3));
        } while (Loyaltyaccount::where("loyaltyaccountnumber", "=", $numberFormated)->first());

        return $numberFormated;
    }


    public function deactivateClient(string $clientId)
    {
        $client = Client::where('id', $clientId)->first();
        $client->active = false;
        $client->save();
        $msg = 'Le client' . $client->name . ' a ete desactive avec succes.';
        session()->flash('status', $msg);
        return redirect()->back()->with('status', $msg);
    }

    public function activateClient(string $clientId)
    {
        $client = Client::where('id', $clientId)->first();
        $client->active = true;
        $client->save();
        $msg = 'Le client' . $client->name . ' a ete active avec succes.';
        session()->flash('status', $msg);
        return redirect()->back()->with('status', $msg);
    }

    public function activateVoucher(string $clientId, string $voucherId)
    {
        $client = Client::where('id', $clientId)->first();
        if ($client === null) {
            $msg = 'Le client avec l\'ID ' . $clientId . ' n\'existe pas.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        $voucher = Voucher::where('id', $voucherId)->first();
        if ($voucher === null) {
            $msg = 'Le bon avec l\'ID ' . $voucherId . ' n\'existe pas.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        $expirationdate = Carbon::parse($voucher->expirationdate);
        if ($voucher->is_used) {
            $msg = 'Le bon avec l\'ID ' . $voucherId . ' est deja utilise. Il a ete utilise le: ' . $expirationdate->format('d/m/Y');
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }


        if ($expirationdate->isBefore(Carbon::now())) {
            $msg = 'Le bon avec l\'ID ' . $voucherId . ' est expire le ' . $expirationdate->format('d/m/Y') . '.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        $voucher->active = true;
        $voucher->activated_at = Carbon::now();
        $voucher->deactivated_at = Carbon::now();
        $voucher->save();
        $msg = 'Le bon ' . $voucher->name . ' a ete active avec succes.';

        session()->flash('status', $msg);
        return redirect()->back()->with('status', $msg);
    }

    public function deactivateVoucher(string $clientId, string $voucherId)
    {
        $client = Client::where('id', $clientId)->first();
        if ($client === null) {
            $msg = 'Le client avec l\'ID ' . $clientId . ' n\'existe pas.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        $voucher = Voucher::where('id', $voucherId)->first();
        if ($voucher === null) {
            $msg = 'Le bon avec l\'ID ' . $voucherId . ' n\'existe pas.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        $expirationdate = Carbon::parse($voucher->expirationdate);
        if ($voucher->is_used) {
            $msg = 'Le bon avec l\'ID ' . $voucherId . ' est deja utilise. Il a ete utilise le: ' . $expirationdate->format('d/m/Y');
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }


        if ($expirationdate->isBefore(Carbon::now())) {
            $msg = 'Le bon avec l\'ID ' . $voucherId . ' est expire le ' . $expirationdate->format('d/m/Y') . '.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        $voucher->active = false;
        //$voucher->activated_at = Carbon::now();
        $voucher->deactivated_at = Carbon::now();
        //$voucher->activated_by = Auth::user()->id;

        $voucher->save();
        $msg = 'Le bon ' . $voucher->name . ' a ete desactive avec succes.';

        session()->flash('status', $msg);
        return redirect()->back()->with('status', $msg);
    }


    public function useVoucher(Request $request, string $clientId, string $voucherId)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:9|min:8',
        ]);
        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }


        $rawCode = trim($request->input('code'));

        if (!(strlen($rawCode) === 8 || strlen($rawCode) === 9)) {
            session()->flash('error', 'Code invalide');
            return back()->withErrors(['error' => 'Code invalide']);
        }

        $rawCodeArray = explode("-", $rawCode);

        $code = '';
        //$codewithhifen = '';
        if (count($rawCodeArray) === 2) {
            if (strlen($rawCodeArray[0]) !== 4) {
                session()->flash('error', 'Code invalide');
                return back()->withErrors(['error' => 'Code invalide']);
            }
            if (strlen($rawCodeArray[1]) !== 4) {
                session()->flash('error', 'Code invalide');
                return back()->withErrors(['error' => 'Code invalide']);
            }
            $code = $rawCode; //$rawCodeArray[0] . '-' . $rawCodeArray[1];
            //$codewithhifen = $rawCodeArray[0] . '-' . $rawCodeArray[1];
        }else{
            if (strlen($rawCode) != 8){
                session()->flash('error', 'Code invalide');
                return back()->withErrors(['error' => 'Code invalide']);
            }
            if (count($rawCodeArray) !== 1) {
                session()->flash('error', 'Code invalide');
                return back()->withErrors(['error' => 'Code invalide']);
            }

            $part1 = substr($rawCode, 0, 4);
            $part2 = substr($rawCode, 4, 4);
            $code =$part1 . '-' . $part2;
        }

        $client = Client::where('id', $clientId)->first();
        if ($client === null) {
            $msg = 'Le client avec l\'ID ' . $clientId . ' n\'existe pas.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        $voucher = Voucher::where('id', $voucherId)->first();
        if ($voucher === null) {
            $msg = 'Le bon avec l\'ID ' . $voucherId . ' n\'existe pas.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        if (!$voucher->active) {
            $msg = 'Le bon avec l\'ID ' . $voucherId . ' n\'est pas actif. Veuillez contacter l\'administrateur pour l\'activer.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        if ($voucher->is_used) {
            $msg = 'Le bon avec l\'ID ' . $voucherId . ' est deja utilise. Merci de contacter l\'administration pour plus d\'informations.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        $expirationdate = Carbon::parse($voucher->expirationdate);

        if ($expirationdate->isBefore(Carbon::now())) {
            $msg = 'Le bon avec l\'ID ' . $voucherId . ' est expire le ' . $expirationdate->format('d-m-Y H:i:s') . '.';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }


        $usageCode = VoucherUsageCode::where('voucherid', $voucherId)->first();
        $encryptedCode = $usageCode->code;
        $decryptedCode = decrypt($encryptedCode);

        //dd($decryptedCode);

        if ($decryptedCode !== $code) {
            $msg = 'Code invalide';
            session()->flash('error', $msg);
            return redirect()->back()->with('error', $msg);
        }

        $config = Config::where('is_applicable', true)->first();

        $voucher->code_used = $code;
        $voucher->is_used = true;
        $voucher->used_at = Carbon::now();

        DB::beginTransaction();

            try {
                $voucher->save();
                $msg = 'Le bon ' . $voucher->name . ' a ete enregistre comme utilise avec succes.';
                $subjec = 'Utilisation du Bon ayant le numero de serie ' . $voucher->serialnumber . '.';
                /// TODO Send email and SMS to client

                if ($client->email !== null) {
                    $message = [$client->gender . ' ' . $client->name . ', votre bon ayant le numero de serie ' . $voucher->serialnumber . ' a ete utiise le ' . (Carbon::now()->format('d-m-Y H:i:s')) . '.'];
                    $emaildata = ['email' =>$client->email, 'name' => $client->name, 'clientLoginUrl' => url('/home-client'), 'level' => $voucher->level, 'msg' => $message,
                        'code' => $code, 'config' => $config, 'subject' => $subjec];
                    //dd($emaildata);
                    ProcessSendEMailVoucherUsedJob::dispatch($emaildata);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator = '' . Auth::user()->id . '';
                    $notifsubject = 'Utilisation du Bon ayant le numero de serie ' . $voucher->serialnumber;
                    $notifsentat = Carbon::now();
                    $notifbody = json_encode($message);
                    $notifdata = json_encode($emaildata);
                    $notifsender = Auth::user()->name;
                    $notifrecipient = $client->name;
                    $notifsenderaddress = Auth::user()->email;
                    $notifrecipientaddress = $client->email;
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
                }

                $message = ['Mme/M. ' . ' ' . Auth::user()->name . ', le bon ayant le numero de serie ' . $voucher->serialnumber . ' a ete utiise le ' . (Carbon::now()->format('d-m-Y H:i:s')) . '.'];
                $emaildata = ['email' =>Auth::user()->email, 'name' => Auth::user()->name, 'clientLoginUrl' => url('/home'), 'level' => $voucher->level, 'msg' => $message,
                    'code' => $code, 'config' => $config, 'subject' => $subjec];
                //dd($emaildata);
                ProcessSendEMailVoucherUsedJob::dispatch($emaildata);

                $notifid = Str::uuid()->toString();
                $notifgenerator = '' . Auth::user()->id . '';
                $notifsubject = 'Utilisation du Bon ayant le numero de serie ' . $voucher->serialnumber;
                $notifsentat = Carbon::now();
                $notifbody = json_encode($message);
                $notifdata = json_encode($emaildata);
                $notifsender = Auth::user()->name;
                $notifrecipient = Auth::user()->name;
                $notifsenderaddress = Auth::user()->email;
                $notifrecipientaddress = Auth::user()->email;
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
            }catch (\Exception $exception){
                DB::rollBack();
                session()->flash('error', $exception->getMessage());
                return redirect()->back()->with('error', $exception->getMessage());
            }

        DB::commit();

        session()->flash('status', $msg);
        return redirect()->back()->with('status', $msg);
    }



    /*public function convertPointToAmount($point){
        $lastConversion = Conversion::where('is_applicable', true)->orderBy('created_at', 'desc')->first();
        if ($lastConversion == null) {
            return null;
        }
        return ($point *  $lastConversion -> point_to_amount_amount)/$lastConversion -> point_to_amount_point;
    }*/
}
