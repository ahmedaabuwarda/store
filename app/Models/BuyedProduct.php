<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyedProduct extends Model
{
    use HasFactory;
    protected $table = 'buyed_products';
    
    public function buy_bill() {
        return $this->belongsTo('App\Models\BuyBill', 'buy_bill_id', 'id');
    }
    public function product() {
        return $this->belongsTo('App\Models\Product');
    }
}
