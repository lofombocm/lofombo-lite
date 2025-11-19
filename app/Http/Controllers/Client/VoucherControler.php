<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSendEMailVoucherGeneratedJob;
use App\Jobs\ProcessSendSMSVoucherUsageCodeJob;
use App\Models\Client;
use App\Models\Config;
use App\Models\Conversion;
use App\Models\ConversionPointReward;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltytransaction;
use App\Models\Notification;
use App\Models\Reward;
use App\Models\Threshold;
use App\Models\Transactiontype;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsageCode;
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

        $usagecodeid = Str::uuid()->toString();
        $usagecode = $this->generateVoucherUsageCode();


        $clientid  = $client->id;
        $niveau     = $level->name;
        $enterprise = $config->enterprise_name;//env('ENTERPRISE');
        //VOUCHER_EXPIRATION_DATE_IN_MONTH

        $nummonth = $config->voucher_duration_in_month;//intval(env('VOUCHER_EXPIRATION_DATE_IN_MONTH'));
        /*if (!($config === null)){
            $nummonth = $config->voucher_duration_in_month;
        }*/
        $expirationdate = Carbon::now()->addMonths($nummonth);

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
                'activated_at' => Carbon::now(),
            ];
            $voucher = Voucher::create($data);

            $encryptedCode = encrypt($usagecode);
            $codeData = [
              'id' => $usagecodeid,
              'code' => $encryptedCode,
              'voucherid' =>  $voucherid,
              'expired_at' => $expirationdate
            ];
            $voucherUsageCode = VoucherUsageCode::create($codeData);


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

            $link = url('').'/client/'. $clientid . '/vouchers' ;

            $smsData = [
                'to' => '237' . $client->telephone,
                'message' => 'Le numero de serie du bon genere est: ' . $serialnumber . ' et le code d\'utilisation est: ' . decrypt($voucherUsageCode->code),
            ];
            ProcessSendSMSVoucherUsageCodeJob::dispatch($smsData);

            //dd(Auth::check());
            if (Auth::check()) {
                if ($client->email != null) {
                    $message = [$client->gender . ' ' . $client->name . ', un bon de niveau ' . $voucher->level . ' a ete genere a votre compte.'];
                    $emaildata = ['email' =>$client->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message,
                        'code' => decrypt($voucherUsageCode->code)];
                    //dd($emaildata);
                    ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator = '' . Auth::user()->id . '';
                    $notifsubject = 'Generation de Bon';
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
                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    $message = ['Mme/M. ' . ' ' .  $admin->name . ', le client ' .  $client->name . ' a genere un bon de niveau ' . $voucher->level . '.'];
                    $emaildata = ['email' =>$admin->email, 'name' =>  $admin->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message];
                    //dd($emaildata);
                    ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator =$client->id;
                    $notifsubject = 'Generation de Bon';
                    $notifsentat = Carbon::now();
                    $notifbody = json_encode($message);
                    $notifdata = json_encode($emaildata);
                    $notifsender = $client->name;
                    $notifrecipient = $admin->name;
                    $notifsenderaddress = $client->email == null ? Auth::user()->email : $client->email;
                    $notifrecipientaddress = $admin->email;
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
            }else{

                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    $message = ['Mme/M. ' . ' ' .  $admin->name . ', le client ' .  $client->name . ' a genere un bon de niveau ' . $voucher->level . '.'];
                    $emaildata = ['email' =>$admin->email, 'name' =>  $admin->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message];
                    //dd($emaildata);
                    ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator =$client->id;
                    $notifsubject = 'Generation de Bon';
                    $notifsentat = Carbon::now();
                    $notifbody = json_encode($message);
                    $notifdata = json_encode($emaildata);
                    $notifsender = $client->name;
                    $notifrecipient = $admin->name;
                    $notifsenderaddress = $client->email != null ? $client->email : $admin->email;
                    $notifrecipientaddress = $admin->email;
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

                if ($client->email != null) {
                    $message = [$client->gender . ' ' . $client->name . ', un bon de niveau ' . $voucher->level . ' a ete genere a votre compte.'];
                    $emaildata = ['email' =>$client->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message,
                        'code' => decrypt($voucherUsageCode->code)];
                    //dd($emaildata);
                    ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator = '' . $client->id . '';
                    $notifsubject = 'Generation de Bon';
                    $notifsentat = Carbon::now();
                    $notifbody = json_encode($message);
                    $notifdata = json_encode($emaildata);
                    $notifsender = $client->name;
                    $notifrecipient = $client->name;
                    $notifsenderaddress = $client->email;
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

            }


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

    public function generateVoucherUsageCode():string
    {
        $numberFormated = null;
        do {
            $number = random_int(10000000, 99999999);
            $numberStr = (string) $number;
            $numberFormated = implode("-", str_split($numberStr, 4));
        } while (VoucherUsageCode::where("code", "=", $numberFormated)->first());

        return $numberFormated;
    }
}
