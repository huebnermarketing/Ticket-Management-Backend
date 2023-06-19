<?php

use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\CustomerController;
use App\Http\Controllers\Api\v1\Settings\ContractTypeController;
use App\Http\Controllers\Api\v1\Settings\ProblemTypeController;
use App\Http\Controllers\Api\v1\Settings\ProductServiceController;
use App\Http\Controllers\Api\v1\Settings\SettingsController;
use App\Http\Controllers\Api\v1\Settings\TicketStatusController;
use App\Http\Controllers\Api\v1\Ticket\TicketController;
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

        Route::group(['prefix' => 'settings'], function () {
            Route::group(['prefix' => 'company', 'controller' => SettingsController::class], function () {
                Route::get('get', 'getCompanySettings');
                Route::post('update', 'updateCompanySettings');
            });

            Route::group(['prefix' => 'contract-type', 'controller' => ContractTypeController::class], function () {
                Route::post('add', 'store');
                Route::get('list', 'index');
                Route::get('edit/{id}', 'edit');
                Route::post('update/{id}', 'update');
                Route::delete('delete/{id}', 'destroy');
            });

            Route::group(['prefix' => 'problem-type', 'controller' => ProblemTypeController::class], function () {
                Route::post('add', 'store');
                Route::get('list', 'index');
                Route::get('edit/{id}', 'edit');
                Route::post('update/{id}', 'update');
                Route::delete('delete/{id}', 'destroy');
            });

            Route::group(['prefix' => 'ticket-status', 'controller' => TicketStatusController::class], function () {
                Route::post('add', 'store');
                Route::get('list', 'index');
                Route::get('edit/{id}', 'edit');
                Route::post('update/{id}', 'update');
                Route::delete('delete/{id}', 'destroy');
            });

            Route::group(['prefix' => 'product-service', 'controller' => ProductServiceController::class], function () {
                Route::post('add', 'store');
                Route::get('list', 'index');
                Route::get('edit/{id}', 'edit');
                Route::post('update/{id}', 'update');
                Route::delete('delete/{id}', 'destroy');
            });
        });

        Route::group(['prefix' => 'customer', 'controller' => CustomerController::class], function () {
            Route::post('create', 'store');
            Route::get('list', 'index');
            Route::get('edit/{id}', 'edit');
            Route::post('update/{id}', 'update');
            Route::delete('delete/{id}', 'destroy');
            Route::post('search', 'searchCustomer');

            Route::group(['prefix' => 'address'], function () {
                Route::post('add', 'addCustomerAddress');
                Route::get('get/{id}', 'getCustomerAddress');
                Route::post('update/{id}', 'updateCustomerAddress');
                Route::delete('delete/{id}', 'deleteCustomerAddress');
            });
        });

        Route::group(['prefix' => 'ticket', 'controller' => TicketController::class], function () {
            Route::get('get-detail', 'show');
            Route::post('create', 'store');
            Route::get('list', 'index');
            Route::get('view/{id}', 'view');
            Route::post('update/{id}', 'update');
            Route::delete('delete/{id}', 'destroy');
            Route::post('change-status', 'changeStatus');
            Route::post('filters', 'filters');
            Route::group(['prefix' => 'comment'], function () {
                Route::post('add', 'addComment');
                Route::post('update/{id}', 'updateComment');
                Route::delete('delete/{id}', 'deleteComment');
            });
        });
    });
});
