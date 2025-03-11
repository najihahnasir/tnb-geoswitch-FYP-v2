<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicianGrant extends Model
{
    protected $table = 'academicians_grant';

     // Define the relationship to the academicians table
     public function academician()
     {
         return $this->belongsTo(Academician::class);
     }
 
     // Define the relationship to the research_grants table
     public function researchGrant()
     {
         return $this->belongsTo(ResearchGrant::class);
     }
}
