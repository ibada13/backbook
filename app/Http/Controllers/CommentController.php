<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment ;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function postcomment(Request $request)
    {
        $rules = [
            "book_id" => "required|integer|min:1|exists:books,id",
            "comment" => "required|string"
        ];
    
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 442);
        }
    
        $comment = Comment::create([
            'book_id' => $request->input('book_id'),
            'comment' => $request->input('comment'),
            'user_id' => $user->id,
        ]);
    
        return response()->json([
            "message" => "Comment was created successfully.",
            "comment" => $comment
        ], 201);
    }
    public function getcommentsforbook(Request $request)
{
    $validator = Validator::make($request->all(), [
        "book_id" => "required|integer|exists:books,id"
    ]);

    if ($validator->fails()) {
        return response()->json(["error" => $validator->errors()], 422);
    }

    $book_id = $request->input("book_id");
    $user = auth()->user();

    $comments = Comment::where("book_id", $book_id)
        ->orderBy("created_at", "desc")
        ->paginate(6);

    $comments->getCollection()->transform(function ($comment) use ($user) {
        $comment->is_owner = $user && $comment->user_id == $user->id;
        return $comment;
    });

    $commentwithoutlinks = $comments->toArray();
    unset($commentwithoutlinks["links"]);

    return response()->json($commentwithoutlinks);
}

    

public function deletecomment(Request $request)
{
    $rules = [
        "id" => "required|integer|min:1",
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json(["error" => $validator->errors()], 400);
    }

    $id = $request->input('id');
    $user = auth()->user();
    
    $comment = Comment::find($id);
    
    if (!$comment) {
        return response()->json(["error" => "Comment not found"], 404);
    }

    if ($comment->user_id !== $user->id) {
        return response()->json(["error" => "Unauthorized"], 403);
    }

    try {
        $comment->delete();
        return response()->json(["message" => "Comment deleted successfully"], 200);
    } catch (\Exception $e) {
        return response()->json([
            "error" => "An error occurred while deleting this comment",
            "details" => $e->getMessage()
        ], 500);
    }
}

}
