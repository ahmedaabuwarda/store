<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quantity extends Model
{
    use HasFactory;
    protected $table = 'quantities';
    protected $fillable = ['id', 'product_id', 'quantity', 'buy_price'.'created_at', 'updated_at'];
}
