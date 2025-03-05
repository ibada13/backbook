<?php

namespace App\Http\Controllers;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function GetType(Request $request , $id){
        $type = Type::findOrFail($id);
        // dd($type);
        return response()->json($type);
    }
}
