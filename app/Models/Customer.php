<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  use HasFactory;

  protected $table = 'customers';
  protected $fillable = ['id', 'name', 'identity', 'phone', 'family_number', 'mosque_id' ,'balance', 'status', 'notes', 'created_at', 'updated_at'];

  public function sanadat_sarf()
  {
    return $this->hasMany('App\Models\Sanadat_Sarf');
  }
  public function sanadat_qapd()
  {
    return $this->hasMany('App\Models\Sanadat_Qapd');
  }
  public function buy_bill()
  {
    return $this->hasMany('App\Models\BuyBill');
  }
  public function export_ainiat()
  {
    return $this->hasMany('App\Models\ExportAiniat');
  }
  // has one mosque
  public function mosque()
  {
    return $this->belongsTo('App\Models\Mosque', 'mosque_id');
  }
}
