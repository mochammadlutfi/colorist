<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->post('/broadcasting/auth', function (Request $request) {
//     return Broadcast::auth($request);
// });
// Broadcast::routes(['middleware' => ['auth']]);

Route::namespace('App\Http\Controllers')->name('api.')->group(function(){

    
    Route::prefix('/base')->name('base.')->group(function () {
        Route::get('/', 'BaseController@index')->name('index');
        Route::get('/set-lang/{lang}','BaseController@setLang');
        Route::get('/barcode', 'BaseController@barcode')->name('barcode');
        Route::get('/timezone', 'BaseController@timezone')->name('timezone');
    });
    
    Route::post('/login', 'AuthController@login')->name("login");

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/logout', 'AuthController@logout')->name("logout");

        Route::prefix('/profile')->name('profile.')->group(function () {
            Route::get('/', 'ProfileController@index')->name('index');
            Route::post('/update','ProfileController@update')->name('update');
            Route::post('/password','ProfileController@password')->name('password');
            Route::get('/device', 'ProfileController@device')->name('device');
            Route::post('/device/disconect','ProfileController@deviceDisconnect')->name('device.disconect');
        });

        Route::prefix('/dashboard')->name('dashboard.')->group(function () {
            Route::get('/', 'DashboardController@index')->name('index');
        });

        Route::prefix('/outlet')->name('outlet.')->group(function () {
            Route::get('/', 'OutletController@index')->name('index');
            Route::post('/store','OutletController@store')->name('store');
            Route::get('/{id}', 'OutletController@show')->name('show');
            Route::put('/{id}/update','OutletController@update')->name('update');
            Route::delete('/{id}/delete','OutletController@destroy')->name('delete');
        });

        Route::prefix('/transaction')->name('transaction.')->group(function () {
            Route::get('/', 'TransactionController@index')->name('index');
            Route::post('/store','TransactionController@store')->name('store');
            Route::get('/{id}', 'TransactionController@show')->name('show');
            Route::put('/{id}/update','TransactionController@update')->name('update');
            Route::delete('/{id}/delete','TransactionController@destroy')->name('delete');
        });
        
        Route::namespace('Settings')->prefix('/settings')->group(function () {

            Route::prefix('/user')->name('user.')->group(function () {
                Route::get('/', 'UserController@index')->name('index');
                Route::post('/store','UserController@store')->name('store');
                Route::get('/{id}', 'UserController@show')->name('show');
                Route::put('/{id}/update','UserController@update')->name('update');
                Route::delete('/{id}/delete','UserController@destroy')->name('delete');
            });
            
            Route::prefix('/permissions')->name('permissions.')->group(function () {
                Route::get('/', 'PermissionController@index')->name('index');
                Route::get('/list', 'PermissionController@list')->name('list');
                Route::post('/store','PermissionController@store')->name('store');
                Route::get('/{id}', 'PermissionController@show')->name('show');
                Route::put('/{id}/update','PermissionController@update')->name('update');
                Route::delete('/{id}/delete','PermissionController@destroy')->name('delete');
            });
            
            Route::prefix('/branch')->name('branch.')->group(function () {
                Route::get('/', 'BranchController@index')->name('index');
                Route::post('/store','BranchController@store')->name('store');
                Route::get('/{id}', 'BranchController@show')->name('show');
                Route::put('/{id}/update','BranchController@update')->name('update');
                Route::delete('/{id}/delete','BranchController@destroy')->name('delete');
            });
            
            
            Route::prefix('/colorant')->name('colorant.')->group(function () {
                Route::get('/', 'ColorantController@index')->name('index');
                Route::post('/store','ColorantController@store')->name('store');
                Route::get('/{id}', 'ColorantController@show')->name('show');
                Route::put('/{id}/update','ColorantController@update')->name('update');
                Route::delete('/{id}/delete','ColorantController@destroy')->name('delete');
            });

            Route::prefix('/product')->name('product.')->group(function () {
                Route::get('/', 'ProductController@index')->name('index');
                Route::post('/store','ProductController@store')->name('store');
                Route::get('/{id}', 'ProductController@show')->name('show');
                Route::put('/{id}/update','ProductController@update')->name('update');
                Route::delete('/{id}/delete','ProductController@destroy')->name('delete');
            });


            Route::prefix('/base-paint')->name('base-paint.')->group(function () {
                Route::get('/', 'BasePaintController@index')->name('index');
                Route::post('/store','BasePaintController@store')->name('store');
                Route::get('/{id}', 'BasePaintController@show')->name('show');
                Route::put('/{id}/update','BasePaintController@update')->name('update');
                Route::delete('/{id}/delete','BasePaintController@destroy')->name('delete');
            });

            Route::prefix('/general')->name('general.')->group(function () {
                Route::get('/', 'SystemController@general');
                Route::post('/update','SystemController@generalUpdate');
            });
            
            Route::prefix('/email')->name('email.')->group(function () {
                Route::get('/', 'SystemController@email');
                Route::post('/update','SystemController@emailUpdate');
            });

        });
            
        Route::prefix('/upload')->name('upload.')->group(function () {
            Route::get('/', 'UploadController@index')->name('index');
            Route::post('/store','UploadController@store')->name('store');
            Route::post('/{id}/send', 'UploadController@send')->name('send');
            Route::get('/{id}/download', 'UploadController@download')->name('download');
            Route::delete('/{id}/delete', 'UploadController@delete')->name('delete');
        });
        
    });
});

