<?php

use App\Http\Controllers\Auth\LoginClientController;
use App\Http\Controllers\Auth\ResetPasswordClientController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\VoucherControler;
use App\Http\Controllers\Conversion\ConversionController;
use App\Http\Controllers\HomeClientController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Reward\RewardController;
use App\Http\Controllers\Threshold\ThresholdController;
use App\Http\Controllers\Transactiontype\TransactiontypeController;
use App\Http\Middleware\EnsureClientIsActivated;
use App\Http\Middleware\EnsureUserIsActivated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('test', function () {
    return view('test');
});

Route::get('auth', [LoginController::class, 'index'])->name('authentification');
Route::post('auth', [LoginController::class, 'postLogin'])->name('authentification.post');

//Route::get('logout', [HomeController::class, 'logout'])->name('logout');
Route::get('password-reset', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');
Route::post('password-reset', [ResetPasswordController::class, 'postResetPassword'])->name('password.reset.post');


Route::get('password-forgot', [ForgotPasswordController::class, 'forgotPassword'])->name('password.forgot');
Route::post('password-forgot', [ForgotPasswordController::class, 'postForgotPassword'])->name('password.forgot.post');
Route::get('password-forgot-form/{requestid}', [ForgotPasswordController::class, 'forgotPasswordForm'])->name('password.forgot.form');
Route::post('password-forgot-form', [ForgotPasswordController::class, 'postForgotPasswordForm'])->name('password.forgot.post.form');



Route::get('client-password-forgot', [LoginClientController::class, 'forgotPassword'])->name('client.password.forgot');
Route::post('client-password-forgot', [LoginClientController::class, 'postForgotPassword'])->name('client.password.forgot.post');
Route::get('client-password-forgot-form/{requestid}', [LoginClientController::class, 'forgotPasswordForm'])->name('client.password.forgot.form');
Route::post('client-password-forgot-form', [LoginClientController::class, 'postForgotPasswordForm'])->name('client.password.forgot.post.form');

Route::get('activation-required', function (){
    return view('auth.activation-required');
});
Route::post('deconnexion', [HomeController::class, 'logout'])->name('deconnexion');

Route::middleware([EnsureUserIsActivated::class])->group(function () {
    Route::get('/home', [HomeController::class, 'dashboard'])->middleware(EnsureUserIsActivated::class);
    Route::get('/home/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/home/clients', [ClientController::class, 'registerClient'])->name('clients.index.post');
    Route::get('/home/clients/{clientid}', [ClientController::class, 'clientDetails'])->name('clients.index.details');
    Route::get('/home/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::post('/home/purchases', [PurchaseController::class, 'registerPurchase'])->name('purchases.index.post');

    Route::get('/home/conversions-amount-points', [ConversionController::class, 'indexAmountPoint'])->name('conversions-amount-points.index');
    Route::post('/home/conversions-amount-points', [ConversionController::class, 'registerConversionAmountPoint'])->name('conversions-amount-points.index.post');

    Route::get('/home/thresholds', [ThresholdController::class, 'indexThreshold'])->name('thresholds.index');
    Route::post('/home/thresholds', [ThresholdController::class, 'registerThreshold'])->name('thresholds.index.post');

    Route::get('/home/rewards', [RewardController::class, 'indexReward'])->name('rewards.index');
    Route::post('/home/rewards', [RewardController::class, 'registerReward'])->name('rewards.index.post');

    Route::get('/home/conversions-point-rewards', [ConversionController::class, 'indexPointReward'])->name('conversions-point-rewards.index');
    Route::post('/home/conversions-point-rewards', [ConversionController::class, 'registerConversionPointReward'])->name('conversions-point-rewards.index.post');

    Route::get('/transactiontypes', [TransactiontypeController::class, 'transactiontypesView'])->name('transactiontype');
    Route::post('/transactiontypes', [TransactiontypeController::class, 'createTransactiontypes'])->name('transactiontype.post');

    Route::get('/client/voucher', [VoucherControler::class, 'getVoucherView'])->name('vouchers.index');
    Route::post('/client/voucher', [VoucherControler::class, 'postGenVoucher'])->name('vouchers.post');

    Route::post('/client/{id}/deactivate', [ClientController::class, 'deactivateClient'])->name('clients.put.deactive');
    Route::post('/client/{id}/activate', [ClientController::class, 'activateClient'])->name('clients.post.active');
    Route::post('/client/{id}/update', [ClientController::class, 'updateClient'])->name('clients.post.update');

    Route::get('/client/{id}/vouchers', [ClientController::class, 'getVouchers'])->name('clients.getVouchers');
    Route::post('/client/{id}/vouchers/{vid}/activate', [ClientController::class, 'activateVoucher'])->name('clients.activateVoucher');
    Route::post('/client/{id}/vouchers/{vid}/deactivate', [ClientController::class, 'deactivateVoucher'])->name('clients.deactivateVoucher');

    Route::get('/home/loyaltytransactions', [HomeController::class, 'showLoyaltyTransactions'])->name('home.loyaltytransactions');

    Route::get('/home/conversions', [ConversionController::class, 'index'])->name('conversions.index');
    Route::post('/home/conversions', [ConversionController::class, 'registerConversion'])->name('conversions.index.post');
    Route::get('/home/conversions/list', [ConversionController::class, 'conversionList'])->name('conversions.list');
    Route::post('/home/conversions/set-conversion', [ConversionController::class, 'setConversonToUse'])->name('conversions.set-conversion.post');

    Route::get('/registration', [RegisterController::class, 'registration'])->name('enregistrement');
    Route::post('/registration', [RegisterController::class, 'postRegistration'])->name('enregistrement.post');



    //conversions.list
    //.index
});

Route::get('auth/client', [LoginClientController::class, 'loginClientView'])->name('authentification.client');

Route::post('auth/client', [LoginClientController::class, 'postLoginClientView'])->name('authentification.client.post');

//->middleware(EnsureClientIsActivated::class);

Route::get('password-reset-client', [ResetPasswordClientController::class, 'resetPassword'])->name('password.reset.client');

Route::post('password-reset-client', [ResetPasswordClientController::class, 'postResetPassword'])->name('password.reset.client.post');

Route::middleware([EnsureClientIsActivated::class])->group(function () {
    Route::get('home-client', [HomeClientController::class, 'dashboard'])->name('home.client');
    Route::get('/client/voucher', [VoucherControler::class, 'getVoucherView'])->name('vouchers.index');
    Route::post('/client/voucher', [VoucherControler::class, 'postGenVoucher'])->name('vouchers.post');
    Route::post('deconnexion-client', [HomeClientController::class, 'logout'])->name('deconnexion.client');
});





