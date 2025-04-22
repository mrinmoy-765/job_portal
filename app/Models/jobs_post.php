<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\JobsPostFactory;

class jobs_post extends Model
{
    use HasFactory;
    protected $table = 'jobs_post'; 

    protected static function newFactory()
    {
        return JobsPostFactory::new();
    }

    public function jobType(){
        return $this->belongsTo(JobType::class);
    }

    public function Category(){
        return $this->belongsTo(Category::class);
    }

    public function applications(){
        return $this->hasMany(JobApplication::class, 'job_id');
    }
}
