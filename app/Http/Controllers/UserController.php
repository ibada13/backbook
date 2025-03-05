<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        return response()->json($request->user());
    }
    public function Get_Users(Request $request){
        // Validate request
        $validator = Validator::make($request->all(), [
            "page" => "integer|min:1",  // Changed "id" to "page"
            "limit" => "integer|min:10|max:20",
        ]);
    
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 400);
        }
    
        // Get pagination parameters
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);    
    
        // Fetch paginated users
        $users = User::where('role', User::ROLE_SLAVE)
            ->paginate($limit, ['id', 'email', 'user_pfp', 'name'], 'page', $page)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'user_pfp' => $user->user_pfp ? asset("images/users/{$user->user_pfp}") : null,
                    'name' => $user->name,
                ];
            });
    
        return response()->json($users, 200);
    }
    public function Get_Mods(Request $request){
        $validator = Validator::make($request->all(), [
            "page" => "integer|min:1",  
            "limit" => "integer|min:10|max:20",
        ]);
    
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 400);
        }
    
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);    
    
        $users = User::where('role', User::ROLE_CITIZEN)
            ->paginate($limit, ['id', 'email', 'user_pfp', 'name'], 'page', $page)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'user_pfp' => $user->user_pfp ? asset("images/users/{$user->user_pfp}") : null,
                    'name' => $user->name,
                ];
            });
    
        return response()->json($users, 200);
    }
    public function Ban_User(Request $request , $id){
        $user = User::findOrFail($id);
        $user->role = User::ROLE_Exciled ;
        $user->save();
        return response()->json(["message"=>"user was banned succefully"] );
    }
    public function ModUser(Request $request , $id){
        $user = User::findOrFail($id);
        if($user->role === User::ROLE_CITIZEN){
            $user->role = User::ROLE_SLAVE;
        }else{
            $user->role = User::ROLE_CITIZEN;

        }
        $user->save();
        return response()->json(["message"=>"user was puted to mod succefuly" ] ,200);
    }

    public function AdminUser(Request $request , $id){
        $user = User::findOrFail($id);
        $user->role = User::ROLE_KING;
        $user->save();
        return response()->json(["message"=>"user was puted to mod succefuly" ] ,200);
    }
    
}
