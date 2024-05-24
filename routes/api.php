<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
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
    Route::get('/emailForgotPassword', 'emailForgotPassword');
    Route::patch('/resetPassword/{token}', 'resetPassword');
    Route::get('/me', 'me')->middleware(['auth:sanctum']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get('/adm', 'index');
        Route::get('/adm/users', 'users');
        Route::get('/adm/customers', 'customers');
        Route::get('/adm/categories', 'categories');
        Route::get('/adm/artworks', 'artworks');
        Route::get('/adm/events', 'events');
        Route::get('/adm/orders', 'orders');
        Route::get('/adm/tickets', 'tickets');
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
        Route::patch('/adm/event/set/{id}', 'setStatus');
        Route::delete('/adm/event/{id}', 'destroy');
    });
    Route::controller(ArtworkController::class)->group(function () {
        Route::post('/adm/artwork', 'store');
        // Route::get('/adm/artwork/{id}', 'show');
        Route::patch('/adm/artwork/{id}', 'update');
        Route::patch('/adm/artwork/set/{id}', 'setOnGallery');
        Route::delete('/adm/artwork/{id}', 'destroy');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::patch('/usr/update/{id}', 'update');
    });
    Route::controller(OrderController::class)->group(function () {
        Route::post('/checkout-user/{event_id}', 'checkoutUser');
    });
});

// public endpoints
Route::post('/checkout-customer/{event_id}', [OrderController::class, 'checkoutCustomer']);
Route::post('/finalTransaction/{order_id}', [OrderController::class, 'finalTransaction']);
Route::post('/callback', [OrderController::class, 'midtransCallback']);

Route::get('/all-events', [EventController::class, 'index']);
Route::get('/adm/event/{id}', [EventController::class, 'show']);

// Route::get('/testing', [AdminController::class, 'param']);
Route::get('/testing', [AdminController::class, 'test']);
