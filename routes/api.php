<?php

use App\Http\Controllers\Api\v1\Auth\AuthController;
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
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('forgot-password', 'forgotPasswordLinkEmail');
        Route::post('reset-password-details', 'getResetPwdUserDetails');
        Route::post('reset-password', 'resetPassword');
    });
});
Route::group(['prefix' => 'v1', 'middleware' => ['throttle:600,1']], function () {
    Route::middleware('auth:api')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('logout', 'logout');
        });

        Route::controller(UserController::class)->group(function () {
            Route::get('get-users', 'index');
            Route::post('create-user', 'store');
            Route::get('edit-user/{id}', 'show');
            Route::post('update-user/{id}', 'update');
        });
    });
});
