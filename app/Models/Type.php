<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    
    protected $fillable=['name','description'];
    public function books (){
        return $this->belongsToMany(Book::class,'book_type','type_id','book_id');
    }
}
