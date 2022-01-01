<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $table = 'workers';

    public function sanadat_sarf() {
        return $this->hasMany('App\Models\Sanadat_Sarf');
    }
    public function sanadat_qapd() {
        return $this->hasMany('App\Models\Sanadat_Qapd');
    }
    public function salary() {
        return $this->hasMany('App\Models\Salary');
    }
    public function buy_bill() {
        return $this->hasMany('App\Models\BuyBill');
    }
    public function sell_bill() {
        return $this->hasMany('App\Models\SellBill');
    }
    
}