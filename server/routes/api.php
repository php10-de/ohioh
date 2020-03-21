<?php

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

Route::post('/register', 'Auth\RegisterController@register');
Route::post('/login', 'Auth\LoginController@login');

Route::get('/user', function () {
    return response()->json([
        'name' => 'Abigail',
        'state' => 'CA'
    ]);        
});

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/test', function () {
        return response()->json([
            'name' => 'Abigail',
            'state' => 'CA'
        ]);        
    });
});
