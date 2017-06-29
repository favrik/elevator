<?php

use Illuminate\Http\Request;

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

$controller = 'ElevatorController@';
Route::post('request', $controller . 'request');
Route::post('bulk-request', $controller . 'bulkRequest');
Route::get('status', $controller . 'status');
Route::delete('reset', $controller . 'reset');

