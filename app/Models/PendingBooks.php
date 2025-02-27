<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendingBook extends Book
{
    use HasFactory;

    const STATUS_PENDING_APPROVAL = 1;

    const STATUS_PENDING_DELETION = 2;
    protected $table = 'pending_books';

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

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


   

   

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_book', 'book_id', 'author_id');
    }

    public function types()
    {
        return $this->belongsToMany(Type::class, 'book_type', 'book_id', 'type_id');
    }


}
