<?php
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\AccountController;
use App\Http\Controllers\Dashboard\BlockchainController;
use App\Http\Controllers\Dashboard\InstitutionController;
use App\Http\Controllers\Dashboard\ImageController;
use App\Http\Controllers\Dashboard\ArkController;
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
    // Route::get('/', function () {
    //     // return redirect('login');
    // });
    Route::get('/', [SiteController::class, 'index'])->name('site.index');
    Route::get('/documentation', [SiteController::class, 'documentation'])->name('site.documentation');

    Route::group(['middleware' => ['auth'], 'namespace' => 'Dashboard', 'prefix' => 'dashboard'], function () {
        Route::get('/login', [HomeController::class, 'index'])->name('home.index');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('user/create', [UserController::class, 'create'])->name('users.create');
        Route::get('user/edit/{user}', [UserController::class, 'edit'])->name('users.edit');
        Route::post('user/create', [UserController::class, 'store'])->name('users.store');
        Route::put('user/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('user/destroy/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('institutions', [InstitutionController::class, 'index'])->name('institutions.index');
        Route::get('institution/create', [InstitutionController::class, 'create'])->name('institutions.create');
        Route::get('institution/edit/{institution}', [InstitutionController::class, 'edit'])->name('institutions.edit');
        Route::post('institution/create', [InstitutionController::class, 'store'])->name('institutions.store');
        Route::put('institution/{institution}', [InstitutionController::class, 'update'])->name('institutions.update');
        Route::delete('institution/destroy/{institution}', [InstitutionController::class, 'destroy'])->name('institutions.destroy');


        Route::get('arks', [ArkController::class, 'index'])->name('institutions.index-ark');
        Route::get('ark/create-ark', [ArkController::class, 'create'])->name('institutions.create-ark');
        Route::post('ark/create-ark', [ArkController::class, 'store'])->name('institutions.store-ark');
        Route::delete('ark/destroy/{ark}', [ArkController::class, 'destroy'])->name('institutions.destroy-ark');

        Route::get('institutions/{institution}/blockchain-id', [InstitutionController::class, 'addIdBlockchain'])->name('institutions.add_id_blockchain');

        Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::get('account/create', [AccountController::class, 'create'])->name('accounts.create');
        Route::get('account/edit/{account}', [AccountController::class, 'edit'])->name('accounts.edit');
        Route::post('account/create', [AccountController::class, 'store'])->name('accounts.store');
        Route::put('account/{account}', [AccountController::class, 'update'])->name('accounts.update');
        Route::delete('account/destroy/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
        Route::get('account/wallet/{account}', [AccountController::class, 'createWallet'])->name('accounts.account-create');
        Route::get('account/card-profile/{account}', [AccountController::class,'cardProfile'])->name('accounts.card_profile');
        Route::post('account/manager', [AccountController::class, 'accountManager'])->name('accounts.manager');
        Route::get('account/transfer-funds', [AccountController::class, 'transferFunds'])->name('accounts.transfer_funds');
        Route::get('account/balance/{account}', [AccountController::class, 'getBalance'])->name('accounts.get_balance');
        

        Route::get('blockchains', [BlockchainController::class, 'index'])->name('blockchains.index');
        Route::get('blockchain/create', [BlockchainController::class, 'create'])->name('blockchains.create');
        Route::get('blockchain/edit/{blockchain}', [BlockchainController::class, 'edit'])->name('blockchains.edit');
        Route::post('blockchain/create', [BlockchainController::class, 'store'])->name('blockchains.store');
        Route::put('blockchain/{blockchain}', [BlockchainController::class, 'update'])->name('blockchains.update');
        Route::delete('blockchain/destroy/{blockchain}', [BlockchainController::class, 'destroy'])->name('blockchains.destroy');
        Route::get('blockchain/logs', [BlockchainController::class, 'showLogs'])->name('blockchains.log');
        Route::get('blockchain/backup-blockchain', [BlockchainController::class, 'backup'])->name('blockchains.backup_blockchain');
        Route::get('blockchain/logs/fetch', [BlockchainController::class, 'fetchLogs'])->name('blockchain.logs_fetch');


        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings/{user}', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/change-menu', [SettingController::class, 'changeMenu'])->name('settings.changeMenu');

        Route::put('image/{id}/upload', [ImageController::class, 'upload'])->name('image.upload');
        Route::delete('image/destroy/{file}', [ImageController::class, 'destroy'])->name('image.destroy');

       
    });

    Auth::routes();
});
