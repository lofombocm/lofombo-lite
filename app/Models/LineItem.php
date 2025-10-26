<?php

namespace App\Models;

class LineItem
{
    public $name;
    public $quantity;
    public $price;
    public $total;
    public function __construct(string $name, int $quantity, float $price, float $total){
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->total = $total;
    }
    public static function createLineItem(string $name, int $quantity, float $price, float $total): ?LineItem{
        if ($quantity * $price !== $total){
            return null;
        }
        return new LineItem($name, $quantity, $price, $total);
    }

    public function __toString(){
        return 'Product Name: ' . $this->name . ' -   Quantity:  ' . $this->quantity . ' -  Price: ' . $this->price . ' - Total: ' . $this->total;
    }

    public function getName(): string{
        return $this->name;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }
    public function getQuantity(): float{
        return $this->quantity;
    }
    public function getPrice(): float{
        return $this->price;
    }

}
