<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mosque extends Model
{
  use HasFactory;

  protected $table = 'mosques';
  protected $fillable = ['id', 'name', 'notes', 'created_at', 'updated_at'];

  // has many customers
  public function customer()
  {
    return $this->hasMany('App\Models\Customer');
  }
}
