<?php

use App\Http\Controllers\Reward\RewardController;
use App\Http\Middleware\EnsureApiUserIsAdministrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware([EnsureApiUserIsAdministrator::class])->group(function () {
    Route::get('/test', [RewardController::class, 'test']);
    Route::post('/rewards', [RewardController::class, 'registerRewardApi']);
});
