<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orphan extends Model
{
  use HasFactory;
  protected $table = 'orphans';
  protected $fillable = ['id', 'name', 'user_id', 'kafeel_id', 'wasi_id', 'identity', 'phone', 'status', 'notes', 'created_at', 'updated_at'];

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
  // wasi
  public function payment()
  {
    return $this->hasMany('App\Models\OrphanPayment');
  }
}
