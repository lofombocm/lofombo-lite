<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Conversion;
use App\Models\ConversionPointReward;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltytransaction;
use App\Models\Reward;
use App\Models\Threshold;
use App\Models\Transactiontype;
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

        /*$thresholds = Threshold::all();
        if (count($thresholds) === 0) {
            $min = 1;
        }else{
            $min = $thresholds[0]->classic_threshold;
            foreach ($thresholds as $threshold) {
                if ($min < $threshold->classic_threshold) {
                    $min = $threshold->classic_threshold;
                }
            }
        }*/

        /*session()->flash('error',  json_encode($request->all()));
        return redirect()->back()->with('error', json_encode($request->all()));*/

        $validator = Validator::make($request->all(), [
            'clientid' => 'required|uuid|exists:clients,id',
            'rewardid' => 'required|uuid|exists:rewards,id',
            'conversionpointrewardid' => 'required|uuid|exists:conversion_point_rewards,id',
            'thresholdid' => 'required|uuid|exists:thresholds,id',
            'level' => 'required|string|in:CLASSIC,PREMIUM,GOLD',
        ]);

        if($validator->fails()){
            session()->flash('error',  $validator->errors()->first());
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $client = Client::where('id', $request->get('clientid'))->where('active', true)->first();
        if ($client === null) {
            session()->flash('error', 'Client desactive');
        }


        $reward = Reward::where('id', $request->get('rewardid'))->first();
        $conversionpointreward = Conversionpointreward::where('id', $request->get('conversionpointrewardid'))->first();
        $threshold = Threshold::where('id', $request->get('thresholdid'))->first();
        $loyaltyAccount = Loyaltyaccount::where('holderid', $client->id)->first();
        $points = $conversionpointreward->min_point;
        $amount = $reward->value;




        $voucherid = Str::uuid()->toString();
        $serialnumber = $this->generateVoucherSerialNumber();
        $clientid  = $client->id;
        $level     = $request->get('level');
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
                'point' => $points,
                'amount' => $amount,
                'enterprise' => $enterprise,
                'expirationdate' => $expirationdate,
                'active' => false,
                'activated_by' => (Auth::check())? Auth::user()->id : $client->registered_by,
                'reward' => $reward->id,
            ];

            $voucher = Voucher::create($data);

            $transactionDetails = 'Generation de bon identifie par: \'' . $voucherid . '\'. Numero de serie: \'' . $serialnumber.
                '\'. Niveau: ' . $request->get('level') . ' Nombre de points: ' . $points . ', Montant: ' . $amount .
                ', Recompense: ' . $reward->name. '. Pour Client: ' . $client->name . '.';
            $transactionid = Str::uuid()->toString();

            $validator1 = Validator::make($request->all(), [
                'transactiontypeid' => 'required|uuid|exists:transactiontypes,id',
            ]);

            $transactiontype = null;
            if($validator1->fails()){
               $transactiontype = Transactiontype::where('signe', -1)->first();
            }else{
                $transactiontype = Transactiontype::where('id', $request->get('transactiontypeid'))->first();
            }

            Loyaltytransaction::create(
                [   'id' => $transactionid,
                    'date' => Carbon::now(),
                    'loyaltyaccountid' => $loyaltyAccount->id,
                    'conversionid' => $conversionpointreward->id,
                    'sellerid' => (Auth::check()) ? Auth::user()->id : $client->registered_by,
                    'purchaseid' => $reward->id,
                    'amount' => $amount,
                    'point' => $points,
                    'amount_from_converted_point' => $amount,
                    'old_point' => $loyaltyAccount->point_balance,
                    'transactiontypeid' => $transactiontype->id, //env('TRANSACTIONTYPEID_GEN_VOUCHER'),
                    'transactiondetail' => $transactionDetails,
                    'clienttransactionid' => $client->id
                ]
            );

            $signe = $transactiontype->signe;
            $newAmount = $loyaltyAccount->amount_balance + $signe * $amount;

            $loyaltyAccount->update([
                    'amount_balance' => ($newAmount < 0) ? 0 : $newAmount,
                    'point_balance' => $loyaltyAccount->point_balance + $signe * $points,
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
