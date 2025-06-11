<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Switchgear extends Model
{
    protected $fillable = [
        'substation_id',
        'panel_name',
        'panel_code',
        'lineup',
        'compartment',
        'compartment_code',
        'switchgear_code',
    ];

    
    public function substation()
    {
        return $this->belongsTo(Substation::class);
    }

    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }
}
