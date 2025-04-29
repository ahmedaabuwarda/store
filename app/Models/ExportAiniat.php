<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportAiniat extends Model
{
    use HasFactory;

    protected $table = 'export_ainiats';
    protected $fillable = ['id', 'user_id', 'number', 'date_created', 'notes', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function product()
    {
        return $this->hasMany('App\Models\Product');
    }
    public function sold_product()
    {
        return $this->hasMany('App\Models\SoldProduct');
    }
    // has many relationship with selective
    public function selective()
    {
        return $this->hasMany('App\Models\Selective', 'export_ainiat_number', 'number');
    }
}
