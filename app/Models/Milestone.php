<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $casts = [
        'completion_date' => 'datetime', // This will cast the completion_date to a Carbon instance
    ];
    
    // protected $table = 'research_grants';
    protected $primaryKey = 'id';

    protected $fillable = ['milestone_name','completion_date','deliverable',
    'status','remark','date_updated','grant_id'];

    // Define the relationship with ResearchGrant (inverse of one-to-many)
    public function researchGrant()
    {
        return $this->belongsTo(ResearchGrant::class, 'grant_id', 'id');
    }
}
