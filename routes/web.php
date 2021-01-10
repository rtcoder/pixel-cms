<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
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

function generateUrls(string $path, string $controllerClass, string $name)
{
    Route::get('/' . $path, [$controllerClass, 'index'])->name($name);

    Route::get('/' . $path . '/add', [$controllerClass, 'add'])
        ->name($name . '-add');

    Route::post('/' . $path . '/add', [$controllerClass, 'create'])
        ->name($name . '-create');

    Route::get('/' . $path . '/{id}', [$controllerClass, 'edit'])
        ->where('id', '[0-9]+')
        ->name($name . '-edit');

    Route::post('/' . $path . '/{id}', [$controllerClass, 'update'])
        ->where('id', '[0-9]+')
        ->name($name . '-update');

    Route::get('/' . $path . '/{id}/delete', [$controllerClass, 'destroy'])
        ->where('id', '[0-9]+')
        ->name($name . '-delete');
}

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    generateUrls('users', UserController::class, 'users');
    generateUrls('roles', RoleController::class, 'roles');
    generateUrls('clients', UserController::class, 'clients');

    Route::get('/settings', [HomeController::class, 'index'])->name('settings');
});

Route::middleware('superadmin')->group(function () {
    generateUrls('contacts', ContactController::class, 'contacts');

    Route::get('/login-as/{id}', [AuthController::class, 'loginAs'])
        ->where('id', '[0-9]+')->name('login-as');
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
