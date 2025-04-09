<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Substation extends Model
{
    use HasFactory;

    protected $fillable = ['substation_name', 'code', 'latitude', 'longitude', 'description', 'subtation_location',
    'status', 'type'];

    public function switchgears()
    {
        return $this->hasMany(Switchgear::class);
    }

}
