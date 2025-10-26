<?php

use App\Models\Loyaltyaccount;
use Illuminate\Support\Facades\Route;




Route::get('/api/loyaltyaccount/{clientid}', function ($clientid) {
    $accounts = Loyaltyaccount::where('holderid', $clientid)->get();
    return response(array('success' => 1, 'faillure' => 0, 'response' => $accounts), 200);
});








