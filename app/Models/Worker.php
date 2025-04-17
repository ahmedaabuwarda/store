<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $table = 'workers';
    protected $fillable = [
      'id',
      'name',
      'balance',
      'status',
      'notes',
      'create_at',
      'updated_at',
    ];

    public function sanadat_sarf()
    {
        return $this->hasMany('App\Models\Sanadat_Sarf');
    }
    public function sanadat_qapd()
    {
        return $this->hasMany('App\Models\Sanadat_Qapd');
    }
    public function salary()
    {
        return $this->hasMany('App\Models\Salary');
    }
    public function import_ainiat()
    {
        return $this->hasMany('App\Models\ImportAiniat');
    }
    public function export_ainiat()
    {
        return $this->hasMany('App\Models\ExportAiniat');
    }

}
