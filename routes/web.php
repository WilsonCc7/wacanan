<?php

use App\Http\Controllers\Admin\ArticleAdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::post('/articles/{article:slug}/like', [LikeController::class, 'toggle'])->name('articles.like');
Route::get('/articles/id/{article}', [ArticleController::class, 'show'])->name('articles.showById');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->prefix('admin')->group(function (): void {
    Route::get('/', [ArticleAdminController::class, 'index'])->name('admin.index');
    Route::get('/articles', [ArticleAdminController::class, 'index'])->name('admin.articles.index');
    Route::get('/articles/create', [ArticleAdminController::class, 'create'])->name('admin.articles.create');
    Route::post('/articles', [ArticleAdminController::class, 'store'])->name('admin.articles.store');
    Route::post('/articles/bulk', [ArticleAdminController::class, 'bulk'])->name('admin.articles.bulk');
    Route::get('/articles/{article}/edit', [ArticleAdminController::class, 'edit'])->name('admin.articles.edit');
    Route::put('/articles/{article}', [ArticleAdminController::class, 'update'])->name('admin.articles.update');
    Route::delete('/articles/{article}', [ArticleAdminController::class, 'destroy'])->name('admin.articles.destroy');
});
