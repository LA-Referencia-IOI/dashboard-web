<?php

use App\Http\Controllers\Dashboard\HomeController;
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

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings/{user}', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/change-menu', [SettingController::class, 'changeMenu'])->name('settings.changeMenu');

        Route::put('image/{id}/upload', [ImageController::class, 'upload'])->name('image.upload');
        Route::delete('image/destroy/{file}', [ImageController::class, 'destroy'])->name('image.destroy');
    });

    Auth::routes();
});
