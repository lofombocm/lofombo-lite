<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GuestController;
use App\Jobs\ProcessSendEMailVoucherGeneratedJob;
use App\Models\Client;
use App\Models\Config;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltytransaction;
use App\Models\Notification;
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

    public function __construct()
    {
        //$this->middleware('auth:client');
    }


    public function getVoucherView(){
        if (!Auth::guard('client')->check()) {
            return redirect()->route('authentification.client')->with('error',__("Access Denied"));
        }
        return view('client.voucher.index');
    }

    public function getVouchers(){
        if (!Auth::guard('client')->check()) {
            return redirect()->route('authentification.client')->with('error',__("Access Denied"));
        }
        return view('client.voucher.list');
    }


    public function postGenVoucher(Request $request){

        if (!Auth::check() && !Auth::guard('client')->check()) {
            return redirect()->route('authentification.client')->with('error',__("Access Denied"));
        }

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
            'level' => 'required|string',
            'transactiontype' => 'required|string|min:2|max:255',
        ],[
            'level.required' => __('Le niveau de bon est requis'),
        ]);

        if($validator->fails()){
            session()->flash('error',  $validator->errors()->first());
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $client = Client::where('id', $request->get('clientid'))->where('active', true)->first();
        if ($client === null) {
            session()->flash('error', __("Code invalide"));
            return redirect()->back()->with('error', __("Code invalide"));
        }

        $level = json_decode($request->get('level'));
        $configid = $level->config;
        $config = Config::where('id', $configid)->first();
        if ($config === null) {
            session()->flash('error', __("Une erreur est survenue, reessayez de nouveau."));
            return redirect()->back()->with('error', __("Une erreur est survenue, reessayez de nouveau."));
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

            $transactionDetails = __("Génération d'un Bon de Fidélité") . ' ' . __("identifié par") .' : \'' . $voucherid . '\'' . __("Numéro de série") . ': ' . $serialnumber.
                '\'. '. __("Type de bon") . ': ' . $niveau . ' Points: ' . $points . '. ' . __("Pour le client") . ': ' . $client->name . '.';

            $transactionid = Str::uuid()->toString();

            $loyaltyAmountBalance = $loyaltyAccount->amount_balance;
            $loyaltyPointBalance = $loyaltyAccount->point_balance;

            Loyaltytransaction::create(
                [   'id' => $transactionid,
                    'date' => Carbon::now(),
                    'loyaltyaccountid' => $loyaltyAccount->id,
                    'configid' => $config->id,
                    'madeby' => Auth::check() ? '' . Auth::user()->id : (Auth::guard('client')->check() ? Auth::guard('client')->user()->id : 'UNKNOWN'),
                    'reference' => __("GENERATION DE BON"),
                    'amount' => $amount,
                    'purchase_amount'  => 0,
                    'gift_amount' => 0,
                    'birthdate_amount' => 0,
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
                    'point_balance' => encrypt(strval(intval(strval(decrypt($loyaltyAccount->point_balance))) - $points)),
                    'current_point' => $loyaltyAccount->point_balance
                ]);

            $link = url('/'.GuestController::getApplicationLocal().'/client/'. $clientid . '/vouchers');

            /*$smsData = [
                'to' => '237' . $client->telephone,
                'message' => __("Le numéro de série du bon généré est") . ': ' . $serialnumber . ' ' . __("et le code d'utilisation est") . ': ' . decrypt($voucherUsageCode->code),
            ];
            ProcessSendSMSVoucherUsageCodeJob::dispatch($smsData);*/

            if($config->trusted_email !== null && $config->trusted_email !== ""){
              $trusted_email = $config->trusted_email;
                $message = [__("Le client") . ' ' .  $client->name . ' ' . __("a généré") . ' '  . __("un bon de type") . ' '. $voucher->level . '. ' . __("Le code d'utilisation est").': ' . decrypt($voucherUsageCode->code)];
                $emaildata = ['email' =>$trusted_email, 'name' =>  $client->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message];
                ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);
            }

            if ($client->email != null) {
                if (!Auth::check()) {
                    $user = User::where('id',$client->registered_by)->first();
                }else{
                    $user = Auth::user();
                }
                $message = [($client->gender === 'M' ? __("Monsieur") : __("Madame"))  . ' ' . $client->name . ', ' . __("un bon de type") . ' ' . $voucher->level . ' ' . __("a été généré à votre compte") . '.'];
                $emaildata = ['email' =>$client->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message,
                    'code' => decrypt($voucherUsageCode->code)];
                //dd($emaildata);
                ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);

                $notifid = Str::uuid()->toString();
                $notifgenerator = $user->id ;
                $notifsubject = __("Génération d'un Bon de Fidélité");
                $notifsentat = Carbon::now();
                $notifbody = json_encode($message);
                $notifdata = json_encode($emaildata);
                $notifsender = $user->name;
                $notifrecipient = $client->name;
                $notifsenderaddress = $user->email;
                $notifrecipientaddress = $client->email;
                //$notifread = false;

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
                        'read' => false,
                    ]
                );
            }
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                $message = ['Mme/M. ' . ' ' .  $admin->name . ', le client ' .  $client->name . ' ' . __("a généré") . ' '  . __("un bon de type") . ' '. $voucher->level . '.'];
                $emaildata = ['email' =>$admin->email, 'name' =>  $admin->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message];
                //dd($emaildata);
                ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);

                $notifid = Str::uuid()->toString();
                $notifgenerator =$client->id;
                $notifsubject = __("Génération d'un Bon de Fidélité");
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
            //dd(Auth::check());
            /*if (Auth::check()) {
                if ($client->email != null) {
                    $message = [$client->gender . ' ' . $client->name . ', ' . __("un bon de type") . ' ' . $voucher->level . ' ' . __("a été généré à votre compte") . '.'];
                    $emaildata = ['email' =>$client->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message,
                        'code' => decrypt($voucherUsageCode->code)];
                    //dd($emaildata);
                    ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator = '' . Auth::user()->id . '';
                    $notifsubject = __("Génération d'un Bon de Fidélité");
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
                    $message = ['Mme/M. ' . ' ' .  $admin->name . ', le client ' .  $client->name . ' ' . __("a généré") . ' '  . __("un bon de type") . ' '. $voucher->level . '.'];
                    $emaildata = ['email' =>$admin->email, 'name' =>  $admin->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message];
                    //dd($emaildata);
                    ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator =$client->id;
                    $notifsubject = __("Génération d'un Bon de Fidélité");
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
                    $message = ['Mme/M. ' . ' ' .  $admin->name . ', le client ' .  $client->name . ' ' . __("a généré un bon de type") . ' '. $voucher->level . '.'];
                    $emaildata = ['email' =>$admin->email, 'name' =>  $admin->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message];
                    //dd($emaildata);
                    ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator =$client->id;
                    $notifsubject = __("Génération d'un Bon de Fidélité");
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
                    $message = [$client->gender . ' ' . $client->name . ', ' . __("un bon de type") . ' ' . $voucher->level . ' ' . __("a été généré à votre compte") .'.'];
                    $emaildata = ['email' =>$client->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'level' => $voucher->level, 'msg' => $message,
                        'code' => decrypt($voucherUsageCode->code)];
                    //dd($emaildata);
                    ProcessSendEMailVoucherGeneratedJob::dispatch($emaildata);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator = '' . $client->id . '';
                    $notifsubject = __("Génération d'un Bon de Fidélité");
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

            }*/


        }catch (\Exception $exception){
            DB::rollBack();
            session()->flash('error',  $exception->getMessage());
            return redirect()->back()->with('error', $exception->getMessage());
        }

        DB::commit();

        $msg = __('Bon généré avec succès.');
        session()->flash('status', $msg);
        return redirect()->back()->with('status', $msg);
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
