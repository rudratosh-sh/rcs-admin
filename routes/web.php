<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SmartMessageAdvanceController;
use App\Http\Controllers\SmartMessageBasicController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\RcsBalanceController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return redirect('/login');
});


Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('register', [RegisterController::class, 'register']);

Route::get('password/forget',  function () {
	return view('pages.forgot-password');
})->name('password.forget');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');


Route::group(['middleware' => 'auth'], function () {
	// logout route
	Route::get('/logout', [LoginController::class, 'logout']);
	Route::get('/clear-cache', [HomeController::class, 'clearCache']);

	// dashboard route  
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

	// profile route  
	Route::get('/profile', [UserController::class, 'profileEdit'])->name('profile');
	Route::post('/profile/update', [UserController::class, 'profileUpdate']);

	//only those have manage_user permission will get access
	Route::group(['middleware' => 'can:manage_user'], function () {
		Route::get('/users', [UserController::class, 'index']);
		Route::get('/user/get-list', [UserController::class, 'getUserList']);
		Route::get('/user/create', [UserController::class, 'create']);
		Route::post('/user/create', [UserController::class, 'store'])->name('create-user');
		Route::get('/user/{id}', [UserController::class, 'edit']);
		Route::post('/user/update', [UserController::class, 'update']);
		Route::get('/user/delete/{id}', [UserController::class, 'delete']);
		Route::get('/user/changeStatus/{id}/{status}', [UserController::class, 'changeStatus']);
	});

	//only those have manage_role permission will get access
	Route::group(['middleware' => 'can:manage_role|manage_user'], function () {
		Route::get('/roles', [RolesController::class, 'index']);
		Route::get('/role/get-list', [RolesController::class, 'getRoleList']);
		Route::post('/role/create', [RolesController::class, 'create']);
		Route::get('/role/edit/{id}', [RolesController::class, 'edit']);
		Route::post('/role/update', [RolesController::class, 'update']);
		Route::get('/role/delete/{id}', [RolesController::class, 'delete']);
	});


	//only those have manage_permission permission will get access
	Route::group(['middleware' => 'can:manage_permission|manage_user'], function () {
		Route::get('/permission', [PermissionController::class, 'index']);
		Route::get('/permission/get-list', [PermissionController::class, 'getPermissionList']);
		Route::post('/permission/create', [PermissionController::class, 'create']);
		Route::get('/permission/update', [PermissionController::class, 'update']);
		Route::get('/permission/delete/{id}', [PermissionController::class, 'delete']);
	});

	// get permissions
	Route::get('get-role-permissions-badge', [PermissionController::class, 'getPermissionBadgeByRole']);

	// permission examples
	Route::get('/permission-example', function () {
		return view('permission-example');
	});
	// API Documentation
	Route::get('/rest-api', function () {
		return view('api');
	});
	// Editable Datatable
	Route::get('/table-datatable-edit', function () {
		return view('pages.datatable-editable');
	});

	//reports 
	Route::group(['middleware' => 'can:view_reports'], function () {
		Route::get('/smart-report', [ReportsController::class, 'smartReport'])->name("smart-report");
		Route::get('/smart-report-data',[ReportsController::class,'smartReportData'])->name('smart-report-data');
		Route::get('/campaign-report', [ReportsController::class, 'campaignReport'])->name("campaign-report");
		Route::get('/campaign-report-data',[ReportsController::class,'campaignReportData'])->name('campaign-report-data');
		Route::get('/download-campaign-report',[ReportsController::class,'downloadCampaignReport'])->name('download-campaign-report');
		Route::get('/delete-campaign-report',[ReportsController::class,'deleteCampaignReport'])->name('delete-campaign-report');
	});

	//smart messaging basic 
	Route::group(['middleware' => 'can:rcs_send_smart_message_basic'], function () {
		Route::get('/smart-message-basic', [SmartMessageBasicController::class, 'index']);
		Route::post('/send-smart-message-basic', [SmartMessageBasicController::class, 'sendSmartMessageBasic'])->name('send-smart-message-basic');
	});

	//smart messaging advance 
	Route::group(['middleware' => 'can:rcs_send_smart_message_advance'], function () {
		Route::get('/smart-message-advance', [SmartMessageAdvanceController::class, 'index']);
		Route::post('/send-smart-message-advance', [SmartMessageAdvanceController::class, 'sendSmartMessageAdvance'])->name('send-smart-message-advance');;
	});

	//templates
	Route::post('api/fetch-template', [TemplateController::class, 'fetchTemplate']);
	Route::post('api/delete-template', [TemplateController::class, 'deleteTemplate']);


	//filter messages 
	Route::group(['middleware' => 'can:filter_messages'], function () {
		Route::get('/filter-messages', [FilterController::class, 'index'])->name('filter-messages');
		Route::post('/store-filter-messages', [FilterController::class, 'store'])->name('store-filter-messages');
		Route::get('/filter-validation', [FilterController::class, 'filterMobileNumbers'])->name('filter-validation');
	});

	//rcs balance management
	Route::group(['middleware' => 'can:rcs_account_report'], function () {
		Route::get('/rcs-balance-editor', [RcsBalanceController::class, 'index'])->name('rcs-balance-editor');
		Route::get('/rcs-users-ajax', [RcsBalanceController::class, 'getBalanceByUser'])->name('rcs-users-ajax');
		Route::post('/rcs-balance-store', [RcsBalanceController::class, 'store'])->name('rcs-balance-store');
		Route::get('/account-report', [RcsBalanceController::class, 'report'])->name('account-report');

	});
});
 
	
//background services
Route::get('send-basic-sms-background', [SmartMessageBasicController::class, 'sendBulkBasicSms'])->name('send-bulk-basic-sms');
Route::get('send-advance-sms-background', [SmartMessageAdvanceController::class, 'sendBulkAdvanceSms'])->name('send-bulk-advance-sms');
