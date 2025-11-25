<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "categories";
    protected $fillable = [
        'name',
        'description',
        'pic_url'
    ];
    protected $hidden = ['created_at', 'updated_at'];
    public function favoritedByUsers(){
        return $this->belongsToMany(User::class, 'categories_to_users');
    }
    public function assignToBook(){
        return $this->belongsToMany(Book::class, 'categories_to_book');
    }
}
