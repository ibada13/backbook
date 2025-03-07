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
}
