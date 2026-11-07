<?php

declare(strict_types=1);

use CraftCms\AppleNews\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

// Keeps the Craft 5 action paths (`apple-news/article/download`, etc.) working.
// These are reachable at site action URLs too, so each one requires a login.
Route::middleware('auth')->group(function () {
    Route::get('article/download', [ArticleController::class, 'download']);
    Route::match(['get', 'post'], 'article/get-info', [ArticleController::class, 'getInfo']);
    Route::post('article/publish', [ArticleController::class, 'publish']);
});
