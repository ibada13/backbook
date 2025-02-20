<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment ;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function postcomment(Request $request){
        $rules = [
            "book_id"=>"required|integer|min:1|exists:books,id",
            "comment"=>"required"
        ];

        $validator = Validator::make($request->all(),$rules  );
        if($validator->fails()){
            return response()->json(
                [
                    'errors'=>$validator->errors(),
                ]
            ,442);
        }
       $comment =  Comment::create([
            $request->input('book_id'),
            $request->Input('comment'),
        ]) ;
        return response()->json(
            [
                "message"=>"comment was created sucessfully . ",
                "comment"=>$comment
            ],201
        );
    }
    public function getcommentsforbook(Request $request){
        // dd();
        $validator = Validator::make($request->all(),[
            "book_id"=>"required|integer|exists:books,id"
        ]);
        if($validator->fails()){
            return response()->json(
                ["erorr"=>$validator->errors(),],422
            );
        }
    $book_id = $request->input("book_id");
    
    $comments = Comment::where("book_id" , $book_id)
    ->paginate(1);
    $commentwithoutlinks  = $comments->toArray();
    unset($commentwithoutlinks["links"]);
    return response()->json($commentwithoutlinks  );
    }
    public function deletecomment(Request $request){
        $rules = [
            "id"=>"required|integer|min:1",
            
        ];
        $messages = [
            "id.required"=>"the Id params is required",
            "id.integer"=>"the Id must be an valid integer",
            "id.min"=>"the id must be at least 1",
            
        ] ;
        $validator = Validator::make($request->all() ,$rules ,$messages);
        if($validator->fails()){
            return response()->json([
                "error"=> $validator->errors()
            ],400);
        }
        $id = $request->input('id');
        $comment = Comment::find($id);
        if(!$comment){
            return response()->json([
                "error"=>"Comment not found"
            ],404);
        }
        try{
            $comment->delete();
            return response()->json(["message"=>"comment deleted sucessfully"],200);
        }catch(\Exception $e){
            return response()->json([
                "error"=>"an error occured while deleting this comment",
                "details"=>$e->getMessage()
        ],500);

        }
    }
}
