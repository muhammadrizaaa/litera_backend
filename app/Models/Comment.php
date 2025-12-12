<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        "book_id",
        "user_id",
        "parent_id",
        "comment",
        "reply_to"
    ];

    protected $appends = ["is_uploader", "user_reply_to"];
    protected $hidden = [
        "book",
        "parent",
        "reply_to"
    ];

    public function getUserReplyToAttribute(){
        return $this->parent?->user ?? null;
    }

    public function getIsUploaderAttribute(){
        return $this->user_id == $this->book->user_id;
    }

    public function parent(){
        return $this->belongsTo(Comment::class, "parent_id");
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function replies(){
        return $this->hasMany(Comment::class, "parent_id");
    }
    public function book(){
        return $this->belongsTo(Book::class);
    }
}
