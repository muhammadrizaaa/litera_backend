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
    protected $appends = ['is_favorited', 'fav_counts', 'readers_counts', 'categories'];

    protected $hidden = ['assignedToCategories'];
    public function getReadersCountsAttribute(){
        return $this->readingProgresses()->count();
    }

    public function getFavCountsAttribute(){
        return $this->favoritedByUser()->count();
    }
    public function getIsFavoritedAttribute()
    {
        $user = auth()->user();

        if (!$user) return false;

        return $this->favoritedByUser()->where('user_id', $user->id)->exists();
    }

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
    public function favoritedByUser(){
        return $this->belongsToMany(User::class, 'book_user_favorites')->withTimestamps();
    }
}
