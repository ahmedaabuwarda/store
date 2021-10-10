<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanadat_Qapd extends Model
{
    use HasFactory;
    protected $table = 'sanadat_qapds';
    public function user() {
        return $this->belongsTo('App\Models\User');
    }
    public function customer() {
        return $this->belongsTo('App\Models\Customer');
    }
    public function provider() {
        return $this->belongsTo('App\Models\Provider');
    }
}
