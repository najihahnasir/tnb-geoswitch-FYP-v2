<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
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
        'remark',
        'old_status',
        'new_status',
        'status_changed_by',
        'status_changed_at',
    ];

    protected $dates = [
        'reported_at',
        'resolved_at',
        'status_changed_at',
        'created_at',
        'updated_at',
    ];

    
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

    public function latestLogReport()
    {
        return $this->hasOne(SensorLogReport::class)->latestOfMany();
    }

    public function logReports()
    {
        return $this->hasMany(SensorLogReport::class);
    }
    /**
     * The user who reported this log.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
    /**
     * The user who changed the sensor status related to this report.
     */
    public function statusChanger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }

    // Accessor for the "status" attribute
    public function getStatusAttribute($value)
    {
        return ucfirst($value); // online → Online, offline → Offline
    }

    // Accessor for the "type" attribute
    public function getTypeAttribute($value)
    {
        return match ($value) {
            'thermal' => 'Thermal',
            'partial_discharge' => 'Partial Discharge',
            default => ucfirst($value),
        };
    }

}
