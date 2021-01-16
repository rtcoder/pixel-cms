<?php

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Module;
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

function generateUrls(string $path, string $controllerClass, string $name, int $module)
{
    Route::get('/' . $path, [$controllerClass, 'index'])->name($name)
        ->middleware('permission:' . $module);

    Route::get('/' . $path . '/add', [$controllerClass, 'add'])
        ->name($name . '-add')
        ->middleware('permission:' . $module . ',' . Module::CREATE_ACTION);

    Route::post('/' . $path . '/add', [$controllerClass, 'create'])
        ->name($name . '-create')
        ->middleware('permission:' . $module . ',' . Module::CREATE_ACTION);

    Route::get('/' . $path . '/{id}', [$controllerClass, 'edit'])
        ->where('id', '[0-9]+')
        ->name($name . '-edit')
        ->middleware('permission:' . $module . ',' . Module::EDIT_ACTION);

    Route::post('/' . $path . '/{id}', [$controllerClass, 'update'])
        ->where('id', '[0-9]+')
        ->name($name . '-update')
        ->middleware('permission:' . $module . ',' . Module::EDIT_ACTION);

    Route::get('/' . $path . '/{id}/delete', [$controllerClass, 'destroy'])
        ->where('id', '[0-9]+')
        ->name($name . '-delete')
        ->middleware('permission:' . $module . ',' . Module::DELETE_ACTION);
}

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    generateUrls('users', UserController::class, 'users', Module::USERS_MODULE);
    generateUrls('roles', RoleController::class, 'roles', Module::ROLES_MODULE);
    generateUrls('contacts', ContactController::class, 'contacts', Module::CONTACTS_MODULE);
    generateUrls('documents', DocumentController::class, 'documents', Module::DOCUMENTS_MODULE);

    Route::get('/settings', [HomeController::class, 'index'])->name('settings');
});

Route::middleware('superadmin')->group(function () {
    generateUrls('clients', ClientController::class, 'clients', Module::CLIENTS_MODULE);

    Route::get('/login-as/{id}', [AuthController::class, 'loginAs'])
        ->where('id', '[0-9]+')->name('login-as');
});

//Media
Route::get('/client/storage/{name}', [MediaController::class, 'show']);

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
