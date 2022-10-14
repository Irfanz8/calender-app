<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalenderEventController;
use App\Http\Controllers\CalenderController;
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
Route::get('/', [CalenderController::class, 'index']);
Route::post('filter', [CalenderController::class, 'filter']);
Route::post('store', [CalenderController::class, 'store']);
Route::put('update', [CalenderController::class, 'update']);
Route::delete('delete', [CalenderController::class, 'delete']);

Route::get('fullcalender', [CalenderEventController::class, 'index']);
Route::post('fullcalenderAjax', [CalenderEventController::class, 'ajax']);