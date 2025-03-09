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
    public function update(Request $request, $id)
{
    $request->validate([
        'description' => 'required|string|max:500',
    ]);

    $type = Type::find($id);

    if (!$type) {
        return response()->json(['error' => 'type not found'], 404);
    }



    $type->description = $request->description;
    $type->save();

    return response()->json(['message' => 'type updated successfully'], 200);
}
}
