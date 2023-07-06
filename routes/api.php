<?php

use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\Contract\InvoiceController;
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
use App\Http\Controllers\Api\v1\Contract\ContractController;
use App\Http\Controllers\Api\v1\Dashboard\DashboardController;

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
        Route::get('get-company-details', 'getCompanyDetails');
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
                Route::get('get-currency','getCurrency');
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
            Route::post('get-detail', 'show');
            Route::post('create', 'store');
            Route::post('list', 'index');
            Route::get('view/{id}', 'view');
            Route::post('update/{id}', 'update');
            Route::delete('delete/{id}', 'destroy');
            Route::post('change-status', 'changeStatus');
            Route::post('filters', 'filters');
            Route::get('get-customer-address/{id}', 'getCustomerAddresses');
            Route::post('update-list-status/{id}', 'updateListStatus');
            Route::post('customer-contract','CustomerContract');
            Route::group(['prefix' => 'comment'], function () {
                Route::post('add', 'addComment');
                Route::get('{id}/list','listComment');
                Route::post('update/{id}', 'updateComment');
                Route::delete('delete/{id}', 'deleteComment');
            });
        });

        Route::group(['prefix' => 'contract', 'controller' => ContractController::class], function () {
            Route::post('create','store');
            Route::post('client-list','index');
            Route::post('contract-list','contractList');
            Route::post('search','searchClient');
            Route::post('archive-contract','archiveContract');
            Route::get('get-details','getDetails');
            Route::put('update','updateContract');
            Route::post('suspend-contract','suspendContract');
            Route::get('view','viewContract');

            Route::group(['prefix' => 'invoices','controller' => InvoiceController::class], function () {
                Route::get('get-details/{id}', 'getInvoiceDetails');
                Route::get('create/{id}', 'createInvoices'); //optional remove it after invoice flow create
                Route::post('pay-invoice', 'payInvoiceAmount');
            });
        });

        Route::group(['prefix' => 'dashboard','controller' => DashboardController::class], function(){
            Route::get('ticket-details','ticketDetails');
            Route::get('ticket-status','ticketStatus');
        });
    });
});
