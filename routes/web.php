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
Route::get('/auth-form', function () {
    return view('layouts.app');
})->name('auth-form');
Route::post('/auth-submit', [ZohoController::class, 'authSubmit'])->name('auth-submit');
Route::get('/zoho/oauth/callback', [ZohoController::class, 'authCallback']);

