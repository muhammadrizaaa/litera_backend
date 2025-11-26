<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/register',    [RegisteredUserController::class, 'store']);
Route::get("/categories", [CategoryController::class, "index"]);
Route::get("/categories/{id}", [CategoryController::class, "show"]);
Route::put("/categories/edit/{id}", [CategoryController::class, "update"]);
Route::get("/books", [BookController::class, 'index']);


Route::middleware(['auth:sanctum', 'api.auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::post('/books/post', [BookController::class, 'store']);
    Route::put('/books/edit/{id}', [BookController::class, 'update']);
    Route::post('/categories/add', [CategoryController::class, "addCategoryToUser"]);
    Route::post('/categories/remove', [CategoryController::class, "removeCategoryFromUser"]);
    Route::post('/book/progress/{id}', [BookController::class, 'saveProgress']);
    Route::get('/book/continue-read', [BookController::class, 'getContinueRead']);
    Route::post("/categories/post", [CategoryController::class, "store"]);
    Route::get("/authenticated/books/{id}", [BookController::class, 'showAuthenticated']);
    Route::get("/authenticated/your-book", [BookController::class, 'getYourBook']);
    Route::get("/authenticated/fav-book/get", [BookController::class, 'getFavBooks']);
    Route::post("/authenticated/fav-book/add/{id}", [BookController::class, 'addFavBook']);
    Route::post("/authenticated/fav-book/remove/{id}", [BookController::class, 'removeFavBook']);
});
Route::get("/books/{id}", [BookController::class, 'show']);

