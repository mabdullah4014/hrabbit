<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
	'prefix' => config('admin.route.prefix'),
	'namespace' => config('admin.route.namespace'),
	'middleware' => config('admin.route.middleware'),
], function (Router $router) {

	$router->get('/', 'HomeController@index');
	$router->get('province', 'CommonController@province');
	$router->get('completed', 'DriverTripsController@completed');
	$router->get('unpaid', 'DriverTripsController@unpaid');
	$router->get('paid', 'DriverTripsController@paid');
	$router->get('generate', 'DriverTripsController@generate');
	$router->post('generateInvoice', 'DriverTripsController@generateInvoice');
	$router->post('ReleaseDriver', 'DriverController@ReleaseDriver');
	$router->get('DeleteDriver/{id}', 'DriverController@DeleteDriver');
	
	$router->get('DeleteCustomer/{id}', 'CustomerController@DeleteCustomer');

	$router->get('viewInvoice', 'DriverTripsController@viewInvoice');
	
	$router->get('viewReport', 'DriverPayoutController@viewReport');


	$router->get('deleteInvoice', 'DriverTripsController@deleteInvoice');
	$router->get('transaction', 'DriverTripsController@transaction');
	$router->get('viewTransaction', 'DriverTripsController@viewTransaction');
	$router->get('viewPaid', 'DriverTripsController@viewPaid');
	$router->get('earning', 'ReportsController@earning');
	$router->get('driverreport', 'ReportsController@driverList');
	$router->get('driverEarning', 'ReportsController@driverEarning');
	$router->get('viewBank', 'BankPayoutController@viewBank');
	$router->get('paypalAmount', 'BankPayoutController@paypalAmount');
	$router->get('scheduled', 'DriverTripsController@scheduled');
	$router->get('canceled', 'DriverTripsController@canceled');
	$router->get('detailRating/{id}', 'ReportsController@ratingDetail');
	$router->get('driverRating', 'ReportsController@driverRating');
	$router->get('driver_paypal', 'BankPayoutController@driver_paypal_details');
	$router->get('pay_with_paypal/{id}', 'DriverPayoutController@single_payment');
	$router->get('payout_report', 'DriverPayoutController@payout_report');
	//$router->get('accessToken', 'DriverPayoutController@access_token');

	$router->resource('pay_to_driver', DriverPayoutController::class);
	$router->resource('setting', AppSettingController::class);
	$router->resource('paypal', AdaptivePaypalSettingController::class);
	$router->resource('mobile', MobileVerificationController::class);

	$router->resource('demo/users', UserController::class);
	$router->resource('customers', CustomerController::class);
	$router->resource('drivers', DriverController::class);
	$router->resource('driverschekin', DriverCheckinController::class);
	$router->resource('category', VehicleCategoryController::class);
	$router->resource('trips', DriverTripsController::class);

	$router->resource('currency', CurrencyController::class);
	$router->resource('country', CountryController::class);
	$router->resource('state', StateController::class);
	$router->resource('contactus', ContactusController::class);
	$router->resource('bank', BankPayoutController::class);
	$router->resource('location', LocationController::class);
	$router->resource('dashboard', DashboardController::class);
	$router->resource('adminsetting', SettingsController::class);
	$router->resource('emailsetting', EmailController::class);
	$router->resource('help', HelpContentController::class);

	$router->resource('dispatch', DispatchController::class);

	$router->post('/dispatch/load_phone', 'DispatchController@load_phone');

	$router->post('/dispatch/load_number', 'DispatchController@load_number');

    $router->post('/dispatch/load_customer', 'DispatchController@load_customer');

	$router->post('/dispatch/load_email', 'DispatchController@load_email');
    
    $router->get('check_cache', 'DispatchController@check_cache');
    $router->post('deletevehicle', 'VehicleCategoryController@deletevehicle');
    $router->post('/dispatch/drivercheck', 'DispatchController@drivercheck');

    $router->post('/dispatch/getphone', 'DispatchController@getphone');

    $router->post('/dispatch/getemail', 'DispatchController@getemail');

});
