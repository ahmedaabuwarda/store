<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['barcode', 'name', 'quantity', 'original_quantity', 'original_price', 'taqseet_price', 'export_ainiat_id', 'buy_bill_id', 'status', 'type', 'created_at', 'updated_at'];

    public function buy_bill()
    {
        return $this->belongsTo('App\Models\BuyBill');
    }
    public function export_ainiat()
    {
        return $this->belongsTo('App\Models\ExportAiniat');
    }
    public function sold_product()
    {
        return $this->hasMany('App\Models\SoldProduct');
    }
    public function buyed_product()
    {
        return $this->hasMany('App\Models\BuyedProduct');
    }
    public function selective()
    {
        return $this->hasMany('App\Models\Selective');
    }
}
