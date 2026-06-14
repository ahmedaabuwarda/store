<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMS extends Model
{
  use HasFactory;
  protected $table = 'smses';

  protected $fillable = [
    'id',
    'user_id',
    'body',
    'create_at',
    'updated_at',
  ];
}
