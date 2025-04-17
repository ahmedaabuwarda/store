<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
    protected $table = 'providers';

    public function sanadat_sarf()
    {
        return $this->hasMany('App\Models\Sanadat_Sarf');
    }
    public function sanadat_qapd()
    {
        return $this->hasMany('App\Models\Sanadat_Qapd');
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
