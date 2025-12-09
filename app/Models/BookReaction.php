<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookReaction extends Model
{
    protected $table = "post_reactions";
    protected $fillable = [
        "book_id",
        "user_id",
        "reaction"
    ];
    protected $hidden = ['created_at', 'updated_at'];
}
