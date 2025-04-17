<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportAiniat extends Model
{

    use HasFactory;

    protected $table = 'import_ainiats';
    protected $fillable = ['id', 'number', 'date_created', 'provider_id', 'byan', 'created_at', 'updated_at'];

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
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
