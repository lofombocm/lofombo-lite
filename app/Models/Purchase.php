<?php

namespace App\Models;

use App\Models\LineItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';
    protected $primaryKey = 'id';

    protected $casts = ['id' => 'string'];
    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = ['id', 'clientid', 'amount', 'receiptnumber', 'products'];
    //protected $casts = ['products' => 'json'];


    public function __construct($id = null, $clientid = null, $amount = null, $receiptnumber = null, $products = null, array $attributes = []){
        parent::__construct($attributes);
        $this->id = $id;
        $this->clientid = $clientid;
        $this->amount = $amount;
        $this->receiptnumber = $receiptnumber;
        $this->products = $products;
    }

    public function isValidePurchase(): bool{
            $products = json_decode($this->products, true);
            foreach ($products as  $product){
                $lineItem = LineItem::createLineItem($product['name'], $product['quantity'], $product['price'], $product['total']);
                if ($lineItem == null){
                    return false;
                }
            }
            return true;
    }

    public function sauvegarder(): ?Purchase{
        if($this->isValidePurchase()){
            $this->save();
            return $this;
        }
        $purchaseSanitized = $this->sanitizePurchase();
        $purchaseSanitized->save();
        return $purchaseSanitized;
    }

    public function sanitizePurchase(): ?Purchase{
        $sanitizedProducts = [];
        $products = json_decode($this->products, true);
        $amount = 0;
        foreach ($products as  $product){
            $lineItem = LineItem::createLineItem($product['name'], $product['quantity'], $product['price'], $product['total']);
            if ($lineItem !== null){
                array_push($sanitizedProducts, $lineItem);
                $amount = $amount + $product->getTotal();
            }
        }
        $this -> amount = $amount;
        $this->products = json_encode($sanitizedProducts);
        return $this;
    }
}
