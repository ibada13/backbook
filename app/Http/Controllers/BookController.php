<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use App\Models\Comment;
use App\Models\User;
use App\Http\Requests\GetBookRequest;
use App\Models\Author;
use App\Models\Type;
use Illuminate\Support\Facades\Auth;
class BookController extends Controller
{
    public function getBook(Request $request)
{
    $id = $request->input("id");

    $book = Book::with(['authors:id,name', 'types:id,name'])->find($id);

    $bookpublisher = User::where('id', $book->user_id)->first(); // Fetch the user
    
    if ($bookpublisher) {
        $book->publisher = ["id" => $bookpublisher->id, "name" => $bookpublisher->name];
    } else {
        $book->publisher = null; 
    }
    $user = auth()->user();

    if (!$book) {
        return response()->json(["error" => "Book not found"], 404);
    }
    if ($book->cover_path) {
        $book->cover_path = asset("images/books/{$book->cover_path}");
    }
    $book->is_owner = $user && $book->user_id == $user->id;

    
    
    $book->pages_read = $user 
    ? $user->readingBooks()->where('book_user.book_id', $book->id)->value('book_user.pages') 
    : null;


    // $comments = Comment::where('book_id', $book->id)
    //     ->orderBy('created_at', 'desc')
    //     ->paginate(3)
    //     ->withQueryString();

    return response()->json($book, 200);
}





    ###########################################
    ###
    ### this function to fetch the books headlines and covers for homepage 
    ###
    ### takes as not required params limit that should be between 10 and 20
    ###
    ### and pages that should be bigger than 1
    ##########################################################
    public function getBooks(Request $request)
{
    
    $validator = Validator::make($request->all(), [
        "id" => "integer|min:1",  
        "limit" => "integer|min:10|max:20",
    ]);

    if ($validator->fails()) {
        return response()->json(["error" => $validator->errors()], 422);
    }

    $page = $request->input('id', 1);

    $limit = $request->input('limit', 10);

    $books = Book::select('id', 'title', 'cover_path')
    ->withCount('comments')
    ->orderBy('created_at', 'desc')
    ->paginate($limit, ['*'], 'page', $page);

$books->transform(function ($book) {
    return [
        'id' => $book->id,
        'title' => $book->title,
        'comments' => $book->comments_count > 0,
        'cover_path' => $book->cover_path ? asset("images/books/{$book->cover_path}") : null,
    ];
});

$booksArray = $books->toArray();
unset($booksArray['links']);

return response()->json($booksArray, 200);

}



    public function deletebook(Request $request){
        $validator = Validator::make($request->all(),[
            "id"=>"required|integer|exists:books,id",
        ]);
        if($validator->fails()){
            return Response()->json(["error"=>$validator->errors()],422);
        }
        $id= $request->input('id');
        $book= Book::find($id);
        if(!$book){
            return Response()->json(["error"=>"Book not found"] , 404);
        }
        try{
            $book->delete();
            return Response()->json([
                "message"=>"Book deleted sucessfully"
            ],200);
        }
        catch(\Exception $e){
            return Response()->json([
                "error"=>"an error occured while deleting this book",
                "details"=>$e->getMessage(),
            ],500);
            
        }
    }

    
    public function postbook(Request $request)
{
    $rules = [
        "title" => "required|string|max:16",
        "description" => "nullable|string|max:100",
        "authors" => "required|string",
        "types" => "required|string",
        "published_year" => "nullable|integer|digits:4",
        "isbn" => "nullable|string|max:20",
        "pages" => "required|integer|min:1",
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json(["error" => $validator->errors()], 422);
    }

    $user = auth()->user();
    if (!$user) {
        return response()->json(["error" => "Unauthorized"], 401);
    }

    $validatedData = $request->only(['title', 'description', 'pages', 'published_year']);
    $validatedData['isbn'] = $request->isbn ?? (string) rand(1000000000, 9999999999);
    $validatedData['user_id'] = $user->id; 

    $book = Book::create($validatedData);

    $authorNames = explode('*', $request->authors);
    $authorIds = [];

    foreach ($authorNames as $authorName) {
        $authorName = trim($authorName);
        if (!empty($authorName)) {
            $author = Author::firstOrCreate(["name" => $authorName]);
            $authorIds[] = $author->id;
        }
    }

    if (!empty($authorIds)) {
        $book->authors()->attach($authorIds);
    }

    $typeNames = explode('*', $request->types);
    $typeIds = [];

    foreach ($typeNames as $typeName) {
        $typeName = trim($typeName);
        if (!empty($typeName)) {
            $type = Type::firstOrCreate(["name" => $typeName]);
            $typeIds[] = $type->id;
        }
    }

    if (!empty($typeIds)) {
        $book->types()->attach($typeIds);
    }

    return response()->json(["book_id" => $book->id], 201);
}

    public function get_books_from_type_id(Request $request){
        $validator = Validator::make($request->all( ),[
            "type_id"=>"integer|required|min:1|exists:types,id"
        ]);
        if($validator->fails()){
            return response()->json([
                "error"=>$validator->errors(),
            ],422);
        }
        $type_id = $request->input('type_id');
        $type = Type::with('books')->find($type_id);
    }


    public function startReadingBook(Request $request , $id)
{
    $user = auth()->user();
   

    if (!$user) {
        return response()->json(["error" => "Unauthorized"], 401);
    }

    $book = Book::find($id);
    if (!$book) {
        return response()->json(["error" => "Book not found"], 404);
    }

    $user->readingBooks()->syncWithoutDetaching([$id]);

    return response()->json(["message" => "Book added to reading list"], 201);
}
public function updatePagesRead(Request $request, $id)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json(["error" => "Unauthorized"], 401);
    }

    $book = Book::find($id);
    if (!$book) {
        return response()->json(["error" => "Book not found"], 404);
    }

    $request->validate([
        'pages' => 'required|integer|min:0|max:' . $book->pages,
    ]);

    if (!$user->readingBooks()->where('book_id', $id)->exists()) {
        return response()->json(["error" => "Book not in reading list"], 400);
    }

    $user->readingBooks()->updateExistingPivot($id, ['pages' => $request->pages]);

    return response()->json([
        "message" => "Pages updated successfully",
        "book_id" => $id,
        "pages_read" => $request->pages
    ], 200);
}


}

