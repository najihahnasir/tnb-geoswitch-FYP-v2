<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Academician extends Model
{
    protected $fillable = ['name','staff_number','email','college',
        'department','position','user_id'];
        
    // Define the relationship with ResearchGrant (many-to-many)
    public function researchGrants()
    {
        return $this->belongsToMany(ResearchGrant::class, 'academicians_grant', 'academician_id', 'research_grant_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function leaderGrants()
    {
        return $this->hasMany(ResearchGrant::class, 'leader_id'); // Grants where the academician is the leader
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grants()
    {
        return $this->belongsToMany(ResearchGrant::class, 'academicians_grant')
                    ->withPivot('role');
    }


}
