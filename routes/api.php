<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [\App\Http\Controllers\Api\v1\AuthController::class, 'login']);
    Route::post('user/register', [\App\Http\Controllers\Api\v1\UserController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::apiResource('subscription_plan', \App\Http\Controllers\Api\v1\SubscriptionPlanController::class);

        Route::get("subscription", [\App\Http\Controllers\Api\v1\SubscriptionController::class, "index"]);
        Route::post("subscription/subscribe", [\App\Http\Controllers\Api\v1\SubscriptionController::class, "subscribe"]);
        Route::put("subscription/prolongation/{subscription}", [\App\Http\Controllers\Api\v1\SubscriptionController::class, "prolongation"]);
        Route::put("subscription/cancel/{subscription}", [\App\Http\Controllers\Api\v1\SubscriptionController::class, "cancel"]);

        Route::post("payment/stripe", [\App\Http\Controllers\Api\v1\PaymentController::class, "stripe"]);
        Route::post("payment/paypal", [\App\Http\Controllers\Api\v1\PaymentController::class, "paypal"]);
        Route::post('logout', [\App\Http\Controllers\Api\v1\AuthController::class, 'logout']);
    });
});

