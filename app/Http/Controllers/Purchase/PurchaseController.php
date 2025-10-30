<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSendEMailVoucherAvailableJob;
use App\Models\Client;
use App\Models\ConversionAmountPoint;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltytransaction;
use App\Models\Purchase;
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

    public function registerPurchase(Request $request){

        $validator = Validator::make($request->all(), [
            'clientid' => 'required|string|max:255|min:2|exists:clients,telephone',
            'amount' => 'required|numeric|min:1',
            'transactiontypeid' => 'required|string|uuid:4',
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

            if ($clientBithDate) {
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
            $totalPoint = ceil($rate * intdiv($purchaseAmount + $loyaltyAmountBalance, $minAmount)) + $pointToBeAdded; // applique pour ne pas avoir quelqu'un qui a un solde de montant eleve et n'a pas de points
            $point = round($rate * intdiv($purchaseAmount, $minAmount));

            //$totalPoint = $loyaltyPointBalance + $signe * $point;

            if ($totalPoint > $thresholdGold){
                /// TODO: Send SMS and email notification to client.
                $link = env('HOST_WEB_CLIENT_DOMAIN').'/auth/client' ;
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
                $link = env('HOST_WEB_CLIENT_DOMAIN').'/auth/client' ;
                if ($clientEmail){
                    $data = ['email' => $clientEmail, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'PREMIUM'];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);
                }
                $data = ['email' => Auth::user()->email, 'name' => $clientName, 'clientLoginUrl' => $link, 'type' => 'PREMIUM'];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);
            }

            if ($totalPoint < $thresholdPremium && $totalPoint >= $thresholdClassic){
                /// TODO: Send SMS and email notification to client.
                $link = env('HOST_WEB_CLIENT_DOMAIN').'/auth/client' ;
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


    public function registerPurchaseBackup(Request $request){
        //return json_encode($request->all());
        //session()->flash('error', $request->get('clientid'));
        //return back()->withErrors(['error' => $request->get('clientid')]);
        $validator = Validator::make($request->all(), [
            'clientid' => 'required|string|max:255|min:2|exists:clients,telephone',
            'amount' => 'required|numeric|min:1',
            //'numitem' => 'required|numeric|min:1',
            //'productname0' => 'required|string|max:255|min:2',
            //'unitprice0' => 'required|numeric|min:1',
            //'quantity0' => 'required|numeric|min:1',
            'transactiontypeid' => 'required|string|uuid:4',
        ]);
        if($validator->fails()){
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        //$numitem = intval($request->get('numitem'));

        //$productname0 = trim($request->get('productname0'));
        //$unitprice0 = floatval(trim($request->get('unitprice0')));
        //$quantity0 = intval(trim($request->get('quantity0')));

        //$lineitem = LineItem::createLineItem($productname0, $quantity0, $unitprice0, $unitprice0 * $quantity0);
        //$items = [$lineitem];
        //$sum = $lineitem->total;
        $purchaseDetails = 'Achat d\'un montant de: ' . $request->get('amount');
        //$noms = [$productname0];
        //$itemArray = [];
        /*for($i = 1; $i < $numitem; $i++){
            $productname = trim($request->get('productname' . "$i"));
            $unitprice = floatval(trim($request->get('unitprice' . "$i")));
            $quantity = intval(trim($request->get('quantity' . "$i")));
            $total = $unitprice * $quantity;
            $sum += $total;
            array_push($items, LineItem::createLineItem($productname, $quantity, $unitprice, $total));
            //array_push($itemArray, ['name' => $productname, 'quantity' => $quantity, 'price' => $unitprice, 'total' => $total]);
            array_push($noms, $productname);
        }*/

        $now = Carbon::now();

        //$purchaseDetails .= join(', ', $noms) . '. Pour un montant total de: ' . $sum . '. Enregistre le: ' . $now;
        $amount = floatval(trim($request->get('amount')));
        /*if (!($sum === $amount)){
            return back()->withErrors(['error' => 'Achat invalide: Le total des des differents produits est differents du montant de l \'achat.']);
        }*/

        $client = Client::where('telephone', $request->get('clientid'))->first();
        if(!$client){
            return back()->withErrors(['error' => 'Aucun client avec le numero ' . $request->get('clientid') . ' n\'existe pas dans le systeme']);
        }

        session()->flash('error', $request->get('clientid'));
        return back()->withErrors(['error' => $request->get('clientid')]);

        $loyaltyaccount = Loyaltyaccount::where('holderid', $client->id)->first();

        $conversionAmountPoint = ConversionAmountPoint::where('is_applicable', true)->first();

        $threshold = Threshold::where('is_applicable', true)->first();

        $transactiontype = Transactiontype::where('id', $request->get('transactiontypeid'))->first();
        if(!$transactiontype){
            return back()->withErrors(['error' => 'Aucun type de transaction avec l\'ID  \'' . $request->get('transactiontypeid') . '\'.']);
        }

        DB::beginTransaction();

        //session()->flash('error', 'ID: ' . $client->id);
        //return back()->withErrors(['error' => 'ID: ' . $client->id]);
        $purchase = null;
        try {
            $signe = intval($transactiontype->sign);

            //$products = [];

            /*foreach($items as $item){
                try {
                    $productid =  Str::uuid()->toString();
                    $prod = Product::where('name',  $item->name)->first();
                    if(!$prod){
                        $product = Product::Create([
                            'id' => $productid,
                            'name' => $item->name,
                            'price' => $item->price,
                            'others' => '',
                        ]);
                        array_push($products, $product);
                    }

                }catch (\Exception $exception){
                    DB::rollBack();
                    return back()->withErrors(['error' => $exception->getMessage()]);
                }
            }*/

            $purchaeId = Str::uuid()->toString();
            /*$purchase = new Purchase(
                $purchaeId, $client->id, $amount, $request->get('receiptnumber'), json_encode([])
            );
            $purchase = $purchase->save();*/
            $purchase = Purchase::create([
                'id' => $purchaeId,
                'client_id' => $client->id,
                'amount' => $amount,
                'receiptnumber' => $request->get('receiptnumber'),
                'products' => json_encode([])
            ]);

            /*$h = fopen('testo.txt', 'w+');
            fprintf($h, '%s', json_encode($loyaltyaccount) . '\n\n' . json_encode($conversion));
            fclose($h);*/

            /*$purchase = Purchase::create([
                'id' => $purchaeId,
                'clientid' => $client->id,
                'amount' => $amount,
                'receiptnumber' => $request->get('receiptnumber'),
                'products' => json_encode($items)
            ]);*/

            $realAmount = $purchase->amount;

            $birthdate_rate = $conversionAmountPoint->birthdate_rate;
            $isApplicableBirthdate = false;

            if ($client->birthdate){
                $birthdate = Carbon::parse($client->birthdate);
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

           $point = ceil($rate * intdiv($realAmount, $conversionAmountPoint->min_amount));

            $totalPoint = $loyaltyaccount->point_balance + $signe * $point;



            if ($totalPoint > $threshold->gold_threshold){
                /// TODO: Send SMS and email notification to client.
                $link = env('HOST_WEB_CLIENT_DOMAIN').'/auth/client' ;
                if ($client->email){
                    $data = ['email' => $client->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'type' => 'GOLD'];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);
                }
                $data = ['email' => Auth::user()->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'type' => 'GOLD'];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);

                /// TODO: Generate voucher.

            }

            if ($totalPoint < $threshold->gold_threshold && $totalPoint >= $threshold->premium_threshold){
                /// TODO: Send SMS and email notification to client.
                $link = env('HOST_WEB_CLIENT_DOMAIN').'/auth/client' ;
                if ($client->email){
                    $data = ['email' => $client->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'type' => 'PREMIUM'];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);
                }
                $data = ['email' => Auth::user()->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'type' => 'PREMIUM'];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);
            }

            if ($totalPoint < $threshold->premium_threshold && $totalPoint >= $threshold->classic_threshold){
                /// TODO: Send SMS and email notification to client.
                $link = env('HOST_WEB_CLIENT_DOMAIN').'/auth/client' ;
                if ($client->email){
                    $data = ['email' => $client->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'type' => 'CLASSIC'];
                    ProcessSendEMailVoucherAvailableJob::dispatch($data);
                }
                $data = ['email' => Auth::user()->email, 'name' => $client->name, 'clientLoginUrl' => $link, 'type' => 'CLASSIC'];
                ProcessSendEMailVoucherAvailableJob::dispatch($data);
            }

            //$amount_from_converted_point = ($point *  $conversion -> point_to_amount_amount)/$conversion -> point_to_amount_point;
            /*
             $lastConversion = Conversion::where('is_applicable', true)->orderBy('created_at', 'desc')->first();
        return ($point *  $lastConversion -> point_to_amount_amount)/$lastConversion -> point_to_amount_point;
             */
            $transactionid = Str::uuid()->toString();
            Loyaltytransaction::create(
                [   'id' => $transactionid,
                    'date' => Carbon::now(),
                    'loyaltyaccountid' => $loyaltyaccount->id,
                    'conversionid' => $conversionAmountPoint->id,
                    'sellerid' => Auth::user()->id,
                    'purchaseid' => $purchaeId,
                    'amount' => $realAmount,
                    'point' => $point,
                    'old_point' => $loyaltyaccount->point_balance,
                    'transactiontypeid' => $request->get('transactiontypeid'), //env('TRANSACTIONTYPEID_PURCHASE'),
                    'transactiondetail' => $purchaseDetails,
                    'clienttransactionid' => $client->id
                ]
            );

            //$current_point_new = (int)(($loyaltyaccount->amount_balance + $realAmount) * $conversion->amount_to_point_point) / $conversion->amount_to_point_amount;
            //$amount_from_converted_point = ($point *  $conversion -> point_to_amount_amount)/$conversion -> point_to_amount_point;
            $loyaltyaccount->update(
                [
                    'amount_balance' => $loyaltyaccount->amount_balance + $signe * $realAmount,
                    'point_balance' => $loyaltyaccount->point_balance + $signe * $point,
                    'current_point' => $loyaltyaccount->point_balance
                ]);

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->withErrors(['error' => $exception->getMessage() . '   ' . $exception->getLine()]);
        }
        session()->flash('status', 'Achat enregistre avec succes!');
        return redirect("/home");//->withSuccess(['status' => 'Achat enregistre avec succes.', 'purchase' => $purchase]);
    }
}



