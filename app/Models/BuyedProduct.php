<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyedProduct extends Model
{

    use HasFactory;
    protected $table = 'buyed_products';

    protected $fillable = ['id', 'product_id', 'quantity', 'import_ainiat_id', 'created_at', 'updated_at'];

    public function import_ainiat()
    {
        return $this->belongsTo('App\Models\ImportAiniat', 'import_ainiat_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

}
