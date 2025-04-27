<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\JobType;
use App\Models\jobs_post;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;



class AccountController extends Controller
{
    //this method will show user registration page
    public function registration()
    {
        return view('front.account.registration');
    }

    //this method wii save user
    public function processRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|same:confirm_password',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        session()->flash('success', 'Registration Successful');

        return response()->json([
            'status' => true,
            'redirect_url' => route('account.login')
        ]);
    }


    //this method will show user login page
    // public function login(){
    //         return view('front.account.login');
    // }



    //this prevents back login/ back page
    public function login()
    {
        return response()
            ->view('front.account.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }



    //this method will authenticate a user/ login
    public function authenticate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return redirect()->route('account.profile');
            } else {
                return redirect()->route('account.login')->with('error', 'Incorrect email/password');
            }
        } else {
            return redirect()->route('account.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }
    }


    //this method will show user profile page
    // public  function profile()
    // {
    //     return view('front.account.profile');
    // }


    //this prevents back login / back page
    public function profile()
    {
        $id = Auth::user()->id;
        $user = User::where('id', $id)->first();

        return response()
            ->view('front.account.profile', ['user' => $user])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }




    //logout method
    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }


    //update profile method
    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        if ($validator->passes()) {
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->designation = $request->designation;
            $user->save();

            session()->flash('success', 'Profile Updated Successfully');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    //update profile pic
    public function updateProfilePic(Request $request){
           //dd($request->all());

           $id = Auth::user()->id;

           $validator = Validator::make($request->all(),[
                  'image' => 'required|image'
           ]);

           if($validator -> passes()){
               
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName= $id.'-'.time().'.'.$ext;
            $image->move(public_path('/Profile_pic/'),$imageName);


            //create a small thumbnail
            $sourcePath = public_path('/Profile_pic/'.$imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sourcePath);

            //crop the best fitting 5:3 (600x360) ratio and resize to 600x360 pixel
            $image->cover(150,150);
            $image->toPng()->save(public_path('/profile_pic/thumb/'.$imageName));

            //delete old profile picture
            File::delete(public_path('/profile_pic/thumb/'.Auth::user()->image));
            File::delete(public_path('/profile_pic/'.Auth::user()->image));

            User::where('id',$id)->update(['image' => $imageName]);

            session()->flash('success','Profile Picture Updated Successfully.');

            
           }else{
              return response()->json([
                 'status' => false,
                 'errors' => $validator->errors()
              ]);
           }
    }


    //this method shows create job page
    public function createJob(){

        $categories = Category::orderBy('name','ASC')->where('status', 1)->get();
        $jobTypes = JobType::orderBy('name')->where('status', 1)->get();

        return view('front.account.job.create',[
            'categories' => $categories,
            'jobTypes'  => $jobTypes
        ]);   
    }



     //this method creates a job
    public function saveJob(Request $request){

      

       $rules =[
        'title' => 'required|min:5|max:30',
        'category' => 'required',
        'jobType'  => 'required',
        'vacancy' => 'required|int',
        'location' => 'required|max:20',
        'description' => 'required',
        'company_name' => 'required|max:20'
       ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->passes()){
        
            $job = new jobs_post();
            $job->title = $request -> title;
            $job->category_id = $request -> category;
            $job->job_type_id = $request -> jobType;
            $job->user_id = Auth::user()->id;
            $job->vacancy = $request -> vacancy;
            $job->salary = $request -> salary;
            $job->location = $request -> location;
            $job->description = $request -> description;
            $job->benefits = $request -> benefits;
            $job->responsibility = $request -> responsibility;
            $job->qualifications = $request -> qualifications;
            $job->keywords = $request -> keywords;
            $job->experience = $request -> experience;
            $job->company_name = $request -> company_name;
            $job->company_location = $request -> company_location;
            $job->company_website = $request -> website;
            $job -> save();

            session() -> Flash('success','Job created successfully');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        }else{
            return response()->json([
                 'status' => false,
                 'errors' => $validator->errors()
            ]);
        }
    }


    //this method shows jobs created by a user
    public function myJobs(){

        $jobs = jobs_post::where('user_id', Auth::user()->id)->with('jobType')->orderBy('created_at','DESC')->paginate(10);
          return view('front.account.job.myJobs',[
            'jobs' => $jobs
          ]);
    }


    //this method shows job edit page
    public function editJob(Request $request, $id){


        $categories = Category::orderBy('name','ASC')->where('status', 1)->get();
        $jobTypes = JobType::orderBy('name')->where('status', 1)->get();

        $job = jobs_post::where([
            'user_id' => Auth::user()->id,
            'id' => $id
        ])->first();

        if($job == null){
            abort(404);
        }
         
        return view('front.account.job.edit',[
            'categories'  => $categories,
            'jobTypes' => $jobTypes,
            'job' => $job
        ]);
    }


    //this method updates job
    public function updateJob(Request $request, $id){

      

        $rules =[
         'title' => 'required|min:5|max:25',
         'category' => 'required',
         'jobType'  => 'required',
         'vacancy' => 'required|int',
         'location' => 'required|max:25',
         'description' => 'required',
         'company_name' => 'required|max:25'
        ];
 
         $validator = Validator::make($request->all(),$rules);
 
         if($validator->passes()){
         
             $job = jobs_post::find($id);
             $job->title = $request -> title;
             $job->category_id = $request -> category;
             $job->job_type_id = $request -> jobType;
             $job->user_id = Auth::user()->id;
             $job->vacancy = $request -> vacancy;
             $job->salary = $request -> salary;
             $job->location = $request -> location;
             $job->description = $request -> description;
             $job->benefits = $request -> benefits;
             $job->responsibility = $request -> responsibility;
             $job->qualifications = $request -> qualifications;
             $job->keywords = $request -> keywords;
             $job->experience = $request -> experience;
             $job->company_name = $request -> company_name;
             $job->company_location = $request -> company_location;
             $job->company_website = $request -> website;
             $job -> save();
 
             session() -> Flash('success','Job updated successfully');
 
             return response()->json([
                 'status' => true,
                 'errors' => []
             ]);
         }else{
             return response()->json([
                  'status' => false,
                  'errors' => $validator->errors()
             ]);
         }
     }


     //delete job method
     public function deleteJob(Request $request){
            
       $job = jobs_post::where([
            'user_id' => Auth::user()->id,
            'id' => $request->jobId
        ])->first();

        if($job == null){
            session() -> flash('error', 'Job not Found!!!');
            return response()->json([
                'status' => true
            ]);
        }

        jobs_post::where('id', $request->jobId)->delete();
        session() -> flash('success', 'Job Post deleted successfully!!!');
        return response()->json([
            'status' => true
        ]);
    }

    //this method will show my applied job page
    public function myJobApplications(){
        $jobApplications = JobApplication::where('user_id',Auth::user()->id)
        ->with('jobs_post','jobs_post.jobType','jobs_post.applications')
        ->paginate(10);
      //  dd($jobApplications);  
        return view('front.account.job.my-job-applications',[
            'jobApplications' =>  $jobApplications
          ]);
    }



    //this method will remove job application
    public function removeJobApplication(Request $request){
               $jobApplication = JobApplication::where([
                     'id'  => $request->id,
                      'user_id' => Auth::user()->id]
                )->first();


            if($jobApplication == null){
                session() ->flash('error','Job application not found');
                return response()->json([
                    'status' => false,
                ]);
            }

            JobApplication::find($request->id)->delete();

            session() ->flash('success','Application removed');
            return response()->json([
                'status' => true,
            ]);
    }

    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(),[
              'old_password' =>'required',
              'new_password' => 'required|min:5',
              'confirm_password' => 'required|same:new_password',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        if(Hash::check($request->old_password, Auth::user()->password) == false){
            
            session()->flash('error','Your old password is incorrect');
            return response()->json([
                'status' => true,
            ]);
        }

        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->new_password);
        $user -> save();


        session()->flash('success','Password Changed Successfully');
            return response()->json([
                'status' => true,
            ]);
    }
}