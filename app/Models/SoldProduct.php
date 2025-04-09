<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldProduct extends Model
{
    use HasFactory;
    protected $table = 'sold_products';
    
    public function export_ainiat()
    {
        return $this->belongsTo('App\Models\ExportAiniat', 'export_ainiat_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
