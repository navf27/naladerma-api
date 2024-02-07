<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::get('/logout', 'logout')->middleware(['auth:sanctum']);
    Route::post('/register', 'register');
    Route::get('/me', 'me')->middleware(['auth:sanctum']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get('/adm', 'index');
        Route::get('/adm/user', 'users');
        Route::get('/adm/customer', 'customers');
        Route::get('/adm/category', 'categories');
        Route::get('/adm/artwork', 'artworks');
        Route::get('/adm/event', 'events');
        Route::get('/adm/order', 'orders');
        Route::get('/adm/ticket', 'tickets');
    });
    Route::controller(CategoryController::class)->group(function () {
        Route::post('/adm/category', 'store');
        Route::patch('/adm/category/{id}', 'update');
        Route::delete('/adm/category/{id}', 'destroy');
    });
    Route::controller(EventController::class)->group(function () {
        Route::post('/adm/event', 'store');
        Route::get('/adm/event/{id}', 'show');
        Route::patch('/adm/event/{id}', 'update');
        Route::patch('/adm/event/set/{id}', 'setOnGoing');
        Route::delete('/adm/event/{id}', 'destroy');
    });
    Route::controller(ArtworkController::class)->group(function () {
        Route::post('/adm/artwork', 'store');
        Route::get('/adm/artwork/{id}', 'show');
        Route::patch('/adm/artwork/{id}', 'update');
        Route::patch('/adm/artwork/set/{id}', 'setOnGallery');
        Route::delete('/adm/artwork/{id}', 'destroy');
    });
});
