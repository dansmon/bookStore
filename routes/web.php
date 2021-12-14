<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RentBookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('home');
// });

Route::get('/', [HomeController::class, 'indexBlank']);

Auth::routes();
Route::get('/home', [HomeController::class, 'index']);
Route::get('/viewResRen', [HomeController::class, 'getResRen']);

Route::get('/book/books', [BookController::class, 'booksData']);
Route::get('/book/bookPic', [BookController::class, 'bookPicLookup']);
Route::post('/book/store', [BookController::class, 'store']);
Route::delete('/book/remove/{ident}/{zap}', [BookController::class, 'destroy']);
Route::put('/book/update/{id}', [BookController::class, 'update']);

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'is_admin'], function () {
        Route::get('/book/subBooks/{id}', [BookController::class, 'subBooks']);
    });
});


Route::get('/resRent/userBooks/{od}/{do}/{date}/{duration}/{title}/{author}/{year}/{category}/{mode}/{reloadIdent}', [RentBookController::class, 'loadingBooks']);
Route::get('/resRent/findResBook/{ident}/{date}/{duration}/{dayPicked}', [RentBookController::class, 'bookIdeRes']);
Route::get('/resRent/subBooks/{polIdent}/{obdobjeDatum}/{casRezIzp}', [RentBookController::class, 'bookSubBooks']);


Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'is_admin'], function () {
        Route::get('/resRent/users', [RentBookController::class, 'userLookup']);
    });
});


Route::post('/resRent/store', [RentBookController::class, 'store']);
Route::put('/resRent/update/{id}', [RentBookController::class, 'update']);
Route::get('/resRent/resRents', [RentBookController::class, 'resRenData']);
Auth::routes();
