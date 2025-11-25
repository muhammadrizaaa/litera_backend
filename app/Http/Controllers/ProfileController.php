<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function getProfile() : JsonResponse {
        $user = Auth::guard('sanctum')->user();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => "User not authenticated"
            ], 401);
        }
        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'profile_pic_url' => $user->profile_pic_url,
                'banner_url' => $user->banner_url,
                'created_at' => $user->created_at,
                'categories' => $user->favoriteCategories()->get(),
                'books' => $user->book()->get(),
                // 'total_books' => $user->with('book')->get()->sum()
            ]
        ]);
    }
}
