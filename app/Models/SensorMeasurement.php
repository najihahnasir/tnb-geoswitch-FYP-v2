<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorMeasurement extends Model
{
    protected $fillable = [
        'sensor_id',
        'value',
        'unit',
        'threshold',
        'status',
        'severity',
    ];
    
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
