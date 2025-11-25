<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'author',
        'publisher',
        'published_date',
        'pages',
        'cover_url',
        'pdf_url',
        'is_visible',
        'readers',
        'likes',
        'dislikes',
        'favorites',
        'user_id',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'published_date' => 'date',
    ];
    protected $appends = ['categories'];

    protected $hidden = ['assignedToCategories'];

    // Optional: if you want relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function readingProgresses(){
        return $this->hasMany(ReadingProgress::class, 'book_id');
    }
    public function categories(){
        return $this->belongsToMany(Category::class, 'categories_to_book');
    }

    public function getCategoriesAttribute(){
        return $this->categories()->get();
    }
}
