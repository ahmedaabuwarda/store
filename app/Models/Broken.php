<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Broken extends Model
{
    use HasFactory;

    protected $table = 'brokens';
    protected $fillable = ['id', 'number', 'date_created','provider_id', 'customer_id', 'worker_id', 'total_balance', 'paid_balance', 'byan', 'created_at', 'updated_at'];

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
    public function worker()
    {
        return $this->belongsTo('App\Models\Worker');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function product()
    {
        return $this->hasMany('App\Models\Product');
    }
    public function sold_product()
    {
        return $this->hasMany('App\Models\SoldProduct');
    }

}