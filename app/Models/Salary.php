<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
  use HasFactory;

  protected $table = 'salaries';
  protected $fillable = [
    'id',
    'worker_id',
    'box_id',
    'user_id',
    'remaining_balance',
    'balance',
    'net_balance',
    'date_created',
    'notes',
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
  public function box()
  {
    return $this->belongsTo('App\Models\Box');
  }
}
