<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class adminController extends Controller
{
    public function index(){
        return view('admin.dashboard');
    }


    //get all users
    public function getUsers(){
        $users = User::orderBy('created_at', 'DESC')->paginate(10);
         return view('admin.users.list',[
                    'users' => $users
         ]);
    }
}
