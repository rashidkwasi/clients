<?php

use App\Http\Controllers\CoaController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\OfflineSyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaystackController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/paystack/verify', [PaystackController::class, 'verify'])
    ->name('paystack.verify');

Route::match(['get', 'post'], '/webhook', [PaystackController::class, 'webhook']);


//Route::post('/webhook', [PaystackController::class, 'webhook']);
 //   ->middleware('auth'); // or your client auth middleware

//Route::post('/login',[AuthController::class,'login']);
//Route::post('/userforgotpassword',[AuthController::class,'userforgotpassword']);
//Route::post('/loadsetup',[SetupController::class,'loadsetup']);


Route::group(['middleware' => 'auth:sanctum'], routes: function () {

    //User
//    Route::post('/register',[AuthController::class,'register']);
//    Route::post('/userupdate',[AuthController::class,'userupdate']);
//    Route::post('/userupdatepassword',[AuthController::class,'userupdatepassword']);
//    //Route::post('/userforgotpassword',[AuthController::class,'userforgotpassword']);
//    Route::post('/allusers',[AuthController::class,'allUsers']);
//    Route::post('/logout',[AuthController::class,'logout']);
//    Route::post('/logoutall',[AuthController::class,'logoutAll']);
//    Route::post('/allusergroups',[AuthController::class,'allUserGroups']);
//    Route::post('/addusergroup',[AuthController::class,'addUserGroup']);
//    Route::post('/usergroupupdate',[AuthController::class,'UserGroupUpdate']);
//    Route::post('/offlineSync',[OfflineSyncController::class,'syncReferenceData']);
//    Route::post('/uploaddata',[OfflineSyncController::class,'uploadOffineTransaction']);
//
//    //dashboard
//    Route::post('/dashboard',[\App\Http\Controllers\Dashboard::class,'Dashboard']);

});

//Log::info('SQL Query: ' . $query->toSql());
//Log::info('SQL Bindings: ', $query->getBindings());
//truncate -s 0 storage/logs/laravel.log
