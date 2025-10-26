<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Conversion;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltyewalet;
use App\Models\User;
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
        if (!$request->filled('birthdate')){
            $secret = "12345678";
        }else{

            $validatorBirthdate = Validator::make($request->all(), [
                'birthdate' => 'date|before:today',
            ]);
            if($validatorBirthdate->fails()){
                return back()->withErrors(['error' => $validatorBirthdate->errors()->first()]);
            }

            $birthdate = $request->get('birthdate');

            $birthdateFormatedArr = explode('-', $birthdate);
            if(count($birthdateFormatedArr) != 3){
                $birthdateFormatedArr = explode('/', $birthdate);
                if(count($birthdateFormatedArr) != 3){
                    $secret = "12345678";
                }else{
                    $secret = $birthdateFormatedArr[2] . $birthdateFormatedArr[1] . $birthdateFormatedArr[0];
                }
            }else{
                $secret = $birthdateFormatedArr[2] . $birthdateFormatedArr[1] . $birthdateFormatedArr[0];
            }

            /*$birthdateFormated = Carbon::createFromFormat('m/d/Y', $birthdate)->format('Y-m-d');
            if ($birthdateFormated === false){
                $birthdateFormated = Carbon::createFromFormat('m-d-Y', $birthdate)->format('Y-m-d');
                if ($birthdateFormated === false){
                    $birthdateFormated = Carbon::createFromFormat('Y-m-d', $birthdate)->format('Y-m-d');
                    if ($birthdateFormated === false){
                        $birthdateFormated = Carbon::createFromFormat('Y/m/d', $birthdate)->format('Y-m-d');
                        if ($birthdateFormated === false){
                            $secret = "12345678";
                        }else{
                            $year = $birthdateFormated->year;
                            $month = $birthdateFormated->month;
                            $day = $birthdateFormated->day;
                            $secret = "{$day}{$month}{$year}";
                        }
                    }else{
                        $year = $birthdateFormated->year;
                        $month = $birthdateFormated->month;
                        $day = $birthdateFormated->day;
                        $secret = "{$day}{$month}{$year}";
                    }
                }else{
                    $year = $birthdateFormated->year;
                    $month = $birthdateFormated->month;
                    $day = $birthdateFormated->day;
                    $secret = "{$day}{$month}{$year}";
                }
            }else{
                $year = $birthdateFormated->year;
                $month = $birthdateFormated->month;
                $day = $birthdateFormated->day;
                $secret = "{$day}{$month}{$year}";
            }*/
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
            'birthdate' => $request->get('birthdate'),
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
            $amount_from_converted_point = $this->convertPointToAmount($point_balance);
            if ($amount_from_converted_point == null){
                DB::rollback();
                return back()->withErrors(['error' => 'Aucune regle de conversion trouvee']);
            }
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
                'amount_from_converted_point' => $amount_from_converted_point,
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

    public function clientDetails($clientId){
        $client = Client::where('id', $clientId)->first();
        $user = User::where('id', $client->registered_by)->first();
        return view('client.client-details', ['client' => $client, 'user' => $user]);
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

    public function convertPointToAmount($point){
        $lastConversion = Conversion::where('is_applicable', true)->orderBy('created_at', 'desc')->first();
        if ($lastConversion == null) {
            return null;
        }
        return ($point *  $lastConversion -> point_to_amount_amount)/$lastConversion -> point_to_amount_point;
    }
}
