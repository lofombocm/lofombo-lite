<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSendEMailVoucherAvailableJob;
use App\Models\Client;
use App\Models\Config;
use App\Models\ConversionAmountPoint;
use App\Models\LineItem;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltytransaction;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Reward;
use App\Models\Threshold;
use App\Models\Transactiontype;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        return view('purchase.index');
    }

    public function showProductsToUser()
    {
        return view('purchase.purchases-products');
    }

    public function registerPurchaseBackup(Request $request){

        $validator = Validator::make($request->all(), [
            'clientid' => 'required|string|max:255|min:2|exists:clients,telephone',
            'amount' => 'required|numeric|min:1',
            'transactiontypeid' => 'required|string|min:2|max:255',
        ]);

        if($validator->fails()){
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $purchaseDetails = 'Achat d\'un montant de: ' . $request->get('amount');

        $amount = floatval(trim($request->get('amount')));

        $theclient = Client::where('telephone', $request->get('clientid'))->where('active', true)->first();
        if(!$theclient){
            return back()->withErrors(['error' => 'Aucun client avec le numero ' . $request->get('clientid') . ' n\'existe pas dans le systeme']);
        }

        $clientId = $theclient->id;
        $clientBithDate = $theclient->birthdate;
        $clientEmail = $theclient->email;
        $clientName = $theclient->name;

        $loyaltyaccount = Loyaltyaccount::where('holderid', $theclient->id)->first();
        $loyaltyPointBalance = $loyaltyaccount->point_balance;
        $loyaltyId = $loyaltyaccount->id;
        $loyaltyAmountBalance = $loyaltyaccount->amount_balance;
        //$loyaltyAccontBalance = $loyaltyaccount->point_balance;

        $conversionAmountPoint = ConversionAmountPoint::where('is_applicable', true)->first();
        $birthdate_rate = $conversionAmountPoint->birthdate_rate;
        $minAmount = $conversionAmountPoint->min_amount;
        $conversionAmountPointId = $conversionAmountPoint->id;

        $threshold = Threshold::where('is_applicable', true)->first();
        $thresholdGold = $threshold->gold_threshold;
        $thresholdPremium = $threshold->premium_threshold;
        $thresholdClassic = $threshold->classic_threshold;

        $transactiontype = Transactiontype::where('id', $request->get('transactiontypeid'))->where('active', true)->first();
        if(!$transactiontype){
            return back()->withErrors(['error' => 'Aucun type de transaction avec l\'ID  \'' . $request->get('transactiontypeid') . '\'.']);
        }

        $signe = intval($transactiontype->signe);

        /*
             $fp = fopen('signe.txt', 'w');
            fwrite($fp, $signe);
            fclose($fp);
         */

        $purchaseId = Str::uuid()->toString();

        $purchaseData = [
            'id' => $purchaseId,
            'clientid' =>$clientId,
            'amount' => $amount,
            'receiptnumber' => $request->get('receiptnumber'),
            'products' => json_encode([])
        ];

        /*session()->flash('error', json_encode($theclient));
        return back()->withErrors(['error' => json_encode($theclient)]);*/

        DB::beginTransaction();
        try {
            //dd($purchaseData);
            $purchase = Purchase::create($purchaseData);

            $purchaseAmount = $purchase->amount;

            $isApplicableBirthdate = false;

            if ($clientBithDate != null) {
                $birthdate = Carbon::parse($clientBithDate);
                $birthdateMonth = $birthdate->month;
                $birthdateDay = $birthdate->day;
                $maintenant = Carbon::now();
                $maintenantMonth = $maintenant->month;
                $maintenantDay = $maintenant->day;

                if($birthdateMonth == $maintenantMonth && $birthdateDay == $maintenantDay){
                    $isApplicableBirthdate = true;
                }
            }
            $rate = 1;
            if($isApplicableBirthdate){
                $rate = $birthdate_rate;
                if($rate < 1){
                    $rate = 1;
                }
            }

            $pointToBeAdded = ($loyaltyAmountBalance === (double)0) ? $loyaltyPointBalance : 0;
            $totalPoint = floor($rate * intdiv($purchaseAmount + $loyaltyAmountBalance, $minAmount)) + $pointToBeAdded; // applique pour ne pas avoir quelqu'un qui a un solde de montant eleve et n'a pas de points
            $point = floor($rate * intdiv($purchaseAmount, $minAmount));

            //$totalPoint = $loyaltyPointBalance + $signe * $point;

            if ($totalPoint > $thresholdGold){
                /// TODO: Send SMS and email notification to client.
                $link = url('').'/auth/client' ;
                if ($clientEmail){
                    $data = ['email' => $clientEmail, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'GOLD'];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);
                }
                $data = ['email' => Auth::user()->email, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'GOLD'];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);

                /// TODO: Generate voucher.

            }

            if ($totalPoint < $thresholdGold && $totalPoint >= $thresholdPremium){
                /// TODO: Send SMS and email notification to client.
                $link = url('').'/auth/client' ;
                if ($clientEmail){
                    $data = ['email' => $clientEmail, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'PREMIUM'];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);
                }
                $data = ['email' => Auth::user()->email, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'PREMIUM'];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);
            }

            if ($totalPoint < $thresholdPremium && $totalPoint >= $thresholdClassic){
                /// TODO: Send SMS and email notification to client.
                $link = url('').'/auth/client' ;
                if ($clientEmail){
                    $data = ['email' => $clientEmail, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'CLASSIC'];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);
                }
                $data = ['email' => Auth::user()->email, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'CLASSIC'];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);
            }

            $transactionid = Str::uuid()->toString();
            Loyaltytransaction::create(
                [   'id' => $transactionid,
                    'date' => Carbon::now(),
                    'loyaltyaccountid' => $loyaltyId,
                    'conversionid' => $conversionAmountPointId,
                    'sellerid' => Auth::user()->id,
                    'purchaseid' => $purchaseId,
                    'amount' => $purchaseAmount,
                    'point' => $point,
                    'old_point' => $loyaltyPointBalance,
                    'transactiontypeid' => $request->get('transactiontypeid'), //env('TRANSACTIONTYPEID_PURCHASE'),
                    'transactiondetail' => $purchaseDetails,
                    'clienttransactionid' => $clientId
                ]
            );

            $loyaltyaccount->update(
                [
                    'amount_balance' => $loyaltyAmountBalance + $signe * $purchaseAmount,
                    'point_balance' => $totalPoint,
                    'current_point' => $loyaltyPointBalance
                ]);

        }catch (\Exception $exception){
            DB::rollback();
            return back()->withErrors(['error' => $exception->getMessage() . '   ' . $exception->getLine()]);
            //return back()->withErrors(['error' => $e->getMessage()]);
        }
        //Auth::guard('client')->login($client);
        DB::commit();

        session()->flash('status', 'Achat enregistre avec succes!');
        return redirect("/home");//->withSuccess(['status' => 'Achat enregistre avec succes.', 'purchase' => $purchase]);

    }


    public function registerPurchase(Request $request){
        //return json_encode($request->all());
        //session()->flash('error', $request->get('clientid'));
        //return back()->withErrors(['error' => $request->get('clientid')]);
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'clientid' => 'required|string|max:255|min:2|exists:clients,telephone',
            'amount' => 'required|numeric|min:1',
            'transactiontype' => 'required|string|min:2|max:255',
            'receiptnumber' => 'required|string|max:255|min:2|unique:purchases,receiptnumber',
        ]);
        if($validator->fails()){
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $numitem = intval($request->get('numitem'));

        //$productname0 = trim($request->get('productname0'));
        //$unitprice0 = floatval(trim($request->get('unitprice0')));
        //$quantity0 = intval(trim($request->get('quantity0')));

        //$lineitem = LineItem::createLineItem($productname0, $quantity0, $unitprice0, $unitprice0 * $quantity0);
        $items = [];
        $sum = 0;//$lineitem->total;
        $purchaseDetails = 'Achat d\'un montant de: ' . $request->get('amount');
        $noms = [];
        //$itemArray = [];
        for($i = 0; $i < $numitem; $i++){
            $productname = trim($request->get('productname' . "$i"));
            $unitprice = floatval(trim($request->get('unitprice' . "$i")));
            $quantity = intval(trim($request->get('quantity' . "$i")));
            $total = $unitprice * $quantity;
            $sum += $total;
            array_push($items, LineItem::createLineItem($productname, $quantity, $unitprice, $total));
            //array_push($itemArray, ['name' => $productname, 'quantity' => $quantity, 'price' => $unitprice, 'total' => $total]);
            array_push($noms, $productname);
        }
        //dd($sum);
        $now = Carbon::now();

        $purchaseDetails .= ' des produits : ' . join(', ', $noms) . '. Pour un montant total de: ' . $sum . '. Enregistre le: ' . $now;
        $amount = doubleval(trim($request->get('amount')));
        if ($numitem > 0){
            //dd(['numitem' => $numitem, 'items' => $items, 'amount' => $amount, 'sum' => $sum]);
            if (!($sum === $amount)){
                return back()->withErrors(['error' => 'Achat invalide: Le total des  differents produits est differents du montant de l \'achat.']);
            }
        }


        $theclient = Client::where('telephone', $request->get('clientid'))->where('active', true)->first();
        if(!$theclient){
            return back()->withErrors(['error' => 'Aucun client avec le numero ' . $request->get('clientid') . ' n\'existe pas dans le systeme']);
        }

       /* session()->flash('error', $request->get('clientid'));
        return back()->withErrors(['error' => $request->get('clientid')]);*/

        $loyaltyaccount = Loyaltyaccount::where('holderid', $theclient->id)->where('active', true)->first();

        $config = Config::where('is_applicable', true)->first();

        //$threshold = Threshold::where('is_applicable', true)->first();

        $transactiontype = $request->get('transactiontype'); //Transactiontype::where('id', $request->get('transactiontype'))->first();

        $clientId = $theclient->id;
        $clientBithDate = $theclient->birthdate;
        $clientEmail = $theclient->email;
        $clientName = $theclient->name;

        //$loyaltyaccount = Loyaltyaccount::where('holderid', $theclient->id)->where('active', true)->first();
        $loyaltyPointBalance = $loyaltyaccount->point_balance;
        $loyaltyId = $loyaltyaccount->id;
        $loyaltyAmountBalance = $loyaltyaccount->amount_balance;
        //$loyaltyAccontBalance = $loyaltyaccount->point_balance;

        //$conversionAmountPoint = ConversionAmountPoint::where('is_applicable', true)->first();
        $birthdate_rate = $config->birthdate_bonus_rate;
        $amount_per_point = $config->amount_per_point;
        //$conversionAmountPointId = $conversionAmountPoint->id;

        $levels = json_decode($config->levels); //Threshold::where('is_applicable', true)->first();
        //$thresholdGold = $threshold->gold_threshold;
        //$thresholdPremium = $threshold->premium_threshold;
        //$thresholdClassic = $threshold->classic_threshold;

        /*$transactiontype = Transactiontype::where('id', $request->get('transactiontypeid'))->where('active', true)->first();
        if(!$transactiontype){
            return back()->withErrors(['error' => 'Aucun type de transaction avec l\'ID  \'' . $request->get('transactiontypeid') . '\'.']);
        }*/

        /*
             $fp = fopen('signe.txt', 'w');
            fwrite($fp, $signe);
            fclose($fp);
         */

        $purchaseId = Str::uuid()->toString();

        /*$purchaseData = [
            'id' => $purchaseId,
            'clientid' =>$clientId,
            'amount' => $amount,
            'receiptnumber' => $request->get('receiptnumber'),
            'products' => json_encode([])
        ];*/

        DB::beginTransaction();

        //session()->flash('error', 'ID: ' . $client->id);
        //return back()->withErrors(['error' => 'ID: ' . $client->id]);
        $purchase = null;
        try {
            $products = [];

            foreach($items as $item){
                try {
                    $productid =  Str::uuid()->toString();
                    $prod = Product::where('name',  strtoupper($item->name))->first();
                    if(!$prod){
                        $product = Product::Create([
                            'id' => $productid,
                            'name' => strtoupper($item->name),
                            'price' => $item->price,
                            'others' => '' . $item->total,
                        ]);
                        array_push($products, $product);
                    }

                }catch (\Exception $exception){
                    DB::rollBack();
                    return back()->withErrors(['error' => $exception->getMessage()]);
                }
            }

            $purchaeId = Str::uuid()->toString();
            $purchase = new Purchase(
                $purchaeId, $theclient->id, $amount, $request->get('receiptnumber'), json_encode($products)
            );
            $purchase->save();
            $purchaseAmount = $purchase->amount;

            $isApplicableBirthdate = false;


            if ($clientBithDate != null) {
                $birthdate = Carbon::parse($clientBithDate);
                $birthdateMonth = $birthdate->month;
                $birthdateDay = $birthdate->day;
                $maintenant = Carbon::now();
                $maintenantMonth = $maintenant->month;
                $maintenantDay = $maintenant->day;

                if($birthdateMonth == $maintenantMonth && $birthdateDay == $maintenantDay){
                    $isApplicableBirthdate = true;
                }
            }
            $rate = 1;
            if($isApplicableBirthdate === true){
                $rate = $birthdate_rate;
                if($rate < 1){
                    $rate = 1;
                }
            }

            //$pointToBeAdded = ($loyaltyAmountBalance === (double)0) ? $loyaltyPointBalance : 0;
            //$amount_per_point
            $totalPoint = floor(($rate * $purchaseAmount + $loyaltyAmountBalance) / $amount_per_point); // applique pour ne pas avoir quelqu'un qui a un solde de montant eleve et n'a pas de points
            $point = floor($rate * $purchaseAmount / $amount_per_point);
            $montantTransaction = $rate * $purchaseAmount;

            $transactionid = Str::uuid()->toString();
            Loyaltytransaction::create(
                [   'id' => $transactionid,
                    'date' => Carbon::now(),
                    'loyaltyaccountid' => $loyaltyId,
                    'configid' => $config->id,
                    'madeby' => Auth::user()->id,
                    'reference' => 'ENREGISTREMENT ACHAT',
                    'amount' => $amount,
                    'point' => $point,
                    'old_amount' => $loyaltyAmountBalance,
                    'old_point' => $loyaltyPointBalance,
                    'transactiontype' => $request->get('transactiontype'), //env('TRANSACTIONTYPEID_PURCHASE'),
                    'transactiondetail' => $purchaseDetails,
                    'clientid' => $clientId,
                    'products' => json_encode($products)
                ]
            );

            $loyaltyaccount->update(
                [
                    'amount_balance' => $loyaltyAmountBalance + $montantTransaction,
                    'point_balance' => $totalPoint,
                    'current_point' => $loyaltyPointBalance
                ]);


            $configuration = Config::where('is_applicable', true)->first();

            $levels = json_decode($configuration->levels);
            $maxLevel = $levels[0];
            $minLevel = $levels[0];
            foreach ($levels as $level){
                if($level->point > $maxLevel->point && $totalPoint >= $level->point){
                    $maxLevel = $level;
                }
                if($level->point < $minLevel->point && $totalPoint >= $level->point){
                    $minLevel = $level;
                }
            }

            $possibleLevels = [];
            foreach ($levels as $level){
                if ($level->point <= $maxLevel->point && $level->point >= $minLevel->point){
                    array_push($possibleLevels, $level);
                }
            }

            $possibleRewards = [];
            $rewards = Reward::where('active', true)->get();
            foreach ($rewards as $reward){
                $level = json_decode($reward->level);
                $rewardPoint = $level->point;
                if ($totalPoint >= $rewardPoint){
                    array_push($possibleRewards, $reward);
                }
            }

            $link = url('').'/auth/client' ;
            $message = [$theclient->gender . ' '. $theclient->name. ' Vous avez atteint un niveau de points vous permettant de beneficier des recompenses:'];
            //$data = [];
            if ($totalPoint >= $minLevel->point){
                /// TODO: Send SMS and email notification to client.
                foreach ($possibleRewards as $possibleReward){
                    $level = json_decode($possibleReward->level);
                    array_push($message,  '"' . $possibleReward->name . '" pour un bon de niveau "' . $level->name . '" correspondant a ' . $level->point. ' points ');
                }
                if ($clientEmail){
                    $data = ['email' => $clientEmail, 'name' => $clientName, 'clientLoginUrl' => $link, 'msg' => $message];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator = Auth::user()->id;
                    $notifsubject = 'Disponibilite de recompenses au travers des bons';
                    $notifsentat = Carbon::now();
                    $notifbody = json_encode($message);
                    $notifdata = json_encode($data);
                    $notifsender = Auth::user()->name;
                    $notifrecipient = $clientId;
                    $notifsenderaddress = Auth::user()->email;
                    $notifrecipientaddress = $clientEmail;
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
                $data = ['email' => Auth::user()->email, 'name' => $clientName, 'clientLoginUrl' => $link, 'msg' => $message];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);
                $notifid = Str::uuid()->toString();
                $notifgenerator = Auth::user()->id;
                $notifsubject = 'Disponibilite de recompenses au travers des bons';
                $notifsentat = Carbon::now();
                $notifbody = json_encode($message);
                $notifdata = json_encode($data);
                $notifsender = Auth::user()->name;
                $notifrecipient = $clientId;
                $notifsenderaddress = Auth::user()->email;
                $notifrecipientaddress = $theclient->telephone;
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

            /*if ($totalPoint > $thresholdGold){
                /// TODO: Send SMS and email notification to client.
                $link = url('').'/auth/client' ;
                if ($clientEmail){
                    $data = ['email' => $clientEmail, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'GOLD'];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);
                }
                $data = ['email' => Auth::user()->email, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'GOLD'];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);

                /// TODO: Generate voucher.

            }

            if ($totalPoint < $thresholdGold && $totalPoint >= $thresholdPremium){
                /// TODO: Send SMS and email notification to client.
                $link = url('').'/auth/client' ;
                if ($clientEmail){
                    $data = ['email' => $clientEmail, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'PREMIUM'];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);
                }
                $data = ['email' => Auth::user()->email, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'PREMIUM'];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);
            }

            if ($totalPoint < $thresholdPremium && $totalPoint >= $thresholdClassic){
                /// TODO: Send SMS and email notification to client.
                $link = url('').'/auth/client' ;
                if ($clientEmail){
                    $data = ['email' => $clientEmail, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'CLASSIC'];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);
                }
                $data = ['email' => Auth::user()->email, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'CLASSIC'];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);
            }*/

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->withErrors(['error' => $exception->getMessage() . '   ' . $exception->getLine()]);
        }
        session()->flash('status', 'Achat enregistre avec succes!');
        return back()->with('status', 'Achat enregistre avec succes!');//->withSuccess(['status' => 'Achat enregistre avec succes.', 'purchase' => $purchase]);
    }
}



