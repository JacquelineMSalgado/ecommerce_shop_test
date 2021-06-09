<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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

//MIDDLEWARE GROUP: CLIENT (USERS NOT AUTHENTICATED)
Route::group(['middleware' => ['guest']], function() {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/checkout', function() {
        return 'Check out';
    });

    Route::resource('/api/products', ProductController::class);
    Route::get('/api/addItemCart/{id}', [App\Http\Controllers\ProductController::class, 'addToCart']);
    Route::get('/api/productsCart', [App\Http\Controllers\ProductController::class, 'getCart']);
    Route::get('/api/removeItemCart/{id}', [App\Http\Controllers\ProductController::class, 'removeItemToCart']);
});

//MIDDLEWARE GROUP: AUTHENTICATED (USERS AUTHENTICATED)
Route::group(['middleware' => ['auth']], function() {
    Route::get('/admin', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

Auth::routes();