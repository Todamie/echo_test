<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

//php artisan l5-swagger:generate

Route::get('/', function () {
    return view('index');
});

Route::get('/api/authors', [AuthorController::class, 'index']);
Route::post('/api/authors/search', [AuthorController::class, 'search']);
Route::get('/api/authors/{idOrSlug}', [AuthorController::class, 'show']);

Route::get('/api/categories', [CategoryController::class, 'index']);
Route::get('/api/categories/{idOrSlug}', [CategoryController::class, 'show']);

Route::get('/api/articles', [ArticleController::class, 'index']);
Route::post('/api/articles/search', [ArticleController::class, 'search']);
Route::get('/api/articles/{idOrSlug}', [ArticleController::class, 'show']);

Route::post('/api/image', [ImageController::class, 'store']);
