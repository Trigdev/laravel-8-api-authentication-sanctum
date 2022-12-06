<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register'])->name('user.register');
Route::post('/login', [AuthController::class, 'login'])->name('user.login');

Route::group(["middleware" => ["auth:sanctum"]], function(){
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/post', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
    Route::put('/posts/{id}',[PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::post('/file-upload', [FileController::class, 'store'])->name('file.store');
    Route::post('/file-uploads', [FileController::class, 'multiple'])->name('files.store');
    Route::post('/uploader', [FileController::class, 'uploader']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');
});
