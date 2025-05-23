<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedJob extends Model
{
    use HasFactory;

    public function jobs_post(){
        return $this->belongsTo(jobs_post::class, 'job_id'); 
    }
}
