<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportAiniat extends Model
{
    use HasFactory;
    
    protected $table = 'export_ainiats';
    protected $fillable = ['id', 'total_profit', 'discount', 'created_at', 'updated_at'];
    
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
