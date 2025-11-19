<?php

use App\Http\Controllers\Auth\LoginClientController;
use App\Http\Controllers\Auth\ResetPasswordClientController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\VoucherControler;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\Conversion\ConversionController;
use App\Http\Controllers\HomeClientController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\RegistratIoninvitationController;
use App\Http\Controllers\Reward\RewardController;
use App\Http\Controllers\Threshold\ThresholdController;
use App\Http\Controllers\Transactiontype\TransactiontypeController;
use App\Http\Middleware\EnsureClientIsActivated;
use App\Http\Middleware\EnsureUserIsActivated;
use App\Http\Middleware\EnsureUserIsAdministrator;
use App\Http\Middleware\EnsureUserOrClientAreConnected;
use App\Models\Reward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;


Route::get('/', function () {
    return view('welcome',['rewards' => Reward::all(), 'error' => '']);
})->name('welcome');

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('test', function () {


    /*require_once 'HTTP/Request2.php';
    $request = new HTTP_Request2();
    $request->setUrl('https://api.infobip.com/sms/2/text/advanced');
    $request->setMethod(HTTP_Request2::METHOD_POST);
    $request->setConfig(array(
        'follow_redirects' => TRUE
    ));
    $request->setHeader(array(
        'Authorization' => 'App fcf4cec9739d5ef829adebe68ab92124-7009db67-fe42-4d94-bbee-e480a78aa0ea',
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ));
    $request->setBody('{"messages":[{"destinations":[{"to":"237691179154"}],"from":"447491163443","text":"Congratulations on sending your first message. Go ahead and check the delivery report in the next step."}]}');
    $body = '';
    try {
        $response = $request->send();
        if ($response->getStatus() == 200) {
            $body =  $response->getBody();
        } else {
            $body = 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
                $response->getReasonPhrase();
        }
    } catch (HTTP_Request2_Exception $e) {
        $body =  'Error: ' . $e->getMessage();
    }*/

    /*$file=fopen('../storage/app/letest.txt','w');
    fwrite($file,'NKALLA EHAWE');
    fclose($file);*/

    /*$h = fopen('letest.txt', 'w');
    //fwrite($h, json_encode($this->data));
    fwrite($h, 'NKALLA EHAWE');
    fclose($h);*/

    return view('test'/*, ['body' => $body]*/);
});

Route::get('auth', [LoginController::class, 'index'])->name('authentification');
Route::post('auth', [LoginController::class, 'postLogin'])->name('authentification.post');

