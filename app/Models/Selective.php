<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selective extends Model
{
  use HasFactory;

  protected $table = 'selectives';
  protected $fillable = [
    'id',
    'export_ainiat_number',
    'user_id',
    'customer_id',
    'product_id',
    'status',
    'create_at',
    'updated_at',
  ];

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }

  public function customer()
  {
    return $this->belongsTo('App\Models\Customer');
  }

  public function product()
  {
    return $this->belongsTo('App\Models\Product');
  }

  public function export_ainiat()
  {
    return $this->belongsTo('App\Models\ExportAiniat', 'export_ainiat_number', 'number');
  }

}
