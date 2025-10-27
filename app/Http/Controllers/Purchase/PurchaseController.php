<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Conversion;
use App\Models\ConversionAmountPoint;
use App\Models\LineItem;
use App\Models\Loyaltyaccount;
use App\Models\Loyaltytransaction;
use App\Models\Product;
use App\Models\Purchase;
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

    public function index(): View
    {
        return view('purchase.index');
    }


    public function registerPurchase(Request $request){
        //return json_encode($request->all());
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

        $loyaltyaccount = Loyaltyaccount::where('holderid', $client->id)->first();

        $conversionAmountPoint = ConversionAmountPoint::where('is_applicable', true)->first();

        $transactiontype = Transactiontype::where('id', $request->get('transactiontypeid'))->first();
        if(!$transactiontype){
            return back()->withErrors(['error' => 'Aucun type de transaction avec l\'ID  \'' . $request->get('transactiontypeid') . '\'.']);
        }

        DB::beginTransaction();

        $purchase = null;
        try {
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

            $totalPoint = $loyaltyaccount->point_balance + $point;

            if ($totalPoint > $conversionAmountPoint->min_amount){}

            ///TODO After create conversion point Montant

            $amount_from_converted_point = ($point *  $conversion -> point_to_amount_amount)/$conversion -> point_to_amount_point;
            /*
             $lastConversion = Conversion::where('is_applicable', true)->orderBy('created_at', 'desc')->first();
        return ($point *  $lastConversion -> point_to_amount_amount)/$lastConversion -> point_to_amount_point;
             */
            $transactionid = Str::uuid()->toString();
            Loyaltytransaction::create(
                [   'id' => $transactionid,
                    'date' => Carbon::now(),
                    'loyaltyaccountid' => $loyaltyaccount->id,
                    'conversionid' => $conversion->id,
                    'sellerid' => Auth::user()->id,
                    'purchaseid' => $purchaeId,
                    'amount' => $realAmount,
                    'point' => $point,
                    'amount_from_converted_point' => $amount_from_converted_point,
                    'current_point' => $loyaltyaccount->point_balance,
                    'transactiontypeid' => $request->get('transactiontypeid'), //env('TRANSACTIONTYPEID_PURCHASE'),
                    'transactiondetail' => $purchaseDetails,
                    'clienttransactionid' => $client->id,
                    'state' => 'SUCCESS',
                    'returnresult'=>'SUCCESS'
                ]
            );

            $current_point_new = (int)(($loyaltyaccount->amount_balance + $realAmount) * $conversion->amount_to_point_point) / $conversion->amount_to_point_amount;
            $amount_from_converted_point = ($point *  $conversion -> point_to_amount_amount)/$conversion -> point_to_amount_point;
            $loyaltyaccount->update(
                [
                    'amount_balance' => $loyaltyaccount->amount_balance + $realAmount,
                    'point_balance' => $loyaltyaccount->point_balance + $point,
                    'amount_from_converted_point' =>  $loyaltyaccount->amount_from_converted_point + $amount_from_converted_point,
                    'current_point' => $loyaltyaccount->point_balance
                ]);

        }catch (\Exception $exception){
            DB::rollBack();
            return back()->withErrors(['error' => $exception->getMessage() . '   ' . $exception->getLine()]);
        }

        DB::commit();
        session()->flash('status', 'Achat enregistre avec succes!');
        return redirect("/home");//->withSuccess(['status' => 'Achat enregistre avec succes.', 'purchase' => $purchase]);
    }

}



