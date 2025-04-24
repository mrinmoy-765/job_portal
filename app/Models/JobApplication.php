<?php

namespace App\Models;
use App\Models\jobs_post;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class JobApplication extends Model
{
      use HasFactory;

      public function jobs_post(){
        return $this->belongsTo(jobs_post::class, 'job_id'); 
    }


    public function user(){
      return $this->belongsTo(User::class);
    }
    
}
