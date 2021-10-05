<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
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
