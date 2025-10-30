<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Conversion;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltyewalet;
use App\Models\Threshold;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        if ($request->filled('email')){
            $validatorEmail = Validator::make($request->all(), [
                'email' => 'string|email|max:255',
            ]);
            if($validatorEmail->fails()){
                return back()->withErrors(['error' => $validatorEmail->errors()->first()]);
            }
        }

        $secret = null;
        $birthdate = "";
        if (!$request->filled('day') || !$request->filled('month') || !$request->filled('year')){
            $secret = "12345678";
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
            $birthdateFormatedArr = explode('-', $birthdate);
            $secret = $birthdateFormatedArr[2] . $birthdateFormatedArr[1] . $birthdateFormatedArr[0];
        }

        if ($request->filled('gender')){
            $validatorGender = Validator::make($request->all(), [
                'gender' => 'string|in:MONSIEUR,MADAME,MADEMOISELLE',
            ]);
            if($validatorGender->fails()){
                return back()->withErrors(['error' => $validatorGender->errors()->first()]);
            }
        }

        if ($request->filled('quarter')){
            $validatorQuarter = Validator::make($request->all(), [
                'quarter' => 'string|max:255',
            ]);
            if($validatorQuarter->fails()){
                return back()->withErrors(['error' => $validatorQuarter->errors()->first()]);
            }
        }


        if ($request->filled('city')){
            $validatorCity = Validator::make($request->all(), [
                'city' => 'string|max:255',
            ]);
            if($validatorCity->fails()){
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

        DB::beginTransaction();
        try {

            $client = $this->create($data);


            $loyaltyaccountId = Str::uuid()->toString();
            $loyaltyaccountnumber = $this->generateLoyaltyAccountNumber();
            $holder = $client->id;
            $point_balance = (int)env('INITIAL_POINT_BALANCE');
            $amount_balance = 0;
            /*$amount_from_converted_point = $this->convertPointToAmount($point_balance);
            if ($amount_from_converted_point == null){
                DB::rollback();
                return back()->withErrors(['error' => 'Aucune regle de conversion trouvee']);
            }*/
            //$amount_balance = $amountConverted;
            $current_point = 0.0;
            $photo = '';
            $issuer = Auth::user()->id;
            $currency_name = env('CURRENCY_NAME');
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


    public function clientDetails($clientId){
        $client = Client::where('id', $clientId)->first();
        $user = User::where('id', $client->registered_by)->first();
        $loyaltyAccount = Loyaltyaccount::where('holderid', $client->id)->first();
        $threshold = Threshold::where('is_applicable', true)->where('active', true)->first();
        return view('client.client-details', ['client' => $client, 'user' => $user, 'loyaltyAccount' => $loyaltyAccount, 'threshold' => $threshold]);
    }

    public function getVouchers(string $clientId)
    {
        $vouchers = Voucher::where('clientid', $clientId)->orderBy('created_at', 'desc')->get();
        $client = Client::where('id', $clientId)->first();
        $user = Auth::user();

        return view('client.client-vouchers', ['client' => $client, 'user' => $user, 'vouchers' => $vouchers]);

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

        $voucher->active = true;
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

        $voucher->active = false;
        $voucher->save();
        $msg = 'Le bon ' . $voucher->name . ' a ete desactive avec succes.';

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
