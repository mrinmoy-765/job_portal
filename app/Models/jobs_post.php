<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jobs_post extends Model
{
    protected $table = 'jobs_post'; 

    public function jobType(){
        return $this->belongsTo(JobType::class);
    }

    public function Category(){
        return $this->belongsTo(Category::class);
    }
}
