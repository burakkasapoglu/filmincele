<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\WatchedController;

// ---- Auth ----
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ---- Movies (TMDB) ----
Route::get('/movies/search', [MovieController::class, 'search']);
Route::get('/movies/{id}', [MovieController::class, 'detail']);
Route::get('/movies', [MovieController::class, 'popular']);

// ---- Watched List (sync with Boş Zaman) ----
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/watched', [WatchedController::class, 'index']);
    Route::post('/watched', [WatchedController::class, 'store']);
    Route::delete('/watched/{id}', [WatchedController::class, 'destroy']);
});
