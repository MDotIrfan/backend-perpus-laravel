<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Models\Profile;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("/v1")->group(function () {

    Route::apiResource('/category', CategoryController::class);
    Route::apiResource('/book', BooksController::class);
    Route::apiResource('/borrow', BorrowController::class);

    Route::prefix("/auth")->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('/profile', [ProfileController::class, 'store']);
    });

    Route::middleware(['auth:api', 'isAdmin'])->group(function () {
        Route::apiResource('/role', RoleController::class);
    });
});
