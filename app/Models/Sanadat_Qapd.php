<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanadat_Qapd extends Model
{
  use HasFactory;
  protected $table = 'sanadat_qapds';
  // add fillable
  protected $fillable = [
    'id',
    'worker_id',
    'customer_id',
    'provider_id',
    'box_id',
    'date_created',
    'number',
    'balance',
    'byan',
    'create_at',
    'updated_at',
  ];

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
  // box id linked to box table
  public function box()
  {
    return $this->belongsTo('App\Models\Box');
  }
}
