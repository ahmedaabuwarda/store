<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;
  protected $table = 'products';
  protected $fillable = ['id', 'name', 'quantity', 'original_quantity', 'status', 'type', 'created_at', 'updated_at'];

  public function import_ainiat()
  {
    return $this->hasMany('App\Models\ImportAiniat');
  }
  public function export_ainiat()
  {
    return $this->hasMany('App\Models\ExportAiniat');
  }
  public function sold_product()
  {
    return $this->hasMany('App\Models\SoldProduct');
  }
  public function buyed_product()
  {
    return $this->hasMany('App\Models\BuyedProduct');
  }
  public function selective()
  {
    return $this->hasMany('App\Models\Selective');
  }
}
