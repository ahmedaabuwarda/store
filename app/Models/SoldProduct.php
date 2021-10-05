<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldProduct extends Model
{
    use HasFactory;
    protected $table = 'sold_products';
    public function sell_bill() {
        return $this->belongsTo('App\Models\SellBill', 'sell_bill_id', 'id');
    }
    public function product() {
        return $this->belongsTo('App\Models\Product');
    }
}
