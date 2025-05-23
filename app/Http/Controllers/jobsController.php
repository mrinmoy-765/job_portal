<?php

namespace App\Http\Controllers;

use App\Mail\jobNotificationEmail;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\JobType;
use App\Models\jobs_post;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class jobsController extends Controller
{
    //this method will show jobs page
    public function index(Request $request)
    {


        $categories = Category::where('status', 1)->get();
        $jobTypes = jobType::where('status', 1)->get();

        $jobs = jobs_post::where('status', 1);

        //search using keyword
        if (!empty($request->keyword)) {
            $jobs = $jobs->where(function ($query) use ($request) {
                $query->orWhere('title', 'like', '%' . $request->keyword . '%');
                $query->orWhere('keywords', 'like', '%' . $request->keyword . '%');
            });
        }

        // Search using location
        if (!empty($request->location)) {
            $jobs = $jobs->where('location', 'LIKE', '%' . $request->location . '%');
        }


        //search using category
        if (!empty($request->category)) {
            $jobs = $jobs->where('category_id', $request->category);
        }

        //search using job type
        $jobTypeArray = [];
        if (!empty($request->job_type)) {
            $jobTypeArray = $request->job_type;
            $jobs = $jobs->whereIn('job_type_id', $jobTypeArray);
        }


        //search using experience
        if (!empty($request->experience)) {
            $jobs = $jobs->where('experience', $request->experience);
        }



        $jobs = $jobs->with('jobType');

        if ($request->sort == 0) {
            $jobs = $jobs->orderBy('created_at', 'ASC');
        } else {
            $jobs = $jobs->orderBy('created_at', 'DESC');
        }


        $jobs = $jobs->paginate(9);

        return view('front.jobs', [
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'jobs' => $jobs,
            'jobTypeArray' => $jobTypeArray
        ]);
    }

    //this method will show job detail page
    public function detail($id)
    {

        $job = jobs_post::where([
            'id' => $id,
            'status' => 1
        ])->with(['jobType', 'category'])->first();


        if ($job == null) {
            abort(404);
        }

        $count = 0;

       if(Auth::user()){
        $count =  SavedJob::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();
       }


       //fetch applicants
       $applicants = JobApplication::where('job_id',$id)->with('user')->get();

        return view('front.jobDetail', ['job' => $job, 
                                                    'count' => $count, 
                                                    'applicants' => $applicants]);
    }

    public function applyJob(Request $request)
    {
        $id = $request->id;

        $job = jobs_post::where('id', $id)->first();

        session()->flash('error', 'Job not found');

        if ($job == null) {

            return response()->json([
                'status' => false,
                'message' => 'Job not found'
            ]);
        }


        //you can not apply on your own job
        $employer_id = $job->user_id;

        if ($employer_id == Auth::user()->id) {
            session()->flash('error', 'You can not apply on your own job');
            return response()->json([
                'status' => false,
                'message' => 'You can not apply on your own job',
            ]);
        }


        //you can not apply a job twice
        $jobApplicationCount = JobApplication::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();

        if ($jobApplicationCount > 0) {
            $message = 'You can not apply a job twice';
            session()->flash('success', $message);
            return response()->json([
                'status' => false,
                'message' => $message,
            ]);
        }


        $application = new JobApplication();
        $application->job_id = $id;
        $application->user_id = Auth::user()->id;
        $application->employer_id = $employer_id;
        $application->applied_date = now();
        $application->save();


        //send notification email to employer
        $employer = User::where('id', $employer_id)->first();

        $mailData = [
            'employer' => $employer,
            'user' => Auth::user(),
            'job' => $job,
        ];

        Mail::to($employer->emailf)->send(new jobNotificationEmail($mailData));


        $message = 'Application Successfull!!!';

        session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }



    public function saveJob(Request $request){

        $id = $request->id;

        $job = jobs_post::find($id);

        if($job == null){
           session()->flash('error','Job not found');
           return response()->json([
            'status' =>false
           ]);
        }

        //check if already saved the job

        $count =  SavedJob::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();

        if($count>0){
            session()->flash('error','You have already saved this job');

            return response()->json([
               'status' => false,
            ]);
        }

        $savedJob = new SavedJob;
        $savedJob->job_id = $id;
        $savedJob->user_id = Auth::user()->id;
        $savedJob -> save();


        session()->flash('success','Job Saved');

        return response()->json([
           'status' => true,
        ]);

    }

    public function savedJobs(){

        $savedJobs = SavedJob::where([
            'user_id' => Auth::user()->id
        ])
        ->with(['jobs_post','jobs_post.jobType','jobs_post.applications'])
        ->orderBy('created_at','DESC')
        ->paginate(10);

        return view('front.account.job.saved-jobs',[
                  'savedJobs' => $savedJobs
        ]);
    }



    //this method will remove saved jobs
    public function removeSavedJob(Request $request){
        $savedJob = SavedJob::where([
              'id'  => $request->id,
               'user_id' => Auth::user()->id]
         )->first();


     if($savedJob == null){
         session() ->flash('error','Job  not found');
         return response()->json([
             'status' => false,
         ]);
     }

     SavedJob::find($request->id)->delete();

     session() ->flash('success','Job removed');
     return response()->json([
         'status' => true,
     ]);
}
}
