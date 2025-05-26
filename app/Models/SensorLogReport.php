<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SensorLogReport extends Model
{
    protected $fillable = [
        'substation_id', 'switchgear_id', 'sensor_id', 'reported_by',
        'title', 'description', 'status', 'reported_at', 'resolved_at', 'remark'
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function sensor() {
        return $this->belongsTo(Sensor::class);
    }

    public function substation()  {
        return $this->belongsTo(Substation::class);
    }

    public function switchgear() { 
        return $this->belongsTo(Switchgear::class); 
    }

    public function reporter() {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function getDurationAttribute()
    {
        if (!$this->resolved_at) return '-';

        $diff = $this->reported_at->diff($this->resolved_at);
        return sprintf('%dd %dh %dm', $diff->d, $diff->h, $diff->i);
    }

}
