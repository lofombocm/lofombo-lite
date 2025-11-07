<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Config;
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
            'clientid' => 'required|uuid|exists:clients,id',/*
            'rewardid' => 'required|uuid|exists:rewards,id',
            'conversionpointrewardid' => 'required|uuid|exists:conversion_point_rewards,id',
            'thresholdid' => 'required|uuid|exists:thresholds,id',*/
            'level' => 'required|string',
            'transactiontype' => 'required|string|min:2|max:255',
        ]);

        if($validator->fails()){
            session()->flash('error',  $validator->errors()->first());
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $client = Client::where('id', $request->get('clientid'))->where('active', true)->first();
        if ($client === null) {
            session()->flash('error', 'Client desactive');
            return redirect()->back()->with('error', 'Client desactive');
        }

        $level = json_decode($request->get('level'));
        $configid = $level->config;
        $config = Config::where('id', $configid)->first();
        if ($config === null) {
            session()->flash('error', 'Niveau non configure');
            return redirect()->back()->with('error', 'Niveau non configure');
        }

        //$reward = Reward::where('id', $request->get('rewardid'))->first();
        //$conversionpointreward = Conversionpointreward::where('id', $request->get('conversionpointrewardid'))->first();
        //$threshold = Threshold::where('id', $request->get('thresholdid'))->first();
        $loyaltyAccount = Loyaltyaccount::where('holderid', $client->id)->first();
        $points = $level->point; //$conversionpointreward->min_point;
        $amount = $points * $config->amount_per_point;//$reward->value;

        $voucherid = Str::uuid()->toString();
        $serialnumber = $this->generateVoucherSerialNumber();

        $clientid  = $client->id;
        $niveau     = $level->name;
        $enterprise = $config->enterprise_name;//env('ENTERPRISE');
        //VOUCHER_EXPIRATION_DATE_IN_MONTH

        $nummonth = $config->voucher_duration_in_month;//intval(env('VOUCHER_EXPIRATION_DATE_IN_MONTH'));
        /*if (!($config === null)){
            $nummonth = $config->voucher_duration_in_month;
        }*/
        $expirationdate = Carbon::now()->addMinutes($nummonth * 1440 * 30);

        DB::beginTransaction();

        $voucher = null;
        try {

            $data = [
                'id' => $voucherid,
                'serialnumber' => $serialnumber,
                'clientid' => $clientid,
                'level' => $niveau,
                'point' => $points,
                'amount' => $amount,
                'enterprise' => $enterprise,
                'expirationdate' => $expirationdate,
                'active' => false,
                'activated_by' => (Auth::check())? Auth::user()->id : $client->registered_by,
            ];

            $voucher = Voucher::create($data);

           /* $transactionDetails = 'Generation de bon identifie par: \'' . $voucherid . '\'. Numero de serie: \'' . $serialnumber.
                '\'. Niveau: ' . $niveau . ' Nombre de points: ' . $points . ', Montant: ' . $amount .
                '. Pour le client: ' . $client->name . '.';*/

            $transactionDetails = 'Generation de bon identifie par: \'' . $voucherid . '\'. Numero de serie: \'' . $serialnumber.
                '\'. Niveau: ' . $niveau . ' Nombre de points: ' . $points . '. Pour le client: ' . $client->name . '.';

            $transactionid = Str::uuid()->toString();

            $loyaltyAmountBalance = $loyaltyAccount->amount_balance;
            $loyaltyPointBalance = $loyaltyAccount->point_balance;

            Loyaltytransaction::create(
                [   'id' => $transactionid,
                    'date' => Carbon::now(),
                    'loyaltyaccountid' => $loyaltyAccount->id,
                    'configid' => $config->id,
                    'madeby' => Auth::check() ? '' . Auth::user()->id : (Auth::guard('client')->check() ? Auth::guard('client')->user()->id : 'UNKNOWN'),
                    'reference' => 'GENERATION DE BON',
                    'amount' => $amount,
                    'point' => $points,
                    'old_amount' => $loyaltyAmountBalance,
                    'old_point' => $loyaltyPointBalance,
                    'transactiontype' => $request->get('transactiontype'), //env('TRANSACTIONTYPEID_PURCHASE'),
                    'transactiondetail' => $transactionDetails,
                    'clientid' => $clientid,
                    'products' => json_encode([$voucher])
                ]
            );

            $loyaltyAccount->update([
                    'amount_balance' =>  $loyaltyAccount->amount_balance - $amount,
                    'point_balance' => $loyaltyAccount->point_balance - $points,
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
