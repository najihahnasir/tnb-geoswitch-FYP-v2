<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Switchgear extends Model
{
    protected $fillable = [
        'substation_id',
        'name',
        'lineup',
        'panel',
        'compartment',
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