//Route::get('logout', [HomeController::class, 'logout'])->name('logout');
Route::get('password-reset', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');
Route::post('password-reset', [ResetPasswordController::class, 'postResetPassword'])->name('password.reset.post');
Route::post('password-reset-first-time', [ResetPasswordController::class, 'postResetPasswordFirstConnection'])->name('password.reset.first.time.post');


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
Route::get('admin-required', function (){
    return view('auth.admin-required');
});
Route::post('deconnexion', [HomeController::class, 'logout'])->name('deconnexion');

Route::middleware([EnsureUserIsActivated::class])->group(function () {
    Route::get('/home', [HomeController::class, 'dashboard'])->middleware(EnsureUserIsActivated::class);
    Route::get('/home/clients-list', [HomeController::class, 'showClients'])->name('home.clients-list')->middleware(EnsureUserIsActivated::class);

    Route::get('/home/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/home/clients', [ClientController::class, 'registerClient'])->name('clients.index.post');
    Route::get('/home/clients/{clientid}', [ClientController::class, 'clientDetails'])->name('clients.index.details');
    Route::get('/home/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::post('/home/purchases', [PurchaseController::class, 'registerPurchase'])->name('purchases.index.post');
    Route::get('/home/purchases-products', [PurchaseController::class, 'showProductsToUser'])->name('users.purchases-products.index');


    Route::get('/home/conversions-amount-points', [ConversionController::class, 'indexAmountPoint'])->name('conversions-amount-points.index');
    Route::post('/home/conversions-amount-points', [ConversionController::class, 'registerConversionAmountPoint'])->name('conversions-amount-points.index.post');

    Route::get('/home/thresholds', [ThresholdController::class, 'indexThreshold'])->name('thresholds.index');
    Route::post('/home/thresholds', [ThresholdController::class, 'registerThreshold'])->name('thresholds.index.post');

    Route::get('/home/rewards', [RewardController::class, 'indexReward'])->name('rewards.index');
    Route::get('/home/rewards/lists', [RewardController::class, 'indexRewardList'])->name('rewards.index.list');
    Route::post('/home/rewards', [RewardController::class, 'registerReward'])->name('rewards.index.post');
    Route::get('/home/rewards/{rewardid}/activate-deactivate', [RewardController::class, 'activateOrDeactivateReward'])->name('rewards.activate.deactivate');


    Route::get('/home/conversions-point-rewards', [ConversionController::class, 'indexPointReward'])->name('conversions-point-rewards.index');
    Route::post('/home/conversions-point-rewards', [ConversionController::class, 'registerConversionPointReward'])->name('conversions-point-rewards.index.post');

    Route::get('/transactiontypes', [TransactiontypeController::class, 'transactiontypesView'])->name('transactiontype');
    Route::post('/transactiontypes', [TransactiontypeController::class, 'createTransactiontypes'])->name('transactiontype.post');

    Route::post('/client/{id}/deactivate', [ClientController::class, 'deactivateClient'])->name('clients.put.deactive');
    Route::post('/client/{id}/activate', [ClientController::class, 'activateClient'])->name('clients.post.active');
    Route::post('/client/{id}/update', [ClientController::class, 'updateClient'])->name('clients.post.update');

    Route::get('/client/{id}/vouchers', [ClientController::class, 'getVouchers'])->name('clients.getVouchers');
    Route::get('/client/vouchers', [ClientController::class, 'getVouchersAll'])->name('clients.getVouchersAll.all');
    Route::post('/client/{id}/vouchers/{vid}/activate', [ClientController::class, 'activateVoucher'])->name('clients.activateVoucher');
    Route::post('/client/{id}/vouchers/{vid}/deactivate', [ClientController::class, 'deactivateVoucher'])->name('clients.deactivateVoucher');
    Route::post('/client/{id}/vouchers/{vid}/use', [ClientController::class, 'useVoucher'])->name('clients.useVoucher');

    Route::get('/home/loyaltytransactions/{clientId}', [HomeController::class, 'showLoyaltyTransactions'])->name('home.loyaltytransactions.client');

    Route::post(
        '/home/loyaltytransactions/search/{clientId}',
        [HomeController::class, 'showLoyaltyTransactionsClientSearch']
    )->name('home.loyaltytransactions.client.search');

    Route::get('/home/loyaltytransactions', [HomeController::class, 'showLoyaltyTransactionsAll'])->name('home.loyaltytransactions.all');
    Route::get('/home/loyaltytransactions/{txid}/details', [HomeController::class, 'showLoyaltyTransactionsDetails'])->name('home.loyaltytransactions.details');
    Route::post('/home/loyaltytransactions/search',[HomeController::class, 'showLoyaltyTransactionsSearch'])->name('home.loyaltytransactions.search');

    Route::get('/home/conversions', [ConversionController::class, 'index'])->name('conversions.index');
    Route::post('/home/conversions', [ConversionController::class, 'registerConversion'])->name('conversions.index.post');
    Route::get('/home/conversions/list', [ConversionController::class, 'conversionList'])->name('conversions.list');
    Route::post('/home/conversions/set-conversion', [ConversionController::class, 'setConversonToUse'])->name('conversions.set-conversion.post');

    Route::get('/registration-invitations', [RegisterController::class, 'registrationInvitation'])->name('registration.invitation');
    Route::post('/registration-invitations', [RegisterController::class, 'postRegistratioInvitation'])->name('registration.invitation.post');

    Route::post('/registration', [RegisterController::class, 'postRegistration'])->name('enregistrement.post');
    Route::get('/registration', [RegisterController::class, 'registration'])->name('enregistrement');

    Route::get('/home/users/{userid}', [RegisterController::class, 'putRegistrationIndex'])->name('user.update-parameter.index');
    Route::post('/home/users/{userid}', [RegisterController::class, 'putRegistration'])->name('user.update-parameter.index.put');
    Route::get('/home/users', [RegisterController::class, 'getAllUser'])->name('user.list');
    Route::get('/home/users/notifs/{userid}', [NotificationController::class, 'showNotifs'])->name('notifs.index');

});

Route::get('/registration-invitations/{id}', [RegistratIoninvitationController::class, 'index'])->name('registration.invitation-response.index');
Route::post('/registration-invitation-responses/{invitationid}', [RegistratIoninvitationController::class, 'postRegistratioInvitationResponse'])->name('registration.invitation-response.index.post');

Route::get('auth/client', [LoginClientController::class, 'loginClientView'])->name('authentification.client');

Route::post('auth/client', [LoginClientController::class, 'postLoginClientView'])->name('authentification.client.post');

//->middleware(EnsureClientIsActivated::class);

Route::get('password-reset-client', [ResetPasswordClientController::class, 'resetPassword'])->name('password.reset.client');

Route::post('password-reset-client', [ResetPasswordClientController::class, 'postResetPassword'])->name('password.reset.client.post');

Route::get('rewards', function () {
    return view('rewards-list', ['rewards' => Reward::all()]);
})->name('rewards.list.view');

Route::middleware([EnsureClientIsActivated::class])->group(function () {
    Route::get('home-client', [HomeClientController::class, 'dashboard'])->name('home.client');
    Route::get('/client/voucher', [VoucherControler::class, 'getVoucherView'])->name('vouchers.index');
    //Route::post('/client/voucher', [VoucherControler::class, 'postGenVoucher'])->name('vouchers.post');
    Route::post('deconnexion-client', [HomeClientController::class, 'logout'])->name('deconnexion.client');
    Route::post('/client/{id}/update-client', [HomeClientController::class, 'updateClient'])->name('clients.post.update.client');
    Route::get('/client/{id}/update-client', [HomeClientController::class, 'updateClientForm'])->name('clients.form.update.client');
    Route::post(
        '/home/loyaltytransactions/client/search/{clientId}',
        [HomeClientController::class, 'showLoyaltyTransactionsClientSearch']
    )->name('home.loyaltytransactions.client.search.all.post');
    Route::get(
        '/home/loyaltytransactions/client/search/{clientId}',
        [HomeClientController::class, 'showLoyaltyTransactionsAllPerClient']
    )->name('home.loyaltytransactions.client.search.all');
    Route::get('download-voucher/{voucherId}', [HomeClientController::class, 'downloadVoucher'])->name('vouchers.download');
    Route::get('/home-client/loyaltytransactions/{txid}/details', [HomeClientController::class, 'showLoyaltyTransactionsDetails'])->name('home-client.loyaltytransactions.details');
    Route::get('voucher/{voucherId}/resend-usage-code', [HomeClientController::class, 'resendUsageCodeForm'])->name('vouchers.resend.usage.code');
    Route::post('voucher/{voucherId}/resend-usage-code', [HomeClientController::class, 'resendUsageCode'])->name('vouchers.resend.usage.code.post');
    Route::get('/home/clients/notifs/{clientid}', [NotificationController::class, 'showClientNotifs'])->name('clients.notifs.index');
    //vouchers.resend.activation.code
});

Route::middleware([EnsureUserOrClientAreConnected::class])->group(function () {
    Route::get('/client/voucher', [VoucherControler::class, 'getVoucherView'])->name('vouchers.index');
    Route::post('/client/voucher', [VoucherControler::class, 'postGenVoucher'])->name('vouchers.post');
    Route::get('/notifications/{notificationid}', [NotificationController::class, 'showNotificationView'])->name('notifications.index');
    Route::post('/notifications/{notificationid}', [NotificationController::class, 'setAsReadOrUnread'])->name('notifications.index.read-or-unread');
});


Route::middleware([EnsureUserIsAdministrator::class])->group(function () {
    Route::get('/home/admin/configs', [ConfigController::class, 'showConfigForm'])->name('configs.index');
    Route::post('/home/admin/configs', [ConfigController::class, 'setSystemConfiguration'])->name('configs.post');
    Route::get('/home/admin/users', [RegisterController::class, 'getAllUserForadministrationm'])->name('utilisateurs.admin');
    Route::post('/home/admin/users/{userid}/remove-add-to-admin-role', [RegisterController::class, 'removeOrAddToAdminRole'])->name('utilisateurs.admin.ad.or.remove.role');
    Route::get('/reports', [HomeController::class, 'reportPage'])->name('reports.menu');
    Route::get('/reports/txs', [HomeController::class, 'reportTxs'])->name('reports.txs');
    Route::get('/reports/vouchers', [HomeController::class, 'reportVouchers'])->name('reports.vouchers');
    Route::get('/reports/clients', [HomeController::class, 'reportClients'])->name('reports.clients');
    Route::get('/home/notifications/send-bulk-messages', [HomeController::class, 'sendBulkMessageForm'])->name('send-bulk-message.admin');
    Route::post('/home/notifications/send-bulk-messages', [HomeController::class, 'sendBulkMessage'])->name('send-bulk-message.admin.post');

});

//utilisateurs










