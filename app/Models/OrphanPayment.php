<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrphanPayment extends Model
{
  use HasFactory;
  protected $table = 'orphan_payments';
  protected $fillable = ['id', 'amount', 'user_id', 'kafeel_id', 'wasi_id', 'orphan_id', 'box_id', 'notes', 'date_created', 'created_at', 'updated_at'];

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }
  // kafeel
  public function kafeel()
  {
    return $this->belongsTo('App\Models\Kafeel');
  }
  // wasi
  public function wasi()
  {
    return $this->belongsTo('App\Models\Wasi');
  }
  //orphan
  public function orphan()
  {
    return $this->belongsTo('App\Models\Orphan');
  }
  //box
  public function box()
  {
    return $this->belongsTo('App\Models\Box');
  }
}
