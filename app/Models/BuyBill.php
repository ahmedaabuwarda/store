<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyBill extends Model
{
    use HasFactory;

    protected $table = 'buy_bills';
    protected $fillable = ['number', 'date_created', 'provider_id', 'customer_id',  'worker_id', 'original_balance', 'paid_balance', 'remaining_balance', 'discount', 'byan', 'created_at', 'updated_at'];

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function buyed_product()
    {
        return $this->hasMany('App\Models\BuyedProduct');
    }
}
