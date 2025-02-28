<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum','citizen'])->group(function () {
    Route::put('/users/{id}/ban',[UserController::class , 'Ban_User']);
    Route::put('/books/{id}/accept_pending_book',[BookController::class , 'Accept_Pending_Book']);
    Route::put('/books/{id}/decline-pending',[BookController::class , 'Decline_Pending_Book']);
});

Route::get('/books' , [BookController::class , 'getBooks']);
Route::get('/book' , [BookController::class , 'getBook']);
Route::post('/book' , [BookController::class , 'postbook']);
Route::post('/book/start-reading/{id}' , [BookController::class , 'startReadingBook']);
Route::put('/book/{id}/update-pages' , [BookController::class , 'updatePagesRead']);
Route::post('/book/{id}/favorite' , [BookController::class , 'favoriteit'])->middleware('auth:sanctum');
Route::post('/book/{id}/save' , [BookController::class , 'saveit'])->middleware('auth:sanctum');
Route::get('/books/pending' , [BookController::class , 'Get_PENDING_Books'])->middleware('citizen');
Route::get('/books/read' , [BookController::class , 'Get_Reading_Books'])->middleware('auth:sanctum');
Route::get('/books/readed' , [BookController::class , 'Get_Readed_Books'])->middleware('auth:sanctum');
Route::get('/books/saved' , [BookController::class , 'Get_Saved_Books'])->middleware('auth:sanctum');
Route::get('/books/popular' , [BookController::class , 'Get_Popular_Books']);
Route::get('/books/favorite' , [BookController::class , 'Get_Favorited_Books'])->middleware('auth:sanctum');
Route::get('/books/published' , [BookController::class , 'Get_Published_Books'])->middleware('auth:sanctum');
Route::get('/users' , [UserController::class , 'Get_Users'])->middleware('citizen');
// Route::put('/users/{id}/ghost');

Route::get('/comments' , [CommentController::class , 'getcommentsforbook']);
Route::post('/comments' , [CommentController::class , 'postcomment']);
Route::delete('/comments/{id}' , [CommentController::class , 'deletecomment']);
Route::put('/comments/{id}' , [CommentController::class , 'update']);

