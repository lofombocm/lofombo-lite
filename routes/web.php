<?php

use App\Http\Controllers\Auth\LoginClientController;
use App\Http\Controllers\Auth\LoginSuperAdminController;
use App\Http\Controllers\Auth\RegisterAdminController;
use App\Http\Controllers\Auth\RegisterSuperAdminController;
use App\Http\Controllers\Auth\ResetPasswordClientController;
use App\Http\Controllers\Auth\SuperAdminResetPasswordController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\VoucherControler;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\Conversion\ConversionController;
use App\Http\Controllers\FriendInvitationController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeClientController;
use App\Http\Controllers\HomeSuperAdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\RegistratIoninvitationController;
use App\Http\Controllers\Reward\RewardController;
use App\Http\Controllers\Threshold\ThresholdController;
use App\Http\Controllers\Transactiontype\TransactiontypeController;
use App\Http\Middleware\EnsureClientIsActivated;
use App\Http\Middleware\EnsureLicenseIsActive;
use App\Http\Middleware\EnsureUserIsActivated;
use App\Http\Middleware\EnsureUserIsAdministrator;
use App\Http\Middleware\EnsureUserIsSuperAdministrator;
use App\Http\Middleware\EnsureUserOrClientAreConnected;
use App\Models\Reward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;



//https://laraveldaily.com/post/multi-language-routes-and-locales-with-auth
//route(\Illuminate\Support\Facades\Route::currentRouteName(), array_merge(Route::current()->parameters(),['locale' => $locale]))
Route::get('/', function () {

    //dd(Route::current()->parameters());

    //


    $locale = GuestController::getApplicationLocal();
    /*if (count(SuperAdmin::all()) === 0 || count(User::all()) === 0) {
        (new GuestController())->index($locale);
    }*/


    //$defaultLocale = Config::get('app.locale');
    //dd(app('config'));
    //dd(app()->getLocale());
    return redirect()->route('welcome', ['locale' => $locale]);
    //\Illuminate\Support\Facades\Log::info('NKALLA LOG: APP_LOCAL: ' . app()->getLocale());
    //return redirect(app()->getLocale());
    //return redirect(app()->getLocale() . RouteServiceProvider::HOME);
})->name('local');


Route::get('activation-required', function (){
    return view('auth.activation-required');
})->name('activation-required');

