<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        return response()->json($request->user());
    }
    public function Get_Users(Request $request){
        $users =User::where('role',User::ROLE_SLAVE )
        ->get() ;
        return response()->json($users);
    }
}
