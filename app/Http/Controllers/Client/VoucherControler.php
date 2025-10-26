<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Conversion;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltytransaction;
use App\Models\Voucher;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VoucherControler extends Controller
{

    public function getVoucherView(){
        return view('client.voucher.index');
    }

    public function postGenVoucher(Request $request){
        $validator = Validator::make($request->all(), [
            'level' => 'required|string|in:GOLD,PREMIUM,CLASSIC',
            'clientid' => 'required|string|exists:clients,id',
            'montant' => 'required|numeric|min:'.env('CLASSIC_THRESHOLD'),
        ]);

        if($validator->fails()){
            session()->flash('error',  $validator->errors()->first());
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $montant = floatval($request->get('montant'));
        $client = Client::where('id', $request->get('clientid'))->first();
        $loyaltyAccount = Loyaltyaccount::where('holderid', $client->id)->first();
        $point_balance = $loyaltyAccount->point_balance;
        $amount_from_converted_point = $loyaltyAccount->amount_from_converted_point;
        $threshold = env($request->get('level') . '_THRESHOLD');
        if ($point_balance < $threshold) {
            session()->flash('error',  'Vous n\'avez pas assez de points pour generer un bon de type ' .$request->get('level'));
            return redirect()->back()->with('error', 'Vous n\'avez pas assez de points pour generer un bon de type ' .$request->get('level'));
        }

        if ($point_balance < $montant) {
            session()->flash('error',  'Montant superieur a votre solde');
            return redirect()->back()->with('error', 'Montant superieur a votre solde');
        }

        $conversion = Conversion::where('is_applicable', true)->first();


        $voucherid = Str::uuid()->toString();
        $serialnumber = $this->generateVoucherSerialNumber();
        $clientid  = $client->id;
        $level     = $request->get('level');
        $point     = intval(($point_balance * $montant) / $amount_from_converted_point);
        $amount    = $montant; //($point * $amount_from_converted_point) / $point_balance;
        $enterprise = env('ENTERPRISE');
        //VOUCHER_EXPIRATION_DATE_IN_MONTH
        $nummonth = intval(env('VOUCHER_EXPIRATION_DATE_IN_MONTH'));
        $expirationdate = Carbon::now()->addMinutes($nummonth * 1440 * 30);

        DB::beginTransaction();

        $voucher = null;
        try {

            $data = [
                'id' => $voucherid,
                'serialnumber' => $serialnumber,
                'clientid' => $clientid,
                'level' => $level,
                'point' => $point,
                'amount' => $amount,
                'enterprise' => $enterprise,
                'expirationdate' => $expirationdate,
                'active' => true,
            ];

            //return $data;

            $voucher = Voucher::create($data);

            //return $voucher;


            $transactionDetails = 'Generation de bon identifie par: \'' . $voucherid . '\'. Numero de serie: \'' . $serialnumber.
                '\'. Niveau: ' . $request->get('level') . ' Nombre de points: ' . $point . ', Montant: ' . $amount .
                '. Pour Client: ' . $client->name . '.';
            $transactionid = Str::uuid()->toString();
            Loyaltytransaction::create(
                [   'id' => $transactionid,
                    'date' => Carbon::now(),
                    'loyaltyaccountid' => $loyaltyAccount->id,
                    'conversionid' => $conversion->id,
                    'sellerid' => $client->registered_by,
                    'purchaseid' => $voucherid,
                    'amount' => $amount,
                    'point' => $point,
                    'amount_from_converted_point' => $amount,
                    'current_point' => $loyaltyAccount->point_balance,
                    'transactiontypeid' => env('TRANSACTIONTYPEID_GEN_VOUCHER'),
                    'transactiondetail' => $transactionDetails,
                    'clienttransactionid' => $client->id,
                    'state' => 'SUCCESS',
                    'returnresult'=>'SUCCESS'
                ]
            );

            $point_balancenew = $point_balance - $point;
            $amount_from_converted_pointnew =  $amount_from_converted_point - $amount;

            $loyaltyAccount->update(
                [
                    'point_balance' => $point_balancenew,
                    'amount_from_converted_point' =>  $amount_from_converted_pointnew,
                    'current_point' => $loyaltyAccount->point_balance
                ]);

        }catch (\Exception $exception){
            DB::rollBack();
            session()->flash('error',  $exception->getMessage());
            return redirect()->back()->with('error', $exception->getMessage());
        }

        DB::commit();

        session()->flash('status', 'Bon genere avec succes.');
        return redirect()->back()->with('status', 'Bon genere avec succes.');

    }

    public function generateVoucherSerialNumber():string
    {
        $numberFormated = null;
        do {
            $number = random_int(100000000000, 999999999999);
            $numberStr = (string) $number;
            $numberFormated = implode("-", str_split($numberStr, 3));
        } while (Voucher::where("serialnumber", "=", $numberFormated)->first());

        return $numberFormated;
    }
}
