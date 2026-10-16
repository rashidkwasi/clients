<?php

use App\Http\Controllers\ClientsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaystackController;

Route::get('/', function () {
    return view('welcome');
});


Route::get(
    '/clients/{client}/statement',
    [ClientsController::class, 'getClientInformationphp']
)->name('clients.statement');



Route::post('/paystack/verify', [PaystackController::class, 'verify'])
    ->name('paystack.verify');
   // ->middleware('auth'); // or your client auth middleware

//Route::post('/paystack/webhook', [PaystackController::class, 'webhook']);
Route::match(['get', 'post'], '/webhook', [PaystackController::class, 'webhook']);
