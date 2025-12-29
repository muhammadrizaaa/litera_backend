<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'message' => 'Succesfully to get all category',
            'data' => $categories
        ]);
    }
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Succesfully to get category',
            'data' => $category
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'pic' => 'nullable||image|mimes:jpg,jpeg,png|max:2048'
        ]);
        if ($request->hasFile('pic')) {
            // Store file in "storage/app/public/categories"
            $coverPath = $request->file('pic')->store('categories', 'public');

            // Save only the relative path, e.g. "storage/categories/abc123.jpg"
            $validated['pic_url'] = 'storage/' . $coverPath;
        }

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:255',
            'pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('pic')) {
            // Delete old picture if needed (optional)
            if (isset($category) && $category->pic_url) {
                $oldPath = str_replace('storage/', '', $category->pic_url);
                Storage::disk('public')->delete($oldPath);
            }

            // Save new picture
            $coverPath = $request->file('pic')->store('categories', 'public');
            $validated['pic_url'] = 'storage/' . $coverPath;
        }   

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }
    public function addCategoryToUser(Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Validate category IDs input
        $validated = $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ]);

        // Attach categories to user (avoids duplicates)
        $user->favoriteCategories()->sync($validated['categories']);

        return response()->json([
            'success' => true,
            'message' => 'Categories added to favorites successfully',
            'data' => $user->favoriteCategories()->get()
        ], 200);
    }
    public function removeCategoryFromUser(Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Validate category IDs input
        $validated = $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ]);

        // Attach categories to user (avoids duplicates)
        $user->favoriteCategories()->detach($validated['categories']);

        return response()->json([
            'success' => true,
            'message' => 'Categories removed from favorites successfully',
            'data' => $user->favoriteCategories()->get()
        ], 200);
    }
}
