<?php

use App\Http\Controllers\Dashboard\HomeController;
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

Route::get('/', function () {
    return redirect('login');
});

Route::get('/dashboard', [HomeController::class, 'index'])->name('home');

Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
Route::put('settings/{user}', [SettingController::class, 'udpate'])->name('settings.update');
Route::post('settings/change-menu', [SettingController::class, 'changeMenu'])->name('settings.changeMenu');

Auth::routes();
