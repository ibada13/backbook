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
    
}
