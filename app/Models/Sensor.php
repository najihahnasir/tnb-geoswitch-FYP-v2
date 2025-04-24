<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $fillable = ['switchgear_id','type','tag',];
    
    public function switchgear()
    {
        return $this->belongsTo(Switchgear::class);
    }

    public function measurements()
    {
        return $this->hasMany(SensorMeasurement::class);
    }
    
    public function latestMeasurement()
    {
        return $this->hasOne(SensorMeasurement::class)->latestOfMany();
    }


}
