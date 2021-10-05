<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyBill extends Model
{
    use HasFactory;
    protected $table = 'buy_bills';
    public function provider(){
        return $this->belongsTo('App\Models\Provider');
    }
    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }
    public function worker(){
        return $this->belongsTo('App\Models\Worker');
    }
    public function buyed_product() {
        return $this->hasMany('App\Models\BuyedProduct');
    }
}
