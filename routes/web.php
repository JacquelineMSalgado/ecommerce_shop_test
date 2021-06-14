<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

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
    // Load a principal view
    Route::get('/', function () {
        return view('welcome');
    });

    // Load a check out page
    Route::get('/checkout', function() {
        return view('product/check-out');
    });

    // API Routes Product
    Route::get('/api/productSlug/{id}', [ProductController::class, 'show']);
    Route::get('/api/product/{id}', [ProductController::class, 'getProduct']);

    // API Routes Cart
    Route::get('/api/productsCart', [ProductController::class, 'getCart']);
    Route::get('/api/addItemCart/{id}', [ProductController::class, 'addToCart']);
    Route::get('/api/removeItemCart/{id}', [ProductController::class, 'removeItemToCart']);
    Route::get('/api/totalCart', [ProductController::class, 'totalCart']);
    Route::get('/api/removeCart', [ProductController::class, 'removeCart']);

    // API Route Order
    Route::post('/api/order/store', [OrderController::class, 'store']);
});

//MIDDLEWARE GROUP: AUTHENTICATED (USERS AUTHENTICATED)
Route::group(['middleware' => ['auth']], function() {
    // Load admin pages
    Route::get('/admin', [HomeController::class, 'index'])->name('home');
    Route::get('/admin/users', [HomeController::class, 'index'])->name('users');
    Route::get('/admin/products', [HomeController::class, 'index'])->name('products');

    // API Routes User
    Route::resource('/api/users', UserController::class);
});

// Authentication Routes
Auth::routes();

// API Routes Product
Route::resource('/api/products', ProductController::class);