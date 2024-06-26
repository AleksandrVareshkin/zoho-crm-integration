<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZohoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/form', [ZohoController::class, 'showForm']);
Route::post('/form/submit', [ZohoController::class, 'submitForm']);
Route::get('/auth-form', [ZohoController::class, 'authForm']);
Route::post('/auth-submit', [ZohoController::class, 'authSubmit'])->name('auth-submit');

