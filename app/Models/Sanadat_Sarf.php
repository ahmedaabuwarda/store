<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanadat_Sarf extends Model
{
    use HasFactory;
    protected $table = 'sanadat_sarfs';

    public function worker()
    {
        return $this->belongsTo('App\Models\Worker');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }
}
