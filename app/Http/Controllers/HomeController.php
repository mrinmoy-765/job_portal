<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\jobs_post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){

        $categories = Category::where('status',1)->orderBy('name','ASC')->take(8)->get();

        $newCategories = Category::where('status',1)->orderBy('name','ASC')->get();

        $featuredJobs = jobs_post::where('status',1)->orderBy('created_at','DESC')->with('jobType')->where('isFeatured',1)->take(6)->get();

        $latestJobs = jobs_post::where('status',1)->orderBy('created_at','ASC')->with('jobType')->where('isFeatured',1)->take(6)->get();
        return view('front.home',[
                   'categories' => $categories,
                   'featuredJobs' => $featuredJobs,
                   'latestJobs' => $latestJobs,
                   'newCategories' => $newCategories 
        ]);
    }

    public function contact(){
        return view('front.contact');
    }
}
