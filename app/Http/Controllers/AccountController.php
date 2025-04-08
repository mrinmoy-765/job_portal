<?php

namespace App\Http\Controllers;

use App\Models\User;
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





}
