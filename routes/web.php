<?php

use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\ImageController;
use App\Http\Controllers\Dashboard\SettingController;
use Illuminate\Support\Facades\Auth;
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

Route::group(['middleware' => ['https.protocol']], function () {
    Route::get('/', function () {
        return redirect('login');
    });

    Route::group(['middleware' => ['auth'], 'namespace' => 'Dashboard', 'prefix' => 'dashboard'], function () {
        Route::get('/', [HomeController::class, 'index'])->name('home.index');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('user/create', [UserController::class, 'create'])->name('users.create');
        Route::get('user/edit/{user}', [UserController::class, 'edit'])->name('users.edit');
        Route::post('user/create', [UserController::class, 'store'])->name('users.store');
        Route::put('user/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('user/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings/{user}', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/change-menu', [SettingController::class, 'changeMenu'])->name('settings.changeMenu');

        Route::put('image/{id}/upload', [ImageController::class, 'upload'])->name('image.upload');
        Route::delete('image/destroy/{file}', [ImageController::class, 'destroy'])->name('image.destroy');
    });

    Auth::routes();
});
