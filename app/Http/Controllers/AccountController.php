<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        return response()
            ->view('front.account.profile')
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
}
