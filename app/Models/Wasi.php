<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wasi extends Model
{
  use HasFactory;
  protected $table = 'wasis';
  protected $fillable = ['id', 'name', 'user_id', 'identity', 'phone', 'status', 'notes', 'created_at', 'updated_at'];

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }
  //orphan
  public function orphan()
  {
    return $this->hasMany('App\Models\Orphan');
  }
}
