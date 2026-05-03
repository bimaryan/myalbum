<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Album\AlbumController;
use App\Http\Controllers\Photo\PhotoController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public Album Routes
Route::get('/album', [AlbumController::class, 'index']);
Route::get('/album/{album}', [AlbumController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected API Routes (Butuh Token Bearer)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User Profile (Bawaan biasa kepake buat Flutter nyari data user login)
    Route::get('/user', function (Request $request) {
        return response()->json(['success' => true, 'data' => $request->user()]);
    });

    // Album CRUD (Store, Update, Delete)
    Route::post('/album', [AlbumController::class, 'store']);
    Route::post('/album/{album}', [AlbumController::class, 'update']); // Pakai POST dengan method _method=PUT di Flutter jika upload file
    Route::delete('/album/{album}', [AlbumController::class, 'destroy']);

    // Photos
    Route::post('/album/{album}/photos', [PhotoController::class, 'upload']);
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy']);
    Route::post('/album/{album}/photos/reorder', [PhotoController::class, 'reorder']);
});