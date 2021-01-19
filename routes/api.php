<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteFormController;
use Illuminate\Support\Facades\App;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    echo App::version();
});


// Route::group(['prefix' => 'website-form'], function () {
//     Route::get('/', [WebsiteFormController::class, 'index']);
//     Route::post('/', [WebsiteFormController::class, 'store']);
// });

// Route::group(['prefix' => 'auth'], function () {
//     Route::post('/register', [AuthController::class, 'register']);
// });

// Route::group(['prefix' => 'products', 'middleware' => 'auth:api'], function () {
//     Route::get('/', [ProductController::class, 'index']);
//     Route::post('/', [ProductController::class, 'store']);
//     Route::get('/{id}', [ProductController::class, 'show']);
//     Route::put('/{id}', [ProductController::class, 'update']);
//     Route::delete('/{id}', [ProductController::class, 'delete']);
// });


Route::group(['prefix' => 'website-form'], function () {
    Route::get('/', 'WebsiteFormController@index');
    Route::post('/', 'WebsiteFormController@store');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', 'AuthController@register');
});


Route::group(['prefix' => 'products/incomplete', 'middleware' => 'auth:api'], function () {
    Route::get('/', 'ProductController@incomplete');
    Route::post('/complete', 'ProductController@customStore');
    Route::post('/', 'ProductController@registerIncompleteProduct');
    Route::delete('/', 'ProductController@deleteIncomplete');
});

Route::group(['prefix' => 'products', 'middleware' => 'auth:api'], function () {
    Route::get('/', 'ProductController@index');
    Route::get('/{id}', 'ProductController@show');
    Route::put('/{id}', 'ProductController@update');
    Route::post('/', 'ProductController@store');
    Route::delete('/{id}/soft', 'ProductController@softDelete');
    Route::delete('/{id}/force', 'ProductController@forceDelete');
});

Route::group(['prefix' => 'files', 'middleware' => 'auth:api'], function () {
    Route::post('/product/image-upload', 'FileController@storeImage');
    Route::post('/product/video-upload', 'FileController@storeVideo');
    Route::post('/product-defect/image-upload', 'FileController@storeImageDefect');
    Route::post('/product-defect/video-upload', 'FileController@storeVideoDefect');
    Route::delete('/', 'FileController@delete');
});

Route::post('/how-you-heard-about-us', 'HeardAboutUsController@index');
