<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Author ; 
class AuthorController extends Controller
{
    public function GetAuthor(Request $request , $id){
        $author = Author::findOrFail($id);
        $author->author_pfp = $author->author_pfp ?asset("images/authors/{$author->author_pfp}"):null ; 
        return response()->json($author);
    }
    public function update(Request $request , $id){
        $request->validate([
            'bio' => 'required|string|max:500',
        ]);
    
        $author = Author::find($id);
    
        if (!$author) {
            return response()->json(['error' => 'author not found'], 404);
        }
    
    
    
        $author->bio = $request->bio;
        $author->save();
    
        return response()->json(['message' => 'author updated successfully'], 200);
    }
}
