<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GuestController;
use App\Jobs\ProcessSendEMailPurchaseMadeJob;
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


    public function registerPurchase(Request $request){
        //return json_encode($request->all());
        //session()->flash('error', $request->get('clientid'));
        //return back()->withErrors(['error' => $request->get('clientid')]);
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'clientid' => 'required|phone|max:255|min:2|exists:clients,telephone',
            'amount' => 'required|numeric|min:1',
            'transactiontype' => 'required|string|min:2|max:255',
            //'receiptnumber' => 'required|string|max:255|min:2|unique:purchases,receiptnumber',
        ],[
            'clientid.required' => __("Le client est obligatoire"),
            'clientid.exists' => __("Le client n'est pas reconnu"),
            'amount.required' => __("Le montant est obligatoire"),
            'amount.numeric' => __('Montant invalide')
        ]);
        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $receiptnumber = null;
        if ($request->filled('receiptnumber')){
            $validatorNumReceipt = Validator::make($request->all(), [
                'clientid' => 'required|string|max:255|min:2|exists:clients,telephone',
                'amount' => 'required|numeric|min:1',
                'transactiontype' => 'required|string|min:2|max:255',
                'receiptnumber' => 'required|string|max:255|min:2',
            ],[
                'clientid.required' => __("Le client est obligatoire"),
                'clientid.exists' => __("Le client n'est pas reconnu"),
                'amount.required' => __("Le montant est obligatoire"),
                'amount.numeric' => __('Montant invalide'),
                'receiptnumber.required' => __("Le numéro de recu est obligatoire"),
            ]);

            if($validatorNumReceipt->fails()){
                session()->flash('error', $validatorNumReceipt->errors()->first());
                return back()->withErrors(['error' => $validatorNumReceipt->errors()->first()]);
            }

            $receiptnumber = trim($request->get('receiptnumber'));
            $purchases = Purchase::where('receiptnumber', $receiptnumber)->get();
            if ($purchases->count() > 0){
                $msg = __('Un achat a déja été enregistré ave ce numéro de recu.');
                session()->flash('error', $msg);
                session()->flash('purchase', $purchases[0]);
                return back()->withErrors(['error' => $msg]);
            }
        }else{
            $msg = __("Le numéro de recu est obligatoire");
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

        $numitem = intval($request->get('numitem'));

        //$productname0 = trim($request->get('productname0'));
        //$unitprice0 = floatval(trim($request->get('unitprice0')));
        //$quantity0 = intval(trim($request->get('quantity0')));

        //$lineitem = LineItem::createLineItem($productname0, $quantity0, $unitprice0, $unitprice0 * $quantity0);
        $items = [];
        $sum = 0;//$lineitem->total;
        $purchaseDetails = __("Achat d'un montant de: ") . $request->get('amount');
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

        if ($numitem > 0){
            $purchaseDetails .= __(" des produits : ") . join(', ', $noms) . '. ' . __("Pour un montant total de: ") . $sum . '. ' . __("Enregistré le: ") . $now;
        }
        $amount = doubleval(trim($request->get('amount')));
        if ($numitem > 0){
            //dd(['numitem' => $numitem, 'items' => $items, 'amount' => $amount, 'sum' => $sum]);
            if (!($sum === $amount)){
                $msg = __("Achat invalide: Le total des  montants des différents produits est différents du montant de l'achat.");
                session()->flash('error', $msg);
                return back()->withErrors(['error' => $msg]);
            }
        }


        $theclient = Client::where('telephone', $request->get('clientid'))->where('active', true)->first();
        if(!$theclient){
            $msg = __("Client inconnu");
            session()->flash('error', $msg);
            return back()->withErrors(['error' => $msg]);
        }

       /* session()->flash('error', $request->get('clientid'));
        return back()->withErrors(['error' => $request->get('clientid')]);*/

        $loyaltyaccount = Loyaltyaccount::where('holderid', $theclient->id)->where('active', true)->first();

        $config = Config::where('is_applicable', true)->first();
        $transactions = Loyaltytransaction::where('clientid', $theclient->id)->get();



        //$threshold = Threshold::where('is_applicable', true)->first();

        $transactiontype = trim($request->get('transactiontype')); //Transactiontype::where('id', $request->get('transactiontype'))->first();

        $clientId = $theclient->id;
        $clientBithDate = $theclient->birthdate;
        $clientEmail = $theclient->email;
        $clientName = $theclient->name;

        //$loyaltyaccount = Loyaltyaccount::where('holderid', $theclient->id)->where('active', true)->first();
         //Threshold::where('is_applicable', true)->first();
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
                            'others' => '' . $item->quantity,
                        ]);
                        array_push($products, $product);
                    }else{
                        array_push($products, $prod);
                    }

                }catch (\Exception $exception){
                    DB::rollBack();
                    return back()->withErrors(['error' => $exception->getMessage()]);
                }
            }

            $purchaeId = Str::uuid()->toString();
            $purchase = new Purchase(
                $purchaeId, $theclient->id, $amount, trim($request->get('receiptnumber')), json_encode($products)
            );

            $purchase->save();


            if (count($transactions) === 0){
                $p_balance  = $config->initial_loyalty_points;
                $a_balance = $config->amount_per_point * $p_balance;
                $txid = Str::uuid()->toString();

                Loyaltytransaction::create([
                    'id' => $txid,
                    'date' => Carbon::now(),
                    'loyaltyaccountid' => $loyaltyaccount->id,
                    'configid' => $config->id,
                    'madeby' => Auth::user()->id,
                    'reference' => 'Transaction Initiale donnant les points initiaux au client',
                    'amount' => $a_balance,
                    'purchase_amount'  => 0,
                    'gift_amount' => $a_balance,
                    'birthdate_amount' => 0,
                    'point' => $p_balance,
                    'old_amount' => 0.0,
                    'old_point' => "0",
                    'transactiontype' => 'INITIALISATION COMPTE CLIENT',
                    'transactiondetail' => 'Transaction Initiale donnant les points initiaux au client',
                    'clientid' =>  $theclient->id,
                    'products' => '[]'
                ]);

                $loyaltyaccount->amount_balance = $a_balance;
                $loyaltyaccount->gift_amount_balance = $a_balance;
                $loyaltyaccount->point_balance = encrypt($p_balance);
                $loyaltyaccount->current_point = encrypt(0);
                $loyaltyaccount->save();

            }

            $loyaltyaccount = Loyaltyaccount::where('holderid', $theclient->id)->where('active', true)->first();

            $loyaltyPointBalance = $loyaltyaccount->point_balance;
            $loyaltyId = $loyaltyaccount->id;
            $loyaltyAmountBalance = $loyaltyaccount->amount_balance;
            //$loyaltyAccontBalance = $loyaltyaccount->point_balance;

            //$conversionAmountPoint = ConversionAmountPoint::where('is_applicable', true)->first();
            $birthdate_rate = $config->birthdate_bonus_rate;
            $amount_per_point = $config->amount_per_point;
            //$conversionAmountPointId = $conversionAmountPoint->id;

            $levels = json_decode($config->levels);


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
            //$birthdateAmount = 0;

            $birthdateAmount = ($rate - 1) * $purchaseAmount;
            $giftAmount = 0;


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
                    'purchase_amount'  => $purchaseAmount,
                    'gift_amount' => $giftAmount,
                    'birthdate_amount' => $birthdateAmount,
                    'point' => $point,
                    'old_amount' => $loyaltyAmountBalance,
                    'old_point' => intval(strval(decrypt($loyaltyPointBalance))),
                    'transactiontype' => $request->get('transactiontype'), //env('TRANSACTIONTYPEID_PURCHASE'),
                    'transactiondetail' => $purchaseDetails,
                    'clientid' => $clientId,
                    'products' => json_encode($products)
                ]
            );

            $loyaltyaccount->update(
                [
                    'amount_balance' => $loyaltyAmountBalance + $montantTransaction,
                    'purchase_amount_balance' => $loyaltyaccount->purchase_amount_balance + $purchaseAmount,
                    'birthdate_amount_balance' => $loyaltyaccount->birthdate_amount_balance + $birthdateAmount,
                    'point_balance' => encrypt($totalPoint),
                    'current_point' => $loyaltyPointBalance
                ]
            );


            //$configuration = Config::where('is_applicable', true)->first();

            $levels = json_decode($config->levels);
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
                    $possibleRewards[] = $reward;
                }
            }

            $link = url('/'.GuestController::getApplicationLocal().'/auth/client');
            $messagePurchaseMade = [($theclient->gender === 'M' ? __("Monsieur") : __("Madame")) . ' '. $theclient->name. ' ' . __("un") . ' ' . __("achat") . ' ' . __("d'un montant") . ' ' . __("de") . ' ' . $purchaseAmount . ' ' . __("a été enregistré à votre compte")];

            if ($clientEmail){
                $data = ['email' => $clientEmail, 'name' => $clientName, 'clientLoginUrl' => $link, 'msg' => $messagePurchaseMade];
                ProcessSendEMailPurchaseMadeJob::dispatch($data);

                $notifid = Str::uuid()->toString();
                $notifgenerator = Auth::user()->id;
                $notifsubject = __('Enregistrement Achat');
                $notifsentat = Carbon::now();
                $notifbody = json_encode($messagePurchaseMade);
                $notifdata = json_encode($data);
                $notifsender = Auth::user()->name;
                $notifrecipient = $clientId;
                $notifsenderaddress = Auth::user()->email;
                $notifrecipientaddress = $clientEmail;

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

            $message = [($theclient->gender === 'M' ? __("Monsieur") : __("Madame")) . ' '. $theclient->name. ' ' . __("Vous avez atteint un niveau de points vous permettant de bénéficier des récompenses:")];
            //$data = [];
            if ($totalPoint >= $minLevel->point){
                /// TODO: Send SMS and email notification to client.
                foreach ($possibleRewards as $possibleReward){
                    $level = json_decode($possibleReward->level);
                    array_push($message,  '"' . $possibleReward->name . ' ' . __("pour") . '"' . __("un bon de type") . ' "' . $level->name . '" ' . __("correspondant à") . ' ' . $level->point. ' points ');
                }
                if ($clientEmail){
                    $data = ['email' => $clientEmail, 'name' => $clientName, 'clientLoginUrl' => $link, 'msg' => $message];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);

                    $notifid = Str::uuid()->toString();
                    $notifgenerator = Auth::user()->id;
                    $notifsubject = __('Disponibilité de récompenses à travers les Bons de Fidélité');
                    $notifsentat = Carbon::now();
                    $notifbody = json_encode($message);
                    $notifdata = json_encode($data);
                    $notifsender = Auth::user()->name;
                    $notifrecipient = $clientId;
                    $notifsenderaddress = Auth::user()->email;
                    $notifrecipientaddress = $clientEmail;
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
                $data = ['email' => Auth::user()->email, 'name' => $clientName, 'clientLoginUrl' => $link, 'msg' => $message];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);
                $notifid = Str::uuid()->toString();
                $notifgenerator = Auth::user()->id;
                $notifsubject = __('Disponibilité de récompenses à travers les Bons de Fidélité');
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

        $msg = __("Enregistrement Achat réussi");
        session()->flash('status', $msg);
        return back()->with('status', $msg);//->withSuccess(['status' => 'Achat enregistre avec succes.', 'purchase' => $purchase]);
    }
}



