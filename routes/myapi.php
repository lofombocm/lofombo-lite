<?php

use App\Http\Controllers\Reward\RewardController;
use App\Http\Middleware\EnsureApiUserIsAdministrator;
use App\Models\Loyaltyaccount;
use Illuminate\Support\Facades\Route;




Route::get('/api/loyaltyaccount/{clientid}', function ($clientid) {
    $accounts = Loyaltyaccount::where('holderid', $clientid)->get();
    return response(array('success' => 1, 'faillure' => 0, 'response' => $accounts), 200);
});

Route::middleware([EnsureApiUserIsAdministrator::class])->group(function () {
    Route::get('/api/test', [RewardController::class, 'test']);
    Route::post('/api/rewards', [RewardController::class, 'registerRewardApi']);
});







