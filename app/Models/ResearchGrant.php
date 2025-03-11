<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ResearchGrant extends Model
{
    protected $table = 'research_grants'; // Ensure this matches the table name
    
    protected $fillable = ['project_title','grant_amount','grant_provider',
        'start_date','end_date','duration_month','leader_id'];
    
    // protected $dates = ['start_date', 'end_date'];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Define the relationship to academicians_grant
    public function academiciansGrant()
    {
        return $this->hasMany(AcademicianGrant::class, 'research_grant_id');
    }

    // Define the relationship with Academician (many-to-many for members)
    public function academicians()
    {
        return $this->belongsToMany(Academician::class, 'academicians_grant', 'research_grant_id', 'academician_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Define the relationship with Milestone (one-to-many)
    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'grant_id', 'id');
    }

    // Define the relationship with Academician (one-to-one for project leader)
    public function leader()
    {
        return $this->belongsTo(Academician::class, 'leader_id');
    }

    public function members()
    {
        return $this->belongsToMany(Academician::class, 'research_grant_members', 'academicians_grant', 'research_grant_id', 'academician_id');
    }
    
    public function getLeader() {
        $user = auth()->user();
        if (!empty($user->academician)) {
            if ($user->academician->id == $this->leader_id) {
                return true;
            }
        }
        return false;
    }
}
