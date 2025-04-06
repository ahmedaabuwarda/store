<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movements extends Model
{
    use HasFactory;
    protected $table = 'movements';
    protected $fillable = [
      'id',
        'balance',
        'date_created',
        'type',
        'from',
        'user_id',
        'box_id',
        'created_at',
        'updated_at'
    ];
    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
