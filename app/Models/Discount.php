<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $table = 'discounts';
    protected $fillable = [
        'balance',
        'date_created',
        'notes',
        'done_by',
        'box_id'
    ];
    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }
}
