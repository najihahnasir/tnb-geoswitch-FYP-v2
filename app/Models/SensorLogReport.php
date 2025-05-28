<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SensorLogReport extends Model
{
    protected $fillable = [
        'substation_id',
        'switchgear_id',
        'sensor_id',
        'reported_by',
        'title',
        'description',
        'status',
        'reported_at',
        'resolved_at',
        'downtime_duration',
        'remark',
        'old_status',
        'new_status',
        'status_changed_by',   // ✅ must be here
        'status_changed_at',   // ✅ must be here
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'status_changed_at' => 'datetime',
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

    public function statusChanger()
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }

    protected static function booted()
    {
        static::creating(function ($report) {
            $year = now()->format('Y');
            $latestId = SensorLogReport::max('id') + 1;
            $formattedId = str_pad($latestId, 4, '0', STR_PAD_LEFT);
            $report->case_id = "CASE-{$year}-{$formattedId}";
        });
    }

}
