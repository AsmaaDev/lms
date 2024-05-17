<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\PatronController;
use App\Http\Controllers\Api\BorrowingRecordController;

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

Route::resource('books', BookController::class);
Route::resource('patrons', PatronController::class);
Route::post('/borrow/{bookId}/patron/{patronId}', [BorrowingRecordController::class, 'store']);
Route::put('/return/{bookId}/patron/{patronId}', [BorrowingRecordController::class, 'returnBook']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

   
});
