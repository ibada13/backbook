<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/books' , [BookController::class , 'getBooks']);
Route::get('/book' , [BookController::class , 'getBook']);

Route::get('/comments' , [CommentController::class , 'getcommentsforbook']);
Route::post('/comments' , [CommentController::class , 'postcomment']);
Route::delete('/comments' , [CommentController::class , 'deletecomment']);
