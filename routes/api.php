<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum','citizen'])->group(function () {
    Route::put('/users/{id}/ban',[UserController::class , 'Ban_User']);
    Route::put('/books/{id}/accept_pending_book',[BookController::class , 'Accept_Pending_Book']);
    Route::put('/books/{id}/decline-pending',[BookController::class , 'Decline_Pending_Book']);

    Route::get('/users' , [UserController::class , 'Get_Users']);
    Route::delete('/mod/comments/{id}' , [CommentController::class , 'ModDeleteComment']);
    Route::put('/type/{id}' , [TypeController::class , 'update']);

});
Route::middleware(['auth:sanctum' , 'king'])->group(function(){
    Route::get('/mods' , [UserController::class , 'Get_Mods']);
    Route::put('/{id}/mod',[UserController::class , 'ModUser']);
    Route::put('/{id}/admin',[UserController::class , 'AdminUser']);
    Route::get('/books/pending' , [BookController::class , 'Get_PENDING_Books'])->middleware('citizen');


});
Route::middleware(['auth:sanctum' ])->group(function(){
    Route::post('/book' , [BookController::class , 'postbook']);
    Route::post('/book/start-reading/{id}' , [BookController::class , 'startReadingBook']);
    Route::put('/book/{id}/update-pages' , [BookController::class , 'updatePagesRead']);
    Route::post('/book/{id}/favorite' , [BookController::class , 'favoriteit']);
    Route::post('/book/{id}/save' , [BookController::class , 'saveit']);
    
    Route::get('/books/read' , [BookController::class , 'Get_Reading_Books']);
    Route::get('/books/readed' , [BookController::class , 'Get_Readed_Books']);
    Route::get('/books/saved' , [BookController::class , 'Get_Saved_Books']);
    Route::get('/books/favorite' , [BookController::class , 'Get_Favorited_Books']);
    Route::get('/books/published' , [BookController::class , 'Get_Published_Books']);
    Route::post('/comments' , [CommentController::class , 'postcomment']);
    Route::put('/comments/{id}' , [CommentController::class , 'update']);
    Route::delete('/comments/{id}' , [CommentController::class , 'deletecomment']);
});

Route::get('/books' , [BookController::class , 'getBooks']);
Route::get('/books/{id}/type' , [BookController::class , 'getBooksByType']);
Route::get('/books/{id}/author' , [BookController::class , 'getBooksByAuthor']);
Route::get('/books/{id}/user' , [BookController::class , 'getBooksByUser']);
Route::get('/book' , [BookController::class , 'getBook']);
Route::get('/{id}/author' , [AuthorController::class , 'GetAuthor']);
Route::get('/{id}/type' , [TypeController::class , 'GetType']);
Route::get('/{id}/user' , [UserController::class , 'getUserById']);
Route::get('/books/popular' , [BookController::class , 'Get_Popular_Books']);
// Route::put('/users/{id}/ghost');

Route::get('/comments' , [CommentController::class , 'getcommentsforbook']);

