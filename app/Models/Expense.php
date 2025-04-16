<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $table = 'expenses';
    protected $fillable = [
        'balance',
        'date_created',
        'notes',
        'user_id',
        'box_id'
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
