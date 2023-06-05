<?php

use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\CustomerController;
use App\Http\Controllers\Api\v1\SettingsController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function () {
    Route::group(['prefix' => 'auth', 'controller' => AuthController::class],function () {
        Route::post('login', 'login');
        Route::post('forgot-password', 'forgotPasswordLinkEmail');
        Route::post('reset-password-details', 'getResetPwdUserDetails');
        Route::post('reset-password', 'resetPassword');
    });
});
Route::group(['prefix' => 'v1', 'middleware' => ['throttle:600,1']], function () {
    Route::middleware('auth:api')->group(function () {
        Route::group(['prefix' => 'auth', 'controller' => AuthController::class],function () {
            Route::post('logout', 'logout');
            Route::post('profile-reset-password', 'profileResetPassword');
        });

        Route::group(['prefix' => 'user', 'controller' => UserController::class], function () {
            Route::get('get-users', 'index');
            Route::post('create-user', 'store');
            Route::get('edit-user/{id}', 'show');
            Route::post('update-user/{id}', 'update');
            Route::delete('delete-user/{id}', 'destroy');
            Route::get('get-roles', 'getRoles');
            Route::post('search-user', 'searchUser');
            Route::post('update-user-profile/{id}', 'updateUserProfile');
        });

        Route::group(['prefix' => 'settings', 'controller' => SettingsController::class], function () {
            Route::prefix('company')->group(function () {
                Route::get('get', 'getCompanySettings');
                Route::post('update', 'updateCompanySettings');
            });

            //Contract Type
            Route::prefix('contract')->group(function () {
                Route::post('add', 'addContractType');
                Route::get('list', 'listAllContractType');
                Route::get('edit/{id}', 'editContractType');
                Route::post('update/{id}', 'updateContractType');
            });
        });

        Route::group(['prefix' => 'customer', 'controller' => CustomerController::class], function () {
            Route::post('create-customer', 'store');
        });
    });
});
