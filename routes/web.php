<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;

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
        return 'hh';
    });

    Route::get('/product/{id}', [ProductController::class, 'show']);
    Route::get('/api/product/{id}', [ProductController::class, 'getProduct']);

    Route::resource('/api/products', ProductController::class);
    Route::get('/api/addItemCart/{id}', [ProductController::class, 'addToCart']);
    Route::get('/api/productsCart', [ProductController::class, 'getCart']);
    Route::get('/api/removeItemCart/{id}', [ProductController::class, 'removeItemToCart']);
});

//MIDDLEWARE GROUP: AUTHENTICATED (USERS AUTHENTICATED)
Route::group(['middleware' => ['auth']], function() {
    Route::get('/admin', [HomeController::class, 'index'])->name('home');
});

Auth::routes();