//'license-is-active' => EnsureLicenseIsActive::class,
Route::prefix('{locale}')->where(['locale' => '[a-zA-Z]{2}'])->middleware([EnsureLicenseIsActive::class])->group(function () {
    /*Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth'])->name('dashboard');

    require __DIR__.'/auth.php';*/



    /*Route::get('language/{locale}', function ($locale) {
        app()->setLocale($locale);
        session()->put('locale', $locale);
        //dd(session()->all());
        return redirect()->to('/');
    });*/


    Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    /*Route::get('/', function () {
        return view('welcome');
    });*/

    Route::get('test', function () {
        return view('test');
    });

    Route::post('/registration-admin', [RegisterAdminController::class, 'postRegistration'])->name('enregistrement-admin.post');
    Route::post('/registration-super-admin', [RegisterAdminController::class, 'postRegistrationSuperAdmin'])->name('enregistrement-super-admin.post');

    Route::get('auth', [LoginController::class, 'index'])->name('authentification');
    Route::post('auth', [LoginController::class, 'postLogin'])->name('authentification.post');

//Route::get('logout', [HomeController::class, 'logout'])->name('logout');
    Route::get('password-reset', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');
    Route::post('password-reset', [ResetPasswordController::class, 'postResetPassword'])->name('password.reset.post');
    Route::post('password-reset-first-time', [LoginController::class, 'postResetPasswordFirstConnection'])->name('password.reset.first.time.post');
    /*
    Route::get('super_admin-password-reset', [SuperAdminResetPasswordController::class, 'resetPassword'])->name('super-admin.password.reset');
    Route::post('super_admin-password-reset', [SuperAdminResetPasswordController::class, 'postResetPassword'])->name('super-admin.password.reset.post');*/

//

    Route::get('password-forgot', [ForgotPasswordController::class, 'forgotPassword'])->name('password.forgot');
    Route::post('password-forgot', [ForgotPasswordController::class, 'postForgotPassword'])->name('password.forgot.post');
    Route::get('password-forgot-form/{requestid}', [ForgotPasswordController::class, 'forgotPasswordForm'])->name('password.forgot.form');
    Route::post('password-forgot-form', [ForgotPasswordController::class, 'postForgotPasswordForm'])->name('password.forgot.post.form');


    Route::get('client-password-forgot', [LoginClientController::class, 'forgotPassword'])->name('client.password.forgot');
    Route::post('client-password-forgot', [LoginClientController::class, 'postForgotPassword'])->name('client.password.forgot.post');
    Route::get('client-password-forgot-form/{requestid}', [LoginClientController::class, 'forgotPasswordForm'])->name('client.password.forgot.form');
    Route::post('client-password-forgot-form', [LoginClientController::class, 'postForgotPasswordForm'])->name('client.password.forgot.post.form');

    Route::get('admin-required', function (){
        return view('auth.admin-required');
    });

    Route::get('super-admin-required', function (){
        return view('auth.super-admin-required');
    });


    Route::middleware([EnsureUserIsActivated::class])->group(function () {
        Route::post('deconnexion', [HomeController::class, 'logout'])->name('deconnexion');

        Route::get('/home', [HomeController::class, 'dashboard'])->middleware(EnsureUserIsActivated::class)->name('dashboard');
        Route::get('/home/clients-list', [HomeController::class, 'showClients'])->name('home.clients-list')->middleware(EnsureUserIsActivated::class);

        Route::get('/home/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/home/clients', [ClientController::class, 'registerClient'])->name('clients.index.post');
        Route::get('/home/clients/{clientid}', [ClientController::class, 'clientDetails'])->name('clients.index.details');
        Route::get('/home/purchases', [PurchaseController::class, 'index'])->name('home.purchases.index');
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
        Route::post('/home/rewards/{rewardid}/delete', [RewardController::class, 'deleteReward'])->name('rewards.delete');


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

    Route::get('/client/{clientid}/friend-invitations/{invitationid}', [FriendInvitationController::class, 'getFriendInvitationAcceptationForm'])->name('client.friend-invitations.accept.index');
    Route::post('/client/{clientid}/friend-invitations/{invitationid}', [FriendInvitationController::class, 'postFriendInvitationAcceptationForm'])->name('client.friend-invitations.accept.index.post');

    Route::middleware([EnsureClientIsActivated::class])->group(function () {
        Route::get('home-client', [HomeClientController::class, 'dashboard'])->name('home.client');
        Route::get('/client/voucher', [VoucherControler::class, 'getVoucherView'])->name('vouchers.index');
        Route::get('/client/{id}/client-vouchers', [VoucherControler::class, 'getVouchers'])->name('clients.get.vouchers');
        Route::get('/client/{clientid}/friend-invitations', [HomeClientController::class, 'getFriendInvitationForm'])->name('client.friend-invitations.index');
        Route::post('/client/{clientid}/friend-invitations', [HomeClientController::class, 'postFriendInvitationForm'])->name('client.friend-invitations.index.post');
        Route::get('/client/{clientid}/friend-invitations-list', [HomeClientController::class, 'getFriendInvitationList'])->name('client.friend-invitations.list');

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
        Route::post('/home/admin/users/{user_id}/activate', [RegisterController::class, 'activateUser'])->name('utilisateurs.admin.activate');
        Route::post('/home/admin/users/{user_id}/deactivate', [RegisterController::class, 'deActivateUser'])->name('utilisateurs.admin.deactivate');
        Route::post('/home/admin/users/{userid}/remove-add-to-admin-role', [RegisterController::class, 'removeOrAddToAdminRole'])->name('utilisateurs.admin.ad.or.remove.role');
        Route::get('/bi', [HomeController::class, 'biPage'])->name('bi.menu');
        Route::get('/home/reports', [HomeController::class, 'reports'])->name('home.reports');
        Route::get('/home/products-form', [HomeController::class, 'productForm'])->name('home.products.index');
        Route::post('/home/products', [HomeController::class, 'registerProduct'])->name('home.products.index.post');


        Route::get('/reports/txs', [HomeController::class, 'reportTxs'])->name('reports.txs');
        Route::get('/reports/vouchers', [HomeController::class, 'reportVouchers'])->name('reports.vouchers');
        Route::get('/reports/clients', [HomeController::class, 'reportClients'])->name('reports.clients');
        Route::get('/home/notifications/send-bulk-messages', [HomeController::class, 'sendBulkMessageForm'])->name('send-bulk-message.admin');
        Route::post('/home/notifications/send-bulk-messages', [HomeController::class, 'sendBulkMessage'])->name('send-bulk-message.admin.post');
        Route::get('/home/client-invitations', [HomeController::class, 'showClientAcceptedInvitaions'])->name('client.invitations.accepted.index');
        Route::get('/home/client-invitations/{invitationid}', [HomeController::class, 'showClientAcceptedInvitaionsDetails'])->name('client.invitations.accepted.details');
        Route::post('/home/client-invitations/{invitationid}', [ClientController::class, 'confirmClientAcceptedInvitaions'])->name('client.invitations.accepted.confirm');
        Route::post('/home/client-invitations-refuse/{invitationid}', [ClientController::class, 'confirmClientAcceptedInvitaionsRefuse'])->name('client.invitations.accepted.refuse');
    });
});

Route::prefix('{locale}')->where(['locale' => '[a-zA-Z]{2}'])->group(function () {
    Route::get('/welcome', [GuestController::class, 'index'])->name('welcome');

    Route::middleware([EnsureUserIsSuperAdministrator::class])->group(function () {
        Route::get('auth-super-admin', [LoginSuperAdminController::class, 'indexSuperAdmin'])->name('authentification.superadmin');
        Route::post('auth-super-admin', [LoginSuperAdminController::class, 'postLoginSuperAdmin'])->name('authentification.post.superadmin');

        Route::get('/home-super-admin', [HomeSuperAdminController::class, 'dashboard'])->name('home-super-admin');

        Route::get('/home/super_admins/{super_admin_id}', [RegisterSuperAdminController::class, 'putRegistrationIndex'])->name('super_admin.update-parameter.index');
        Route::post('/home/super_admins/{super_admin_id}', [RegisterSuperAdminController::class, 'putRegistration'])->name('super_admin.update-parameter.index.put');


        Route::get('super_admin-password-reset', [SuperAdminResetPasswordController::class, 'resetPassword'])->name('super-admin.password.reset');
        Route::post('super_admin-password-reset', [SuperAdminResetPasswordController::class, 'postResetPassword'])->name('super-admin.password.reset.post');

        Route::post('deconnexion-super_admin', [HomeSuperAdminController::class, 'logout'])->name('deconnexion-super_admin');

        Route::get('/home-super-admin/licences-index', [HomeSuperAdminController::class, 'showLicenceForm'])->name('home-super-admin.license.form.index');
        Route::post('/home-super-admin/licences-index', [HomeSuperAdminController::class, 'postLicenceForm'])->name('home-super-admin.license.form.index.post');
        Route::get('/home-super-admin/licences/{licenseId}', [HomeSuperAdminController::class, 'licenceDetails'])->name('licences.index.details');
        Route::Post('/home-super-admin/licences/{licenseId}/deactivate', [HomeSuperAdminController::class, 'deactivateLicense'])->name('licences.index.details.deactivate');
        Route::Post('/home-super-admin/licences/{licenseId}/activate', [HomeSuperAdminController::class, 'activateLicense'])->name('licences.index.details.activate');
        Route::Post('/home-super-admin/licences/{licenseId}/add-user', [HomeSuperAdminController::class, 'addUserToLicense'])->name('licences.index.details.add-user');

    });
});

//
//user-is-super-admin
//utilisateurs










