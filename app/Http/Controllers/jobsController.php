<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\JobType;
use App\Models\jobs_post;

class jobsController extends Controller
{
    //this method will show jobs page
    public function index(){


        $categories = Category::where('status', 1)->get();
        $jobTypes = jobType::where('status', 1)->get();

        $jobs = jobs_post::where('status',1)->with('jobType')->orderBy('created_at','DESC')->paginate(9);
         return view('front.jobs',[
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'jobs' => $jobs,
         ]);
    }
}
