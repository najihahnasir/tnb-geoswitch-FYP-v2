<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorStatusHistory extends Model
{
    protected $fillable = [
        'sensor_id',
        'user_id',
        'old_status',
        'new_status',
    ];
    
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
