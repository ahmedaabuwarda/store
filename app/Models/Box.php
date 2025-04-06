<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{

    use HasFactory;

    protected $table = 'box';
    protected $fillable = ['id', 'name', 'balance', 'currency_id' ,'remaining', 'counter', 'created_at', 'updated_at'];
    // each box has one currency
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
