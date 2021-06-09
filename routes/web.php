<?php

use Illuminate\Support\Facades\Route;

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
});

//MIDDLEWARE GROUP: AUTHENTICATED (USERS AUTHENTICATED)
Route::group(['middleware' => ['auth']], function() {
    Route::get('/admin', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

Auth::routes();