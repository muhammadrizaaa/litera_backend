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
    protected $appends = ['user_reaction', 'is_favorited', 'fav_counts', 'readers_counts', 'comments_counts', 'categories'];

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
    public function getCommentsCountsAttribute(){
        return $this->comments()->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function bookReactions(){
        return $this->hasMany(BookReaction::class, 'book_id');
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
    public function getLikesAttribute()
    {
        return $this->bookReactions()->where('reaction', 1)->count();
    }

    public function getDislikesAttribute()
    {
        return $this->bookReactions()->where('reaction', -1)->count();
    }

    public function getUserReactionAttribute()
    {
        if (!auth()->check()) return 0;

        return $this->bookReactions()
            ->where('user_id', auth()->id())
            ->value('reaction') ?? 0;
    }
    public function comments(){
        return $this->hasMany(Comment::class, 'book_id');
    }
}
