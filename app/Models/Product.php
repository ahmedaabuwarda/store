<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['name', 'quantity', 'original_quantity', 'original_price', 'sell_bill_id', 'buy_bill_id', 'status', 'type', 'created_at', 'updated_at'];

    public function sell_bill() {
        return $this->belongsTo('App\Models\SellBill');
    }
    public function sold_product() {
        return $this->hasMany('App\Models\SoldProduct');
    }
    public function buyed_product() {
        return $this->hasMany('App\Models\BuyedProduct');
    }
}
