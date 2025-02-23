<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // use HasFactory, Notifiable;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    const ROLE_SLAVE = 3;
    const ROLE_CITIZEN = 2;
    const ROLE_KING = 1;

    public function isKing()
    {
        return $this->role === self::ROLE_KING;
    }

    public function isCitizen()
    {
        return $this->role === self::ROLE_CITIZEN;
    }
    public function isSlave()
    {
        return $this->role === self::ROLE_SLAVE;
    }
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function readingBooks()
    {
        return $this->belongsToMany(Book::class, 'book_user');
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function comments()
{
    return $this->hasMany(Comment::class);
}

}
