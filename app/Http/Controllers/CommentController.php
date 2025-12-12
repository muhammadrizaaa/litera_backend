<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function indexByBook($bookId){
        $comments = Comment::where('book_id', $bookId)->whereNull('parent_id')->with(['replies.user', 'user'])->get();
        return response()->json([
            "success" => true,
            "message" => "successfully retreive comments data",
            "data" => $comments
        ]);
    }

    public function create($bookId, Request $request){
        $parentId = $request->input('parent_id');
        $user = $request->user();
        $book = Book::find($bookId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "User not authenticated"
            ], 401);
        }

        if(!$book){
            return response()->json([
                'success' => false,
                'message' => "Book not found"
            ]);
        }

        // if replying to a reply, attach to the original parent
        if ($parentId) {
            $parent = Comment::find($parentId);

            if ($parent->parent_id) {
                // parent is a reply → use the original parent comment
                $parentId = $parent->parent_id;
            }
        }

        Comment::create([
            'book_id' => $bookId,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'parent_id' => $parentId,
            
        ]);

        return response()->json([
            'success' => true,
            "message" => "successfully create comment"
        ]);
    }
}
