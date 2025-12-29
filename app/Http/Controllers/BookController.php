<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReaction;
use App\Models\ReadingProgress;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Storage;

class BookController extends Controller
{
    public function index(): JsonResponse{
        $books = Book::with(['user', 'categories'])->get();
        return response()->json([
            'success' => true,
            'message' => "Books succcessfully retrieved",
            'data' => $books
        ]);
    }

    public function getContinueRead(Request $request){
        $userId = auth()->id();
        $query = ReadingProgress::query()
            ->join('books', 'books.id', '=', 'reading_progress.book_id')
            ->where('reading_progress.user_id', $userId)
            ->with('book')
            ->select('reading_progress.*');

        if ($request->sort === 'progress') {
            // Order by progress percentage
            $query->orderByRaw('(reading_progress.progress_page / books.pages) desc');
        } else {
            // Default: last updated
            $query->orderBy('reading_progress.updated_at', 'desc');
        }

        $progressList = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Reading progress list',
            'data' => $progressList->map(function ($item) {
                return [
                    'book_id'       => $item->book->id,
                    'title'         => $item->book->name,
                    'cover'         => $item->book->cover_url,
                    'total_pages'   => $item->book->pages,
                    'progress_page' => $item->progress_page,
                    'updated_at'    => $item->updated_at,
                ];
            })
        ]);
    }

    public function getBooksByGenre($genre){
        $books = Book::whereHas('categories', function ($q) use ($genre) {
            $q->where('categories.id', $genre);
        })
        ->with('categories')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    public function show($id){
        $book = Book::with(['user'])->find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => "Book succcessfully retrieved",
            'data' => $book
        ]);
    }

    public function showAuthenticated($id){
        $book = Book::with('user')->find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found'
            ], 404);
        }

        $user = auth()->user();
        $responseData = $book->toArray();

        if ($user) {
            $progress = $book->readingProgresses()
                ->where('user_id', $user->id)
                ->first();

            $responseData['progress_page'] = $progress->progress_page ?? 0;
        }

        return response()->json([
            'success' => true,
            'message' => "Book succcessfully retrieved",
            'data' => $responseData
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $per_page = $request->input('per_page',5);

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $books = Book::with('user')->where('name', 'LIKE', "%{$query}%")
            ->orWhere('author', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->paginate($per_page);

        return response()->json([
            'success' => true,
            'message' => 'Search results retrieved successfully',
            'data' => $books
        ]);
    }


    public function store(Request $request){
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "User not authenticated"
            ], 401);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'published_date' => 'required|date',
            'cover' => 'required|image|mimes:jpg,jpeg,png|max:2048', // 2MB max
            'pdf' => 'required|mimes:pdf|max:10240', // 10MB max
            'is_visible' => 'boolean',
            'categories' => 'required|array',        // must be array
            'categories.*' => 'integer|exists:categories,id'
        ]);

        // Handle cover upload
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');
            $validated['cover_url'] = 'storage/' . $coverPath;
        }

        // Handle PDF upload
        if ($request->hasFile('pdf')) {
            $pdfFile = $request->file('pdf');
            $pdfPath = $pdfFile->store('pdfs', 'public');
            $validated['pdf_url'] = 'storage/' . $pdfPath;

            // Detect total pages using smalot/pdf-parser
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfFile->getRealPath());

            $details = $pdf->getDetails();
            $validated['pages'] = $details['Pages'] ?? 0;
        }
        
        $validated['user_id'] = $user->id;

        $book = Book::create($validated);
        $book->categories()->sync($validated['categories']);

        return response()->json([
            'success' => true,
            'message' => 'Book created successfully',
            'data' => $book
        ], 201);
    }

    public function update(Request $request, $id){
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "User not authenticated"
            ], 401);
        }

        // Book must exist
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => "Book not found"
            ], 404);
        }

        // Must be the owner
        if ($user->id !== $book->user_id) {
            return response()->json([
                'success' => false,
                'message' => "You do not have permission to edit this book"
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'author' => 'sometimes|string|max:255',
            'publisher' => 'sometimes|string|max:255',
            'published_date' => 'sometimes|date',
            'pages' => 'sometimes|integer|min:1',
            'cover' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'is_visible' => 'sometimes|boolean',
            'categories' => 'sometimes|array',
            'categories.*' => 'integer|exists:categories,id'
        ]);
        if ($request->hasFile('cover')) {
            // Delete old picture if needed (optional)
            if (isset($book) && $book->cover_url) {
                $oldPath = str_replace('storage/', '', $book->cover_url);
                Storage::disk('public')->delete($oldPath);
            }

            // Save new picture
            $coverPath = $request->file('cover')->store('covers', 'public');
            $validated['cover_url'] = 'storage/' . $coverPath;
        } 
        

        $book->update($validated);
        if ($request->has('categories')) {
            $book->categories()->sync($request->categories);
        }

        return response()->json([
            'success' => true,
            'message' => 'Book updated successfully',
            'data' => $book
        ]);
    }
    public function saveProgress(Request $request, $id)
    {
        $validated = $request->validate([
            'progress_page' => 'required|integer|min:1',
        ]);

        $progress = ReadingProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'book_id' => $id
            ],
            [
                'progress_page' => $validated['progress_page']
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Progress saved successfully',
            'data' => $progress
        ]);
    }
    public function getYourBook(){
        $user = Auth::guard('sanctum')->user();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => "User not authenticated"
            ], 401);
        }
        $books = $user->book()->get();
        return response()->json([
            'success' => true,
            'message' => 'User books retrieved successfully',
            'data' => $books
        ]);
    }

    public function getFavBooks(){
        $user = Auth::guard('sanctum')->user();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => "User not authenticated"
            ], 401);
        }
        return response()->json([
            'success' => true,
            'message' => 'Successfully got favorite book',
            'data' => $user->favoriteBook()->get()
        ]);
    }

    public function addFavBook($id){
        $user = Auth::guard('sanctum')->user();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => "User not authenticated"
            ], 401);
        }
        $book = Book::find($id);
        if(!$book){
            return response()->json([
                'success' => false,
                'message' => "Book not found"
            ], 404);
        }
        $user->favoriteBook()->syncWithoutDetaching($id);
        return response()->json([
            'success' => true,
            'message' => 'Successfully added favorite book',
            'data' => $book
        ]);
    }
    public function removeFavBook($id){
        $user = Auth::guard('sanctum')->user();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => "User not authenticated"
            ], 401);
        }
        $book = Book::find($id);
        if(!$book){
            return response()->json([
                'success' => false,
                'message' => "Book not found"
            ], 404);
        }
        $user->favoriteBook()->detach($id);
        return response()->json([
            'success' => true,
            'message' => 'Successfully remove favorite book',
            'data' => $book
        ]);
    }
    public function addBookReaction($bookId, $reaction){
        $user = Auth::guard('sanctum')->user();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => "User not authenticated"
            ], 401);
        }
        $existing = BookReaction::where('book_id', $bookId)
                                ->where('user_id', $user->id)
                                ->first();

        if (!$existing) {
            BookReaction::create([
                'book_id' => $bookId,
                'user_id' => $user->id,
                'reaction' => $reaction
            ]);

            return response()->json([
                'success' => true,
                'message'  => 'created'
            ]);
        }

        if ($existing->reaction == $reaction) {
            $existing->delete();

            return response()->json([
                'success' => true,
                'message'  => 'removed'
            ]);
        }

        $existing->update([
            'reaction' => $reaction
        ]);

        return response()->json([
            'success' => true,
            'message'  => 'updated'
        ]);
    }
}
