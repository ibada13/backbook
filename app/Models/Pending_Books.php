<?php

namespace App\Models;
use App\Models\Book ;
use Illuminate\Database\Eloquent\Model;

class Pending_Books extends Book 
{
    const STATUS_PENDING_APPROVAL = 1;

    const STATUS_PENDING_DELETION = 2;

    protected $table = "pending_books";
    protected $fillable = [
        'user_id',
        'title',
        'author',
        'isbn',
        'description',
        'published_year',
        'pages',
        'cover_path',
        'readers_count',
        'favorited_count',
        'status', 
    ];
}
