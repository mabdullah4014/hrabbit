<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::middleware(['auth.apikey'])->get('/test', function (Request $request) {
//     return $request->user(); // Returns the associated model to the API key
// });

// KEY01718C67609A2A8CE6322DC06ECD87A2_bkuHVRJn1FBgNPjQMjiqam
Route::group([

	'middleware' => 'api',
	'prefix' => 'auth',

], function ($router) {
	Route::post('login', 'AuthController@login');
	//Route::post('driver/add', 'AuthController@login');
});
// Route::get('sendSms', function () {
// 	return send_sms("this is message", "+923147592241");
// });
Route::post('user/verify', 'AuthController@verifyUser');
Route::post('user/verifyOtp', 'AuthController@verifyOtp');
Route::post('customer/add', 'AuthController@addCustomer');
Route::post('driver/add', 'AuthController@addDriver');
Route::post('driver/sendOTP', 'AuthController@sendOTP');
// Route::post('customer/changePass', 'AuthController@changePass');
Route::post('driver/changePass', 'AuthController@changePassDriver');
Route::post('driver/sendOTP', 'AuthController@sendOTP');
Route::post('driver/forgetPassword', 'AuthController@forgetPasswordDriver');
Route::post('customer/forgetPassword', 'AuthController@forgetPasswordCustomer');

Route::post('customer/view', 'CustomerController@viewCustomer');
Route::post('customer/update', 'CustomerController@updateCustomer');
Route::post('customer/uploadImage', 'CustomerController@uploadImage');
Route::post('customer/feedback', 'CustomerController@addFeedback');
Route::post('customer/logout', 'AuthController@logoutCustomer');
Route::post('driver/logout', 'AuthController@logoutDriver');

Route::post('vehicleCategory', 'CustomerController@vehicleCategory');
Route::post('payWithpaypal', 'CustomerController@payWithpaypal');

Route::post('driver/view', 'DriverController@viewDriver');
Route::post('driver/update', 'DriverController@updateDriver');
Route::post('driver/uploadImage', 'DriverController@uploadImage');
Route::post('driver/proof', 'DriverController@uploadProof');
Route::post('driver/viewFeedback', 'DriverController@viewFeedback');
Route::post('driver/viewBank', 'DriverController@viewBank');
Route::post('driver/addBank', 'DriverController@addBank');
Route::post('booking/customer_timeout_cancel', 'BookingController@cusTimeoutCancel');
Route::post('booking/history/ongoing', 'BookingController@viewOngoing');
Route::post('booking/history/past', 'BookingController@viewPast');
Route::post('booking/history/upcoming', 'BookingController@viewUpcoming');
Route::post('booking/history/mailInvoice', 'BookingController@mailInvoice');

Route::get('pdfview', array('as' => 'pdfview', 'uses' => 'BookingController@mailInvoice'));

Route::post('booking/add', 'BookingController@addTrip');
Route::post('booking/getEstimatedFare', 'BookingController@getEstimatedFare');

Route::post('booking/admin_assign', 'BookingController@admin_assign');

Route::post('booking/driverCheckIn', 'BookingController@driverCheckIn');
Route::post('booking/driverCheckOut', 'BookingController@driverCheckOut');
Route::post('booking/Categories', 'BookingController@Categories');
Route::post('booking/appSetting', 'BookingController@appSetting');
Route::post('booking/countriesList', 'BookingController@countriesList');
Route::post('booking/stateList', 'BookingController@stateList');
Route::post('booking/driverList', 'BookingController@driverList');
Route::post('booking/history/viewJobs', 'BookingController@viewJobs');
Route::post('calculate_estimate', 'BookingController@calculateEstimate');
Route::get('croncheck', 'BookingController@cronCheck');
Route::post('automatic_assign', 'BookingController@automatic_assign');
Route::post('get_booking_data', 'BookingController@get_booking_data');
Route::get('booking/getphone', 'BookingController@getphone');

Route::get('booking/getemail', 'BookingController@getemail');
Route::get('booking/driverStatusUpdate', 'BookingController@driverStatusUpdate');
Route::get('booking/charge', 'BookingController@charge');
Route::get('booking/getProfile', 'BookingController@getProfile');
Route::get('booking/refund', 'BookingController@refund');
Route::get('booking/refundJobInitiate', function(){
	dispatch(new \App\Jobs\RefundTransaction());
});

Route::get('booking/distance', "BookingController@calculateDistance");
