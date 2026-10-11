<?php

use App\Http\Controllers\ClientsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get(
    '/clients/{client}/statement',
    [ClientsController::class, 'getClientInformationphp']
)->name('clients.statement');
