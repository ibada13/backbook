<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable=['name','bio'];

    public function books (){
        return $this->belongsToMany(Book::class,'author_book','author_id','book_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
