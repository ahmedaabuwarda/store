<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $fillable = ['name', 'balance'];
    public function sanadat_sarf() {
        return $this->hasMany('App\Models\Sanadat_Sarf');
    }
    public function sanadat_qapd() {
        return $this->hasMany('App\Models\Sanadat_Qapd');
    }
    public function buy_bill() {
        return $this->hasMany('App\Models\BuyBill');
    }
    public function sell_bill() {
        return $this->hasMany('App\Models\SellBill');
    }
}
