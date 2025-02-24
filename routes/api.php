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
Route::post('/book' , [BookController::class , 'postbook']);
Route::post('/book/start-reading/{id}' , [BookController::class , 'startReadingBook']);
Route::put('/book/{id}/update-pages' , [BookController::class , 'updatePagesRead']);
Route::get('/books/pending' , [BookController::class , 'Get_PENDING_Books'])->middleware('citizen');
Route::get('/books/read' , [BookController::class , 'Get_Reading_Books'])->middleware('auth:sanctum');
Route::get('/books/readed' , [BookController::class , 'Get_Readed_Books'])->middleware('auth:sanctum');
Route::get('/books/popular' , [BookController::class , 'Get_Popular_Books']);

Route::get('/comments' , [CommentController::class , 'getcommentsforbook']);
Route::post('/comments' , [CommentController::class , 'postcomment']);
Route::delete('/comments/{id}' , [CommentController::class , 'deletecomment']);
Route::put('/comments/{id}' , [CommentController::class , 'update']);

