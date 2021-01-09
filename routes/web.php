<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts');
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/roles', [HomeController::class, 'index'])->name('roles');
    Route::get('/clients', [HomeController::class, 'index'])->name('clients');
    Route::get('/settings', [HomeController::class, 'index'])->name('settings');
});

//AUTH
Route::get('/login', function () {
    return view('pages.auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', function () {
    return view('pages.auth.register');
})->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
