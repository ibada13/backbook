<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Commment ;
use App\Models\Author ;
use App\Models\Type ;

class Book extends Model
{
    use HasFactory; 
    // protected $table="books";
    const STATUS_PENDING_APPROVAL = 1;
    const STATUS_APPROVED = 2;
    const STATUS_PENDING_DELETION = 3;
    const STATUS_DELETED = 4;
    protected $fillable = [
        'user_id',
        'title',
        'author',
        'isbn',
        'description',
        'published_year',
        'pages',
        'cover_path',
        'status'
    ];

    public function creator(){
        return $this->belongsTo(User::class, 'user_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if ($book->current_page_number > $book->pages) {
                $book->current_page_number = $book->pages; 
            }
        });
    }
    public function comments(){
        return $this->hasMany(Comment::class);
    }
    public function authors(){
        return $this->belongsToMany(Author::class,'author_book','book_id','author_id');
    }
    public function types(){
        return $this->belongsToMany(Type::class,'book_type','book_id','type_id');

    }

    public function readers()
    {
        return $this->belongsToMany(User::class, 'book_user');
    }
    
}
