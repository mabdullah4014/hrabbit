<?php

namespace App\Http\Controllers;

use App\AdaptivePaypalSetting;
use App\AppSetting;
use App\Country;
use App\Currency;
use App\Customer;
use App\CustomerFeedback;
use App\Driver;
use App\DriverCheckin;
use App\DriverTrip;
use App\Email;
use App\Language;
use App\State;
use App\VehicleCategory;
use Carbon\Carbon;
use FCM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Mail;
use PDF;

class BookingController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 *
	 */
	public function viewOngoing(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'user_id' => 'required',
			'type' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.');
		}
		if ($input['type'] == "customer") {
			$ratings = CustomerFeedback::select(DB::raw('AVG(driver_rating) as ratings'))->groupBy('customers_id')->get();
			$ratings = array();
			foreach ($ratings as $key) {
				$ratings[$key->to_id] = $key->rating;
			}
			$customer_id = $input['user_id'];
			$jobs = DriverTrip::where('cus_id', $customer_id)->where('payment_status', '!=', '1')->get();
			if (count($jobs) == 0) {
				$message['code'] = 404;
				$message['error'] = 'No record Found.';
				$response['message'] = $message;
				return response()->json($response, 200);
			} else {
				$response['result'] = $jobs;
				$response['message'] = "Ongoing jobs listed successful";
				return response()->json($response, 200);
			}
		} elseif ($input['type'] == "driver") {
			$driver_id = $input['user_id'];
			$jobs = DriverTrip::where('driver_id', $driver_id)->where('payment_status', '!=', '1')->get();
			if (count($jobs) == 0) {
				$message['code'] = 404;
				$message['error'] = 'No record Found.';
				$response['message'] = $message;
				return response()->json($response, 200);
			} else {

				$response['result'] = $jobs;
				$response['message'] = "Ongoing jobs listed successful";
				return response()->json($response, 200);
			}
		}
	}

	public function mailInvoice(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'booking_id' => 'required',

		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.');
		}
		$jobs = DriverTrip::where('id', $input['booking_id'])->first();

		$category = str_replace('_', ' ', $jobs->vehicle_type);
		$amount = round($jobs->total_amount, 2);
		$social_url = DB::table('settings')->first();
		$logo = $social_url->logo_url;
		$curr = AppSetting::select('currency')->first();
		$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();

		$data = array(
			'DriverName' => $jobs->driver_name,
			'CustomerName' => $jobs->customer_name,
			'Ridedate' => $jobs->today_date,
			'VehicleCategory' => $category,
			'PickUpFrom' => $jobs->pick_up,
			'Dropto' => $jobs->drop_location,
			'TotalDistance' => $jobs->total_distance,
			'TotalAmount' => $amount,
			'logo' => $logo,
			'currency' => $currency->symbol,
		);

		$pdf = PDF::loadView('mails.pdfview', $data);
		$customer_id = $jobs->cus_id;
		$customer_email = Customer::where('id', $customer_id)->first();
		$email = $customer_email->email;
		$app_name = env("APP_NAME");
		$from_mail = env("MAIL_USERNAME");

		Mail::send('mails.pdftemplate', $data, function ($message) use ($pdf, $email, $app_name, $from_mail) {
			$message->from($from_mail, $app_name);

			$message->to($email)->subject('Invoice');

			$message->attachData($pdf->output(), "invoice.pdf");
		});
		$response['message'] = "Mail sent successfully";
		return $response;
	}

	public function viewUpcoming(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'user_id' => 'required',
			'type' => 'required',
			'offset' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.');
		}$offset = $input['offset'];

		$current_time = date("H:i:s", strtotime(date("H:i:s") . " -5 minutes"));

		$date = date('Y-m-d H:i:s');
		if ($input['type'] == "customer") {
			// //	$ratings = CustomerFeedback::select(DB::raw('AVG(driver_rating) as ratings'))->groupBy('customers_id')->get();
			// 	$ratings = array();
			// 	foreach ($ratings as $key) {
			// 		$ratings[$key->to_id] = $key->rating;
			// 	}
			$customer_id = $input['user_id'];
			$jobs = DriverTrip::where('cus_id', $customer_id)->where('status', '0')->where('ride_time', '>=', $date)->offset($offset)->limit(8)->get();
			if (count($jobs) == 0) {
				$response['message'] = 'No record Found.';
				$response['page'] = $offset;
				$response['next_page'] = 0;
				return response()->json($response, 200);
			} else {
				$response['result'] = $jobs;
				$response['message'] = "Upcoming jobs listed successful";
				$response['page'] = $offset;
				$response['next_page'] = $offset + 8;
				return response()->json($response, 200);
			}
		} elseif ($input['type'] == "driver") {
			$driver_id = $input['user_id'];
			$jobs = DriverTrip::where('driver_id', $driver_id)->where('status', '0')->offset($offset)->limit(8)->get();
			if (count($jobs) == 0) {
				$response['message'] = 'No record Found.';
				$response['page'] = $offset;
				$response['next_page'] = 0;
				return response()->json($response, 200);
			} else {
				$response['result'] = $jobs;
				$response['message'] = "Ongoing jobs listed successful";
				$response['page'] = $offset;
				$response['next_page'] = $offset + 8;
				return response()->json($response, 200);
			}
		}
	}

	public function driverCheckIn(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'driver_id' => 'required',
			'latitude' => 'required',
			'longitude' => 'required',
			'mode' => 'required',

		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.');
		}if ($input['mode'] == "add") {
			$driver_details = Driver::where('id', $input['driver_id'])->first();
			if (is_object($driver_details)) {
				$driver = DriverCheckin::where('driver_id', $input['driver_id'])->first();
				if (is_object($driver)) {

					$vehice = VehicleCategory::where('id', $driver_details->vehicle_id)->first();
					if (!is_object($vehice)) {
						$response['result'] = null;
						$response['message'] = "Vehicle not found";
						return response()->json($response, 200);
					} elseif (is_object($vehice) && $vehice->status != '1') {
						$response['result'] = null;
						$response['message'] = "Vehicle is inactive";
						return response()->json($response, 200);
					}

					$driver->d_lat = $input['latitude'];
					$driver->d_lon = $input['longitude'];
					$driver->checkin_time = date('H:i:s');
					$driver->today_date = date('Y-m-d');
					$driver->checkin_time = date('H:i:s');
					$driver->checkin_status = isset($input['checkin_status']) ? $input['checkin_status'] : "1";
					$driver->booking_status = isset($input['booking_status']) ? $input['booking_status'] : "0";
					$driver->waiting_status = isset($input['waiting_status']) ? $input['waiting_status'] : "0";
					$driver->update();
					$response['result'] = $driver;
					$response['message'] = "success";

					$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
					$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
					$database = $firebase->getDatabase();

					$updates = [
						'drivers_status/' . $input['driver_id'] . '/admin_status' => '0',
					];
					$newpost = $database->getReference()
						->update($updates);

					return response()->json($response, 200);
				} else {
					$input['name'] = $driver_details->name;
					$input['lname'] = $driver_details->last_name;
					$input['vehicle_type'] = $driver_details->vehicle_type;
					$input['vehicle_id'] = $driver_details->vehicle_id;
					$input['today_date'] = date('Y-m-d');
					$input['checkin_time'] = date('H:i:s');
					$input['d_lat'] = $input['latitude'];
					$input['d_lon'] = $input['longitude'];
					$input['checkin_status'] = isset($input['checkin_status']) ? $input['checkin_status'] : "1";
					$input['booking_status'] = isset($input['booking_status']) ? $input['booking_status'] : "0";
					$input['waiting_status'] = isset($input['waiting_status']) ? $input['waiting_status'] : "0";
					$user = DriverCheckin::create($input);
					$response['result'] = $user;
					$response['message'] = "success";
					return response()->json($response, 200);
				}
			} else {
				$response['message'] = "failure";
				return response()->json($response, 200);
			}
		} else {
			$response['message'] = "failure";
			return response()->json($response, 200);
		}
	}

	public function driverCheckOut(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'driver_id' => 'required',
			'mode' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.');
		}
		if ($input['mode'] == "checkout") {
			$driver = DriverCheckin::where('driver_id', $input['driver_id'])->first();
			if (is_object($driver)) {
				$driver->checkin_status = isset($input['checkin_status']) ? $input['checkin_status'] : "0";
				$driver->booking_status = isset($input['booking_status']) ? $input['booking_status'] : "0";
				$driver->waiting_status = isset($input['waiting_status']) ? $input['waiting_status'] : "0";
				$driver->update();
				$response['result'] = $driver;
				$response['message'] = "success";
				return response()->json($response, 200);
			} else {
				$response['message'] = "failure";
				return response()->json($response, 200);
			}
		} else {
			$response['message'] = "failure";
			return response()->json($response, 200);
		}

	}

	public function CronCheck() {
		$ctime = date("h:i A", strtotime("+20 minutes"));
		$c1time = date("h:i A", strtotime("+40 minutes"));

		$trips = DriverTrip::whereDate('today_date', date('Y-m-d'))->where('current_status', 0)->where('cancel_status', '<>', '1')->whereBetween('pick_up_time', [$ctime, $c1time])->get();

		if (count($trips) > 0) {
			foreach ($trips as $trip) {
				$tripcount = 0;
				if (strtotime($trip->today_date . ' ' . $trip->pick_up_time) > strtotime(now())) {
					// Check Previous date

					$drivers = DriverCheckin::where('checkin_status', 1)->where('booking_status', 0)->where('vehicle_id', $trip->vehicle_id)->get();
					$count = 0;
					if (count($drivers) > 0) {
						$total_drivers = count($drivers);

						foreach ($drivers as $driver) {
							$customer = Customer::where('id', $trip->cus_id)->first();
							$pickup_lat = $trip->pickup_lat;
							$pickup_lon = $trip->pickup_lon;
							$pick_up_time = $trip->pick_up_time;
							$d_lat = $driver->d_lat;
							$d_lon = $driver->d_lon;
							// $total_km = $this->distance($d_lat, $d_lon, $pickup_lat, $pickup_lon, "K");
							$total_km = $this->getDistance($pickup_lat, $pickup_lon, $d_lat, $d_lon);
							$total_kms = round($total_km, 2);
							if ($total_kms < 5.00) {
								$token = $driver->driver->device_id;
								$msg = "New Ride Request";
								if ($token != '' && $token != '0') {
									$this->sendFCMDriver($token, $msg);
								}

								$count++;
								$tripcount++;
								$input1['DriverId'] = $driver->driver->id;
								$input1['CustomerId'] = $trip->cus_id;
								$input1['CustomerName'] = $trip->customer_name;
								$input1['CustomerPhoneNumber'] = $trip->customer->country . $trip->customer->phone_number;
								$input1['TodayDate'] = Date('Y-m-d', time());
								$input1['PhoneNumber'] = $trip->customer->phone_number;
								$input1['PickupTime'] = $trip->pick_up_time;
								$input1['Status'] = 0; // Service request waiting
								$service = VehicleCategory::where('vehicle_type', $trip->vehicle_type)->first();
								$input1['CategoryType'] = $trip->vehicle_type;
								$input1['PriceKm'] = $service->price_per_km; //	123456
								// $customer->user_photo = env('IMG_URL') . 'storage/' . $customer->user_photo;
								$image1 = $trip->customer->user_photo;
								$trimmed = str_replace('/public', '/storage', $image1);

								if ($trip->added_by != '' && $trip->added_by != '0') {
									$input1['RequestFrom'] = 'Admin';
									$input1['RequestFromOtp'] = 1;
								} else {
									$input1['RequestFrom'] = 'Normal';
									$input1['RequestFromOtp'] = 1;
								}

								$input1['CustomerProfile'] = $trimmed;
								//	$input1['CustomerProfile'] = $trip->customer->user_photo;
								$input1['PickupLocation'] = $trip->pick_up;
								$input1['DropLocation'] = $trip->drop_location;
								$input1['PickupLatitude'] = $trip->pickup_lat;
								$input1['PickupLongitude'] = $trip->pickup_lon;
								$input1['DropLatitude'] = $trip->drop_lat;
								$input1['DropLongitude'] = $trip->drop_lon;
								$input1['CreatedTime'] = date('d/m/y H:i:s');
								$input1['Title'] = "Schedule Trip Request";
								$input1['Profile'] = $trip->customer->user_photo;
								$input1['Status'] = 1;
								$input1['CustomerLastName'] = $trip->customer_lname;
								$input1['BookingId'] = $trip->id;

								//$input['DriverId'] = $driver->driver->id;
								$ctime1 = date('H:i:s');

								if ($trip->added_by != '' && $trip->added_by != '0') {
									$input1['RequestFrom'] = 'Admin';
									$input1['RequestFromOtp'] = 1;
								} else {
									$input1['RequestFrom'] = 'Normal';
									$input1['RequestFromOtp'] = 1;
								}

								$input['CustomerName'] = $trip->customer_name;
								$input['CustomerLastName'] = $trip->customer_lname;
								$input['RequestTime'] = Date('h:i:s a', time());
								$input['PickupLocation'] = $trip->pick_up;
								$input['DropLocation'] = $trip->drop_location;
								$input['PickupTime'] = $trip->pick_up_time;
								$input['Status'] = 1; // Service request waiting
								$input['BookingId'] = $trip->id;
								$service = VehicleCategory::where('vehicle_type', $trip->vehicle_type)->first();
								$input['CategoryType'] = $trip->vehicle_type;
								$this->saveFirebaseCron($input, $input1, $trip->cus_id, $driver->driver->id);
								$this->saveFirebaseCronCus($input1, $trip->cus_id);
								/////////////////////
								$response['message'] = "Push Message Sent";
								$response['count'] = $count;
							}
						}
						if ($count == 0) {
							$customer = Customer::where('id', $trip->cus_id)->first();
							$token = $customer->device_id;
							$msg = "No Drivers Available";
							if ($token != '' && $token != '0') {
								$this->sendFCMDriver($token, $msg);
							}
							$response['message'] = "No Drivers Available";
							//echo "No Drivers Available";
						} else {
							$response['message'] = "Push Message Sent";
							$response['count'] = $count;
							//return $response;
						}
					} else {
						$customer = Customer::where('id', $trip->cus_id)->first();
						$token = $customer->device_id;
						$msg = "No Drivers Available";
						if ($token != '' && $token != '0') {
							$this->sendFCMDriver($token, $msg);
						}
						$response['message'] = "No Drivers Available";
						//echo "No Drivers Available";
					}

				} // Check Previous date
				if ($tripcount == 0) {
					/*$customer = Customer::where('id', $trip->cus_id)->first();
						$token = $customer->device_id;
						$msg = "No Trips Available";
						$this->sendFCMDriver($token, $msg);*/
					$response['message'] = "No Trips Found";
					//echo "No Trips Found";
				}
			}
		} else {
			$response['message'] = "No Trips Found";
			//echo response()->json($response, 200);
			//echo "No Trips Found";
		}
		return $response;
	}

	public function distance($d_lat, $d_lon, $c_lat, $c_lon, $unit) {
		$theta = $d_lon - $c_lon;
		$dist = sin(deg2rad($d_lat)) * sin(deg2rad($c_lat)) + cos(deg2rad($d_lat)) * cos(deg2rad($c_lat)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		if ($unit == "K") {
			return ($miles * 1.609344);
		} else {
			return $miles;
		}
	}

	public function appSetting() {
		$setting = AppSetting::first()->toArray();
		$response['app_setting'] = $setting;
		$paypal_setting = AdaptivePaypalSetting::first();

		// $smstemplastes = SmsTemplate::where('status',1)->get();
		// $response['sms_templates'] = $smstemplastes->toArray();
		$currency = Currency::where('status', 1)->get();
		$response['currency'] = $currency->toArray();
		$language = Language::where('status', 1)->get();
		$response['language'] = $language->toArray();
		foreach ($response['currency'] as $value) {
			if ($value['id'] == (int) $setting['currency']) {
				$setting['currency'] = [$value];
			}
		}
		$response['app_setting'] = $setting;
		$response['app_setting']['paypal_type'] = $paypal_setting->paypal_option;
		$response['app_setting']['paypal_client_id'] = $paypal_setting->paypal_client_id;
		$response['message'] = 'App settings listed successfully.';
		return response()->json($response, 200);
	}

	public function Categories(Request $request) {

		//$id = $request->input('id');
		$customerCategories = VehicleCategory::where('status', 1)->get();
		foreach ($customerCategories as $object) {
			$var = $object->toArray();
			$var['price_per_km'] = (string) $var['price_per_km'];
			$var['base_fare'] = (string) $var['base_fare'];
			$arrays[] = $var;
		}
		$response['result'] = [];
		if (!empty($arrays)) {
			$response['result']['categories'] = $arrays;
			// 	$response['message'] = 'No records Found.';
			// 	return response()->json($response, 200);
		}

		$response['result']['makes'] = \App\VehicleMake::all();
		$response['result']['models'] = \App\VehicleModel::all();
		$response['result']['colors'] = \App\VehicleColor::all();
		$response['message'] = 'categories listed successfully.';
		return response()->json($response, 200);
	}

	public function sendError($message) {
		$message = [
			'code' => 401,
			'error' => $message,
		];
		$response['message'] = $message;
		return response()->json($response, 200);
	}

	public function viewPast(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'user_id' => 'required',
			'type' => 'required',
			'offset' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.');
		}
		if ($input['type'] == "customer") {
			$offset = $input['offset'];
			$ratings = CustomerFeedback::select(DB::raw('AVG(driver_rating) as ratings'))->groupBy('customers_id')->get();
			$ratings = array();
			foreach ($ratings as $key) {
				$ratings[$key->to_id] = $key->rating;
			}
			$customer_id = $input['user_id'];
			$jobs = DriverTrip::where('cus_id', $customer_id)->whereIn('status', [6, 8, 9])->orderBy('today_date', 'desc')->orderBy('pick_up_time', 'desc')->offset($offset)->limit(8)->get();
			if (count($jobs) == 0) {
				$response['result'] = [];
				$response['message'] = 'No record Found.';
				$response['page'] = 0;
				$response['next_page'] = 0;
				return response()->json($response, 200);

				return response()->json($response, 200);
			} else {
				$currency_list = AppSetting::first();
				$currency_id = $currency_list->currency;
				$currency_name = Currency::where('id', $currency_id)->first();
				foreach ($jobs as $job) {
					$job->currency = $currency_name->currency;
					$response['result'][] = $job;
				}

				$response['message'] = "past jobs listed successful";
				$response['page'] = $offset;
				$response['next_page'] = $offset + 8;
				return response()->json($response, 200);
			}
		} elseif ($input['type'] == "driver") {
			$offset = $input['offset'];
			$driver_id = $input['user_id'];
			$jobs = DriverTrip::where('driver_id', $driver_id)->whereIn('status', [6, 8, 9])->orderBy('today_date', 'desc')->orderBy('pick_up_time', 'desc')->offset($offset)->limit(8)->get();
			if (count($jobs) == 0) {
				$response['result'] = [];
				$response['message'] = 'No record Found.';
				$response['page'] = 0;
				$response['next_page'] = 0;
				return response()->json($response, 200);
			} else {
				$currency_list = AppSetting::first();
				$currency_id = $currency_list->currency;
				$currency_name = Currency::where('id', $currency_id)->first();
				foreach ($jobs as $job) {
					$job->currency = $currency_name->currency;
					$response['result'][] = $job;
				}

				$response['message'] = "past jobs listed successful";
				$response['page'] = $offset;
				$response['next_page'] = $offset + 8;
				return response()->json($response, 200);
			}
		}
	}public function viewJobs(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'user_id' => 'required',
			'type' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.');
		}
		if ($input['type'] == "customer") {
			// $ratings = CustomerFeedback::select(DB::raw('AVG(driver_rating) as ratings'))->groupBy('customers_id')->get();
			// $ratings = array();
			// foreach ($ratings as $key) {
			// 	$ratings[$key->to_id] = $key->rating;
			// }
			$customer_id = $input['user_id'];
			$date = date('Y-m-d');
			$jobs = DriverTrip::where('cus_id', $customer_id)->where('today_date', $date)->get();
			if (count($jobs) == 0) {
				$message['code'] = 404;
				$message['error'] = 'No record Found.';
				$response['message'] = $message;
				return response()->json($response, 200);
			} else {
				$response['result'] = $jobs;
				$response['message'] = "Job listed successfully";
				return response()->json($response, 200);
			}
		} elseif ($input['type'] == "driver") {
			$driver_id = $input['user_id'];
			$date = date('Y-m-d');

			$jobs = DriverTrip::where('driver_id', $driver_id)->where('today_date', $date)->get();
			if (count($jobs) == 0) {
				$message['code'] = 404;
				$message['error'] = 'No record Found.';
				$response['message'] = $message;
				return response()->json($response, 200);
			} else {
				$response['result'] = $jobs;
				$response['message'] = "Job listed successfully";
				return response()->json($response, 200);
			}
		}
	}
	public function countriesList(Request $request) {

		$countries = Country::get();
		$response['result'] = $countries;
		$response['message'] = "success";
		return response()->json($response, 200);
	}
	public function stateList(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'country_id' => 'required',

		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.');
		}
		$state = State::where('country_id', $input['country_id'])->get();
		if (count($state) > 0) {
			$response['result'] = $state;
			$response['message'] = "success";
			return response()->json($response, 200);
		} else {
			$response['message'] = "failure";
			return response()->json($response, 200);
		}
	}

	protected function saveBookingInFirebase($input) {
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();
		$appsetting = AppSetting::first();
		$newpost = $database->getReference('unassigned/' . $input['booking_id'])->set($input);
	}
	public function get_booking_data(Request $request) {
		$input = $request->all();
		$booking_details = DriverTrip::where('id', $input['booking_id'])->first();
		$customer_details = Customer::where('id', $booking_details->cus_id)->first();
		$driver_details = Driver::where('id', $booking_details->driver_id)->first();

		$data['phone_number'] = $customer_details->phone_number;
		$data['customer_name'] = $customer_details->name;
		$data['customer_id'] = $booking_details->cus_id;
		$data['customer_email'] = $customer_details->email;
		$data['vehicle_id'] = $booking_details->vehicle_id;
		$data['pickup_location'] = $booking_details->pick_up;
		$data['drop_location'] = $booking_details->drop_location;
		$data['pickup_lat'] = $booking_details->pickup_lat;
		$data['pickup_lon'] = $booking_details->pickup_lon;
		$data['drop_lat'] = $booking_details->drop_lat;
		$data['drop_lon'] = $booking_details->drop_lon;
		$data['booking_id'] = $input['booking_id'];

		return json_encode($data);
	}

	public function automatic_assign(Request $request) {
		$input = $request->all();
		$booking_details = DriverTrip::where('id', $input['booking_id'])->first();
		$customer_details = Customer::where('id', $booking_details->cus_id)->first();
		$driver_details = Driver::where('id', $booking_details->driver_id)->first();

		$data['latitude'] = $booking_details->pickup_lat;
		$data['longitude'] = $booking_details->pickup_lon;
		$data['vehicle_id'] = $booking_details->vehicle_id;

		$driverList = $this->available_drivers($data);
		$input['booking_id'] = $booking_details->id;
		$input['vehicle_id'] = $booking_details->vehicle_id;
		$input['customer_id'] = $booking_details->cus_id;
		$input['customer_profile'] = "";
		$input['customer_name'] = $customer_details->name;
		$input['customer_lname'] = $customer_details->lname;
		$input['phone_num'] = $customer_details->phone_num;

		$input['pick_up'] = $booking_details->pick_up;
		$input['drop_location'] = $booking_details->drop_location;
		$input['pickup_lat'] = $booking_details->pickup_lat;
		$input['pickup_lon'] = $booking_details->pickup_lon;
		$input['drop_lat'] = $booking_details->drop_lat;
		$input['drop_lon'] = $booking_details->drop_lon;
		$input['CreatedTime'] = date('d/m/y H:i:s');
		$input['RequestFrom'] = "Admin";
		$input['RequestFromOtp'] = 1;

		$customer = Customer::find($booking_details->cus_id);
		if (count($driverList) > 0) {
			$input['customer_avatar'] = ($customer->avatar != "" ? $customer->avatar : "");
			$email = $customer->email;
			$name = $customer->name;
			$social_url = DB::table('settings')->first();
			$skype = $social_url->skype;
			$facebook = $social_url->facebook;
			$twitter = $social_url->twitter;
			$app_name = env("APP_NAME");
			$from_mail = env("MAIL_USERNAME");
			$website = $social_url->website_url;
			$email_to = $social_url->mail_to;
			$logo = $social_url->logo_url;
			$admin_mail = $social_url->email;
			$content = Email::select('content')->where('template_name', '=', 'Customer Trip Save')->first();
			$content = str_replace("{{env('APP_NAME')}}", $app_name, $content->content);

			$mail_header = array("name" => $name, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content);

			$response['message'] = "Request sent";

			$this->saveFirebase($input, $driverList);
			$this->deleteFirebase($input['booking_id']);

		} else {
			$response['code'] = 200;
			$response['message'] = "Driver is not available.";
		}

		return $response;
	}

	public function deleteFirebase($booking_id) {
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();

		$newpost = $database->getReference('unassigned/' . $booking_id)->remove();
	}

	public function cusTimeoutCancel(Request $request) {
		$setting = DB::table('app_settings')->select('time_period')->where('id', 1)->first();
		$input = $request->all();
		$validator = Validator::make($input, [
			'booking_id' => 'required',
			'customer_id' => 'required',
			'pick_up_time' => 'required',
		]);

		if ($validator->fails()) {
			return $this->sendError('Validation Error.' . $validator->errors());
		}
		//$date = $input['date'];
		$booking_id = $input['booking_id'];
		$customer_id = $input['customer_id'];
		$time = strtotime($input['pick_up_time']);

		$can_time = strtotime($input['pick_up_time']) + $setting->time_period;
		sleep($setting->time_period);
		$cur_time = strtotime(date("H:i:s"));

		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();

		if ($cur_time >= $can_time) {
			$driver_id = DB::table('driver_trips')->select('driver_id')->where('id', $booking_id)->first();
			if ($driver_id->driver_id == '' || $driver_id->driver_id == NULL) {
				$update1 = ['customer_trips/' . $customer_id . '/Status' => 0, 'customer_trips/' . $customer_id . '/Title' => ''];
				$newpost = $database->getReference()->update($update1);

				$stat_upd = $database->getReference('drivers_trips');
				$stat_up = $stat_upd->getValue();

				foreach ($stat_up as $k => $driver) {
					$status_update = $database->getReference('drivers_trips' . '/' . $k)->getValue();

					if ($status_update['BookingId'] == $booking_id) {
						//print_r($k);
						$update2 = [
							'drivers_trips/' . $k . '/Status' => '0',
							'drivers_trips/' . $k . '/Title' => '',
						];
						$st_up = $database->getReference()->update($update2);
					}

				}

				$response['message'] = "success";
			} else {
				$response['message'] = "failure";
			}

		} else {
			$response['message'] = "failure";
		}
		return response()->json($response, 200);
	}

	public function charge(Request $request) {
		$response = \App\Http\Authorize::chargeCustomerProfile(1512111009, 1512246738, 1.5);
		\Log::info(json_encode($response));
		return response()->json($response, 200);
	}
	public function getProfile(Request $request) {
		$response = \App\Http\Authorize::getCustomerProfile(1511767855);
		\Log::info(json_encode($response));
		return response()->json($response, 200);
	}
	public function refund(Request $request) {
		$response = \App\Http\Authorize::refundTransaction("XXXX1111", "XXXX", 2, "40049523347");
		\Log::info(json_encode($response));
		return response()->json($response, 200);
	}
	public function addTrip(Request $request) {

		$input = $request->all();
		$ctime = Carbon::now();
		$ctime->toTimeString();
		if ($input['mode'] == 'ridenow') {
			$validator = Validator::make($input, [
				'customer_id' => 'required',
				'pick_up' => 'required',
				'pickup_lat' => 'required',
				'pickup_lon' => 'required',
				'drop_location' => 'required',
				'drop_lat' => 'required',
				'drop_lon' => 'required',
				'mode' => 'required',
				'vehicle_id' => 'required',
			]);

			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}

			if ($input['customer_id'] != '0') {
				$customer = Customer::find($input['customer_id']);

				if ($customer->status != 1) {
					$response['message'] = "Your account is blocked,kindly contact Admin";
					return $response;
				}
			} else {
				$customer = new Customer();
				$customer->phone_number = isset($input['phone_num']) ? $input['phone_num'] : '0';
				$customer->email = isset($input['customer_email']) ? $input['customer_email'] : '0';
				$customer->name = isset($input['customer_name']) ? $input['customer_name'] : '0';
				$customer->member_type = 'guest';
				$customer->status = '1';
				/*if($input['booking_id'] != 0){
						$customer->update();
					}else{
				*/
				$customer->save();

				$customer = Customer::find($customer->id);
			}
			
			$total_distance = $this->getDistanceBetweenTwoLocations($input['pickup_lat'], $input['pickup_lon'], $input['drop_lat'], $input['drop_lon'], "Km");
			$input['estimated_distance'] = round($total_distance, 3);
			info($input['estimated_distance']);
			$vehicaleCategory = VehicleCategory::where('id', $input['vehicle_id'])->first();
			$vehicle_type = $vehicaleCategory->vehicle_type;
			$base = VehicleCategory::where('vehicle_type', $vehicle_type)->first();
			$base_fare = $base->base_fare;
			info($base_fare);
			$price_per_km = $base->price_per_km;
			info($price_per_km);
			$total_cost = $input['estimated_distance'] * $price_per_km;
			info($total_cost);
			$input['advance_amount'] = $total_cost + $base_fare;
			info($input['advance_amount']);

			$paymentResponse = \App\Http\Authorize::chargeCustomerProfile($customer->customerProfileId, $customer->customerPaymentProfileId, $input['advance_amount']);
			info($paymentResponse);
			if ($paymentResponse != null) {
				if ($paymentResponse['resultCode'] == "Ok") {
					$tresponse = $paymentResponse['transaction'];
					if ($tresponse != null) {
						$input['advance_transaction_id'] = $tresponse['transId'];
						$input['payment_status'] = "1";
						$input['payment_name'] = "authorize";
					}
					else {
						info("1");
						$response['code'] = 500;
						$response['message'] = 'failure';
						return response()->json($response, 200);
					}
				}
				else {
					info("2");
					$response['code'] = 500;
					$response['message'] = 'failure';
					return response()->json($response, 200);
				}
			}
			else {
				info("3");
				$response['code'] = 500;
				$response['message'] = 'failure';
				return response()->json($response, 200);
			}
			$input['trip_num'] = str_random(4);
			$input['cus_id'] = $input['customer_id'];
			$input['customer_name'] = $customer->name;
			$input['customer_lname'] = $customer->last_name;
			$input['customer_email'] = $customer->email;
			$input['customer_phone_number'] = $customer->country . $customer->phone_number;
			$input['today_date'] = $ctime->toDateString();
			$input['phone_num'] = $customer->phone_number;

			$input['added_by'] = isset($input['added_by']) ? $input['added_by'] : '0';
			$input['send_OTP'] = isset($input['send_OTP']) ? $input['send_OTP'] : '1';
			if ($input['added_by'] != '' && $input['added_by'] != '0') {
				$input['RequestFrom'] = 'Admin';
				$input['RequestFromOtp'] = $input['send_OTP'];
			} else {
				$input['RequestFrom'] = 'Normal';
				$input['RequestFromOtp'] = 1;
			}

			//$input['request_time'] = $ctime->toTimeString();
			$input['pick_up_time'] = $ctime->toTimeString();
			$input['status'] = 1; // Service request waiting
			$input['cancel_service_status'] = 0;

			$service = VehicleCategory::where('id', $input['vehicle_id'])->first();
			$input['vehicle_type'] = $service->vehicle_type;
			$input['price_km'] = number_format($service->price_per_km, 2);
			$input['customer_num'] = rand(0000, 9999);

			$digits = 5;
			$input['trip_num'] = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

			if (isset($input['booking_id']) && $input['booking_id'] != 0) {
				//unset($input['customer_email']);
				//unset($input['customer_id']);
				DriverTrip::where('id', $input['booking_id'])->update(['cus_id' => $input['customer_id'], 'today_date' => $input['today_date'], 'pick_up' => $input['pick_up'], 'pick_up_time' => $input['pick_up_time'], 'drop_location' => $input['drop_location'], 'vehicle_type' => $input['vehicle_type'], 'drop_lat' => $input['drop_lat'], 'drop_lon' => $input['drop_lon'], 'pickup_lat' => $input['pickup_lat'], 'pickup_lon' => $input['pickup_lon']]);

				$id = DriverTrip::where('id', $input['booking_id'])->first();
				//$id = $input['booking_id'];
			} else {
				$id = DriverTrip::create($input);
			}

			$input['booking_id'] = $id->id;

			$customer->user_photo = env('IMG_URL') . $customer->user_photo; // . 'storage/'    removed
			$trimmed = str_replace('/public', '', $customer->user_photo);
			$input['customer_profile'] = $trimmed;

			if (is_object($id)) {
				$response = [
					'result' => $input,
					'message' => "Request sent",
				];
				$data['latitude'] = $input['pickup_lat'];
				$data['longitude'] = $input['pickup_lon'];
				$data['vehicle_id'] = $input['vehicle_id'];
				if (isset($input['favorite_driver_id']) && !empty($input['favorite_driver_id'])) {
					$id = explode("_", $input['favorite_driver_id'])[1];
					$data['favorite_driver_id'] = $id;
				}
				// $driverList = $this->driverList($data);
				$driverList = $this->available_drivers($data);
				if (count($driverList) > 0) {
					$input['customer_avatar'] = ($customer->avatar != "" ? $customer->avatar : "");
					/*$email = $customer->email;
						$name = $customer->name;
						$social_url=DB::table('settings')->first();
						$skype=$social_url->skype;
						$facebook=$social_url->facebook;
						$twitter=$social_url->twitter;
						$app_name=env("APP_NAME");
						$from_mail=env("MAIL_USERNAME");
						$website=$social_url->website_url;
						$email_to=$social_url->mail_to;
						$logo=$social_url->logo_url;
						$admin_mail=$social_url->email;
						$content=Email::select('content')->where('template_name', '=', 'Customer Trip Save')->first();
						$content=str_replace("{{env('APP_NAME')}}", $app_name, $content->content);

					*/

					$response['message'] = "Request sent";
					/// Save data to firebase ///
					$this->saveFirebase($input, $driverList);

					$this->deleteFirebase($input['booking_id']);

					/*Mail::send('mails.customerTripSave', $mail_header, function ($message)
						 use ($email,$from_mail, $app_name) {
							$message->from($from_mail, $app_name);
							$message->subject('Trip Save');
							$message->to($email);
						});*/

				} else {
					// if($input['booking_id'] == 0){
					$this->saveBookingInFirebase($input);
					// }

					$response['code'] = 200;
					$response['message'] = "Driver is not available";
				}

			} else {
				$response['code'] = 500;
				$response['message'] = 'failure';
			}
			return response()->json($response, 200);
		}if ($input['mode'] == 'ridelater') {
			$validator = Validator::make($input, [
				'customer_id' => 'required',
				//'service_id' => 'required',
				'pick_up' => 'required',
				'pickup_lat' => 'required',
				'pickup_lon' => 'required',
				'drop_location' => 'required',
				'drop_lat' => 'required',
				'drop_lon' => 'required',
				'mode' => 'required',
				'date' => 'required',
				//'vehicle_type' => 'required',
				'vehicle_id' => 'required',
				'pick_up_time' => 'required',

			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}

			$input['ride_date'] = date('Y-m-d', strtotime($input['date']));
			$pick_up_time = strtotime($input['ride_date'] . ' ' . $input['pick_up_time']);
			$curre_time = strtotime(date('Y-m-d H:i:s')) + (20 * 60);

			if ($pick_up_time <= $curre_time) {
				$response['result'] = null;
				$response['message'] = 'Ride Time 20 mins';
				return response()->json($response, 200);
			}
			$input['added_by'] = isset($input['added_by']) ? $input['added_by'] : '0';
			$input['send_OTP'] = isset($input['send_OTP']) ? $input['send_OTP'] : '1';
			if ($input['added_by'] != '' && $input['added_by'] != '0') {
				$input['RequestFrom'] = 'Admin';
				$input['RequestFromOtp'] = $input['send_OTP'];
			} else {
				$input['RequestFrom'] = 'Normal';
				$input['RequestFromOtp'] = '1';
			}

			if (isset($input['driver_id'])) {
				$input['driver_id'] = '';
			}

			$customer = Customer::find($input['customer_id']);
			$input['trip_num'] = str_random(4);
			$input['cus_id'] = $input['customer_id'];
			$input['customer_name'] = $customer->name;
			$input['customer_lname'] = $customer->last_name;
			$input['customer_email'] = $customer->email;
			$new_time = date('H:i:s', strtotime($_REQUEST['pick_up_time']));
			$input['ride_time'] = $input['date'] . ' ' . date('H:i:s', strtotime('-5 minutes', strtotime($new_time)));
			$input['customer_phone_number'] = $customer->country . $customer->phone_number;
			//$input['today_date'] = $ctime->toDateString();
			$input['phone_num'] = $customer->phone_number;
			//$input['request_time'] = $ctime->toTimeString();
			$input['pick_up_time'] = $_REQUEST['pick_up_time'];

			$input['pick_time'] = $new_time;
			$input['ride_date'] = date('Y-m-d', strtotime($input['date']));
			$ctime = Carbon::now();
			$input['today_date'] = date('Y-m-d', strtotime($input['date']));
			$input['status'] = 0; // Service request waiting
			$input['cancel_service_status'] = 0;

			$service = VehicleCategory::where('id', $input['vehicle_id'])->first();
			$input['vehicle_type'] = $service->vehicle_type;
			$input['price_km'] = number_format($service->price_per_km, 2);
			$input['customer_num'] = rand(0000, 9999);

			$digits = 5;
			$input['trip_num'] = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
			$id = DriverTrip::create($input);
			$input['booking_id'] = $id->id;

			$customer->user_photo = env('IMG_URL') . $customer->user_photo; //. 'storage/'
			$trimmed = str_replace('/public', '', $customer->user_photo);
			$input['customer_profile'] = $trimmed;

			if (is_object($id)) {
				$response = [
					'result' => $input,
					'message' => "Request saved successfully.",
				];
				$data['latitude'] = $input['pickup_lat'];
				$data['longitude'] = $input['pickup_lon'];
				$data['vehicle_type'] = $input['vehicle_type'];
				// $driverList = $this->driverList($data);
				// if (count($driverList) > 0) {
				// 	$input['customer_avatar'] = ($customer->avatar != "" ? $customer->avatar : "");
				/*$email = $customer->email;
				$name = $customer->name;
				$social_url=DB::table('settings')->first();
				$skype=$social_url->skype;
				$facebook=$social_url->facebook;
				$twitter=$social_url->twitter;
				$app_name=env("APP_NAME");
				$from_mail=env("MAIL_USERNAME");
				$website=$social_url->website_url;
				$email_to=$social_url->mail_to;
				$logo=$social_url->logo_url;
				$admin_mail=$social_url->email;
				$content=Email::select('content')->where('template_name', '=', 'Customer Trip Save')->first();
				$content=str_replace("{{env('APP_NAME')}}", $app_name, $content->content);

				$mail_header = array("name" => $name,'skype'=>$skype,'facebook'=>$facebook,'twitter'=>$twitter,'website'=>$website,'email_to'=>$email_to,'logo'=>$logo,'app_name'=>$app_name,'admin_mail'=>$admin_mail,'content'=>$content);
				Mail::send('mails.customerTripSave', $mail_header, function ($message)
					 use ($email,$from_mail, $app_name) {
						$message->from($from_mail, $app_name);
						$message->subject('Trip Save');
						$message->to($email);
					});*/

				$response['message'] = "Request saved successfully";
				// 	/// Save data to firebase ///
				//$this->scheduleSaveFirebase($input);

				// } else {
				// 	$response['code'] = 200;
				// 	$response['message'] = "No Drivers available.";
				// }

			} else {
				$response['code'] = 500;
				$response['message'] = 'Trip request not saved.';
			}
			return response()->json($response, 200);
		}if ($input['mode'] == 'editRideLater') {
			$validator = Validator::make($input, [
				'customer_id' => 'required',
				//'service_id' => 'required',
				'booking_id' => 'required',
			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			$pick_up_time = strtotime($input['pick_up_time']);
			$curre_time = strtotime(date('H:i:s')) + (20 * 60);
			if ($pick_up_time <= $curre_time) {
				$response['result'] = null;
				$response['message'] = 'Ride Time 20 mins';
				return response()->json($response, 200);
			}
			//$trips=DriverTrip::where('booking_id',$input['booking_id'])->first();
			$trips = DriverTrip::where('id', $input['booking_id'])->first();
			if (is_object($trips)) {
				$trips->pick_up = isset($input['pick_up']) ? $input['pick_up'] : $trips->pick_up;
				$trips->pick_up_time = isset($input['pick_up_time']) ? $input['pick_up_time'] : $trips->pick_up_time;
				$trips->drop_location = isset($input['drop_location']) ? $input['drop_location'] : $trips->drop_location;
				$trips->vehicle_type = isset($input['vehicle_type']) ? $input['vehicle_type'] : $trips->vehicle_type;
				$trips->vehicle_id = isset($input['vehicle_id']) ? $input['vehicle_id'] : $trips->vehicle_id;
				$trips->pickup_lat = isset($input['pickup_lat']) ? $input['pickup_lat'] : $trips->pickup_lat;
				$trips->pickup_lon = isset($input['pickup_lon']) ? $input['pickup_lon'] : $trips->pickup_lon;
				$trips->drop_lat = isset($input['drop_lat']) ? $input['drop_lat'] : $trips->drop_lat;
				$trips->drop_lon = isset($input['drop_lon']) ? $input['drop_lon'] : $trips->drop_lon;
				$ctime = Carbon::now();
				$trips->today_date = isset($input['date']) ? date('Y-m-d', strtotime($input['date'])) : $trips->today_date;
				$trips->ride_date = isset($input['date']) ? date('Y-m-d', strtotime($input['date'])) : $trips->ride_date;
				$trips->update();
				$data['customer_id'] = $trips->cus_id;
				$data['service_status'] = 1;
				$data['pick_up'] = $trips->pick_up;
				$data['pick_up_time'] = $trips->pick_up_time;
				$new_time = strtotime($trips->pick_up_time);
				$new_time = date('H:i:s', $new_time);
				$data['pick_time'] = $new_time;
				$data['drop_location'] = $trips->drop_location;
				$data['vehicle_type'] = $trips->vehicle_type;
				$data['pickup_lat'] = $trips->pickup_lat;
				$data['pickup_lon'] = $trips->pickup_lon;
				$data['drop_lat'] = $trips->drop_lat;
				$data['drop_lon'] = $trips->drop_lon;
				$data['booking_id'] = $trips->id;
				$data['today_date'] = $trips->today_date;
				$data['ride_date'] = $trips->ride_date;
				$data['ride_time'] = $trips->ride_date . ' ' . date('H:i:s', strtotime('-5 minutes', strtotime($trips->pick_time)));
				$this->updateFirebase($data);
				$response['result'] = $data;
				$response['message'] = "request updated";
			} else {
				$response['code'] = 500;
				$response['message'] = 'No Trips Found';
				return $response;
			}
			// $customer = Customer::find($input['customer_id']);
			// $input['trip_num'] = str_random(4);
			// $input['cus_id'] = $input['customer_id'];
			// $input['customer_name'] = $customer->name;
			// $input['customer_lname'] = $customer->last_name;
			// $input['customer_email'] = $customer->email;
			// $input['customer_phone_number'] = $customer->country . $customer->phone_number;
			// $input['today_date'] = $ctime->toDateString();
			// $input['phone_num'] = $customer->phone_number;
			// //$input['request_time'] = $ctime->toTimeString();
			// $input['pick_up_time'] =$_REQUEST['pick_up_time'];
			// $input['date']=$_REQUEST['date'];
			// $input['status'] = 1; // Service request waiting
			// $input['cancel_service_status'] = 0;

			// $service = VehicleCategory::where('vehicle_type', $input['vehicle_type'])->first();
			// //$input['vehicle_type'] = $service->vehicle_type;
			// $input['price_km'] = $service->price_per_km;
			// $input['customer_num'] = rand(0000, 9999);

			// $digits = 5;
			// $input['trip_num'] = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
			// $id = DriverTrip::create($input);
			// $input['booking_id'] = $id->id;
			// $input['customer_profile'] = $customer->user_photo;

			// if (is_object($id)) {
			// 	$response = [
			// 		'result' => $input,
			// 		'message' => "Request saved successfully.",
			// 	];
			// 	$data['latitude'] = $input['pickup_lat'];
			// 	$data['longitude'] = $input['pickup_lon'];
			// 	$data['vehicle_type'] = $input['vehicle_type'];
			// 	// $driverList = $this->driverList($data);
			// 	// if (count($driverList) > 0) {
			// 	// 	$input['customer_avatar'] = ($customer->avatar != "" ? $customer->avatar : "");
			// 		$email = $customer->email;
			// 		$name = $customer->name;
			// 		$mail_header = array("name" => $name);
			// 		Mail::send('mails.customerTripSave', $mail_header, function ($message)
			// 			 use ($email) {
			// 				$message->from('pudgybuddyteam@gmail.com', 'SpotnRides');
			// 				$message->subject('Trip Save');
			// 				$message->to($email);
			// 			});

			// 		$response['message'] = "Request saved successfully";
			// 	// 	/// Save data to firebase ///
			// 		$this->scheduleSaveFirebase($input);

			// 	// } else {
			// 	// 	$response['code'] = 200;
			// 	// 	$response['message'] = "No Drivers available.";
			// 	// }

			// } else {
			// 	$response['code'] = 500;
			// 	$response['message'] = 'Trip request not saved.';
			// }
			return response()->json($response, 200);
		} elseif ($input['mode'] == 'accept') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
				'driver_id' => 'required',
			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			//	$job = DriverTrip::where('booking_id', $input['booking_id'])->first();
			$job = DriverTrip::where('id', $input['booking_id'])->first();
			if (!is_object($job)) {
				$response['message'] = 'failure';
				return $response;
			}
			if ($job->status == 2) {
				$response['message'] = 'Trip Already Accepted';
				return $response;
			}
			$job->pick_up_time = $ctime->toTimeString();
			$job->status = 2;
			$job->driver_id = $input['driver_id'];

			$driver = Driver::find($input['driver_id']);
			$job->driver_name = $driver->name;
			$job->driver_lname = $driver->last_name;
			// $input['driver_email'] = $driver->email;
			// $input['driver_phone_number'] = $driver->country_name . $driver->phone_number;

			$checkin = DriverCheckin::where('driver_id', $input['driver_id'])->first();
			$checkin->booking_status = 1;
			$checkin->update();
			if ($job->save()) {
				$customer = Customer::where('id', $job->cus_id)->first();

				$response = [
					'result' => $input,
					//	'customer' => $customer,
					'message' => "Trip Accept",
				];
				////  Trip Confirmation Mail to customer
				$username = Customer::select('name', 'email')->where('id', '=', $job->cus_id)->first();
				$useremail = $username->email;
				$name = $username->name;
				//$otp = $username->otp;
				$social_url = DB::table('settings')->first();
				$skype = $social_url->skype;
				$facebook = $social_url->facebook;
				$twitter = $social_url->twitter;
				$app_name = env("APP_NAME");
				$from_mail = env("MAIL_USERNAME");
				$website = $social_url->website_url;
				$email_to = $social_url->mail_to;
				$logo = $social_url->logo_url;
				$admin_mail = $social_url->email;
				/*$content=Email::select('content')->where('template_name', '=', 'Customer Trip Confirmation')->first();

					$content=str_replace("{{env('APP_NAME')}}", $app_name, $content->content);
					$mail_header = array("name" => $name,'skype'=>$skype,'facebook'=>$facebook,'twitter'=>$twitter,'website'=>$website,'email_to'=>$email_to,'logo'=>$logo,'app_name'=>$app_name,'admin_mail'=>$admin_mail,'content'=>$content);
					Mail::send('mails.customerTripConfirm', $mail_header, function ($message)
						 use ($useremail,$from_mail, $app_name) {
							$message->from($from_mail, $app_name);
							$message->subject('Trip Confirm');
							$message->to($useremail);

				*/
				//

				///// driver email Trip notification details
				$username1 = Driver::where('id', '=', $job->driver_id)->first();

				$driveremail = $username1->email;
				$name = $username1->name;
				$customer_id = $job->cus_id;
				$customer_name = $job->customer_name;
				$driver_id = $job->driver_id;
				$driver_name = $username1->name;

				$booking_id = $job->id;
				$content = Email::select('content')->where('template_name', '=', 'Driver Trip Notification')->first();
				$content = str_replace("{{env('APP_NAME')}}", $app_name, $content->content);

				$mail_header = array("name" => $name, 'customer_id' => $customer_id, 'customer_name' => $customer_name, 'driver_id' => $driver_id, 'driver_name' => $driver_name, 'booking_id' => $job->id, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content);

				Mail::send('mails.driverTripNotification', $mail_header, function ($message)
					 use ($driveremail, $from_mail, $app_name) {
						$message->from($from_mail, $app_name);
						$message->subject('Trip Notification');
						$message->to($driveremail);
					});
				////customer trip Notification Details
				$content = Email::select('content')->where('template_name', '=', 'Customer Trip Notification')->first();
				$content = str_replace("{{env('APP_NAME')}}", $app_name, $content->content);
				$mail_header = array("customer_name" => $customer_name, 'customer_id' => $customer_id, 'customer_name' => $customer_name, 'driver_id' => $driver_id, 'driver_name' => $driver_name, 'booking_id' => $job->id, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content);
				Mail::send('mails.customerTripNotification', $mail_header, function ($message)
					 use ($useremail, $from_mail, $app_name) {
						$message->from($from_mail, $app_name);
						$message->subject('Trip Notification');
						$message->to($useremail);

					});
				/////

				$data['booking_id'] = $job->id;
				$data['start_time'] = $ctime->toTimeString();
				$data['driver_id'] = $input['driver_id'];
				$data['customer_id'] = $job->cus_id;
				$data['service_status'] = 2;
				$data['driver_name'] = $username1->name;
				$data['driver_lname'] = $username1->last_name;

				$username1->photo = env('IMG_URL') . $username1->photo;
				$trimmed = str_replace('/public', '', $username1->photo);
				$data['profile_pic'] = $trimmed;

				$data['category_type'] = $username1->vehicle_type;
				$data['category_id'] = $username1->vehicle_id;
				$data['otp'] = $job->trip_num;
				$data['vehicle_number'] = $username1->vehicle_num;
				$data['phone'] = $username1->phone_number;
				$rating = DB::table('customer_feedbacks')
					->where('driver_id', $input['driver_id'])
					->avg('driver_rating');
				$avg_rating = round($rating, 1);
				$data['rating'] = $avg_rating;
				$this->updateFirebase($data);
				return response()->json($response, 200);
			} else {
				//$message['code'] = 500;
				//$message['error'] = 'Not saved.';
				$response['message'] = 'Not saved';
				return response()->json($response, 200);
			}
		} elseif ($input['mode'] == 'request_time') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
				'status' => 'required',
			]);

			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			//$job = DriverTrip::where('booking_id', $input['booking_id'])->first();
			$job = DriverTrip::where('id', $input['booking_id'])->first();
			if (!is_object($job)) {
				$response['message'] = "trip not found";
				return $response;
			}
			if ($input['status'] == 0) {
				$job->status = 7;
				if ($job->save()) {
					$driver = Driver::where('id', $job->driver_id)->first();
					$response = [
						'result' => $input,
						//	'driver' => $driver,
						'message' => "Request updated",
					];

					// $data['booking_id'] = $job->id;
					// //$data['driver_id'] = $input['driver_id'];
					// $data['customer_id'] = $job->cus_id;
					// $data['service_status'] = 7;
					// $this->updateFirebase($data);
					return response()->json($response, 200);
				} else {
					//	$message['code'] = 500;

					$data['booking_id'] = $job->id;
					$data['vehicle_id'] = $job->vehicle_id;
					$this->TimeoutUpdate($data);
					$message['error'] = 'Not saved.';

					$response['message'] = $message;
					return response()->json($response, 200);
				}
			} else {
				$response = [
					'result' => $input,
					'message' => "Request updated",
				];
				return response()->json($response, 200);
			}
		} elseif ($input['mode'] == 'start') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
				'driver_id' => 'required',
				'otp' => 'required',
			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			//	$job = DriverTrip::where('booking_id', $input['booking_id'])->first();
			$job = DriverTrip::where('id', $input['booking_id'])->first();
			if (!is_object($job)) {
				$response['message'] = "trip not found";
				return $response;
			}
			if ($input['otp'] != $job->trip_num) {
				$response['message'] = "otp mismatch";
				return $response;
			}
			$job->pick_up_time = $ctime->toTimeString();
			$job->driver_wait_end_time = $ctime;
			$job->status = 3;
			if ($job->save()) {
				$driver = Driver::where('id', $job->driver_id)->first();
				$response = [
					'result' => $input,
					//'driver' => $driver,
					'message' => "Request saved successfully.",
				];
				$data['booking_id'] = $job->id;
				$data['start_time'] = $ctime->toTimeString();
				$data['driver_id'] = $input['driver_id'];
				$data['customer_id'] = $job->cus_id;
				$data['service_status'] = 3;
				$this->updateFirebase($data);
				return response()->json($response, 200);
			} else {
				$message['code'] = 500;
				$message['error'] = 'Not saved.';
				$response['message'] = $message;
				return response()->json($response, 200);
			}
		} elseif ($input['mode'] == 'end') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
				'driver_id' => 'required',
				// 'drop_lat' => 'required',
				// 'drop_lon' => 'required',
				// 'total_distance' => 'required',
				// 'dest_address'=>'required'

			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			$job = DriverTrip::where('id', $input['booking_id'])->first();
			//$job = DriverTrip::where('booking_id', $input['booking_id'])->first();
			//$job->pick_up_time = $ctime->toTimeString();
			$job->drop_lat = isset($input['drop_lat']) ? $input['drop_lat'] : $job->drop_lat;
			$job->drop_lon = isset($input['drop_lon']) ? $input['drop_lon'] : $job->drop_lon;

			$job->drop_off_time = $ctime;
			
			$job->drop_location = isset($input['drop_location']) ? $input['drop_location'] : $job->drop_location;
			$job->status = 4;
			$total_distance = isset($input['total_distance']) ? $input['total_distance'] : 0;
			$job->total_distance = round($total_distance, 3);
			
			$vehicle_type = $job->vehicle_type;
			$base = VehicleCategory::where('vehicle_type', $vehicle_type)->first();
			$base_fare = $base->base_fare;
			$price_per_km = $job->price_km;

			if ($job->save()) {
				$total_amount = $this->getTripFare($job->id);
				$job->total_amount = round($total_amount, 3);
				$job->commission = $this->getCommission($job->total_amount);
				$job->save();
				$response = [
					'result' => $input,
					'message' => "Trip Completed",
				];
				$data['booking_id'] = $job->id;
				$data['reach_time'] = $ctime->toTimeString();
				$data['driver_id'] = $input['driver_id'];
				$data['customer_id'] = $job->cus_id;
				$data['service_status'] = 4;
				$data['total_amount'] = $total_amount;
				$data['base_fare'] = $base_fare;
				$data['price_per_km'] = $price_per_km;
				$data['total_distance'] = round($input['total_distance'], 3);
				$data['drop_location'] = $job->drop_location;
				$data['vehicle_type'] = $vehicle_type;
				$data['distance'] = isset($input['distance']) ? $input['distance'] : 0;
				$this->updateFirebase($data);
				return response()->json($response, 200);
			} else {

				$response['message'] = 'failure';
				return response()->json($response, 200);
			}
		} elseif ($input['mode'] == 'complete') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'driver_id' => 'required',
				'customer_id' => 'required',
				'booking_id' => 'required',
				'payment_type' => 'required',
			]);

			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			$customer = Customer::find($input['customer_id']);
			$job = DriverTrip::where('id', $input['booking_id'])->first();
			//$job = DriverTrip::where('booking_id', $input['booking_id'])->first();

			if ($input['payment_type'] == 'cash') {
				$job->payment_status = "1";
				$job->payment_name = "cash";
				$job->status = 6;
				$total_amount = $job->total_amount;
				$commission_percent = VehicleCategory::where('id', $job->vehicle_id)->first();
				$commission_percentage = $commission_percent->commission_percentage;
				$commission = $total_amount * $commission_percentage;
				$total_commission = $commission / 100;
				$job->commission = $total_commission;
				$driver = Driver::where('id', $input['driver_id'])->first();
				$driver->wallet = $driver->wallet - $total_commission;
				$driver->save();
				$job->save();
			} elseif ($input['payment_type'] == 'paypal') {
				$job->payment_status = "1";
				$job->payment_name = "paypal";
				$job->status = 6;
				$job->paypal_id = $input['paypal_id'];
				$total_amount = $job->total_amount;
				$commission_percent = VehicleCategory::where('id', $job->vehicle_id)->first();
				$commission_percentage = $commission_percent->commission_percentage;
				$commission = $total_amount * $commission_percentage;
				$total_commission = $commission / 100;
				$job->commission = $total_commission;
				$total = $total_amount - $total_commission;
				$driver = Driver::where('id', $input['driver_id'])->first();
				$driver->wallet = $driver->wallet + $total;
				$driver->save();
				$job->save();
			} elseif ($input['payment_type'] == 'authorize') {
				if($job->advance_amount < $job->total_amount){
					$amountToCut = $job->total_amount - $job->advance_amount;
					info("amountToCut");
					info($amountToCut);
					$response = \App\Http\Authorize::chargeCustomerProfile($customer->customerProfileId, $customer->customerPaymentProfileId, (string)$amountToCut);
					if ($response != null) {
						if ($response['resultCode'] == "Ok") {
							$tresponse = $response['transaction'];
							if ($tresponse != null) {
								$job->transaction_id = $tresponse['transId'];
							} 
						}
					}
				}
				else if($job->advance_amount - $job->total_amount > 0){
					$refund = new \App\RefundTransaction();
					$refund->trip_id = $job->id;
					$refund->amount = $job->advance_amount - $job->total_amount;
					$refund->save();
				}
			}
			$job->status = 6;
			$total_amount = $job->total_amount;
			$total_commission = $job->commission;
			$total = $total_amount - $total_commission;
			$driver = Driver::where('id', $input['driver_id'])->first();
			$driver->wallet = $driver->wallet + $total;
			$driver->save();
			$job->drop_time = $ctime;
			if ($job->save()) {
				$drivercheck = DriverCheckin::where('driver_id', $input['driver_id'])->first();
				$drivercheck->booking_status = 0;
				$drivercheck->save();
				$response = [
					'result' => $input,
					'message' => "Payment Success",
				];
				//	$data['total_distance'] = $input['total_distance'];
				$data['total_amount'] = $job->total_amount;
				$data['payment_name'] = $job->payment_name;
				$data['driver_id'] = $input['driver_id'];
				$data['customer_id'] = $job->cus_id;
				$data['service_status'] = 6;
				$data['drop_time'] = $ctime->toTimeString();
				$this->updateFirebase($data);
				/////customer Trip Complete Email
				$username = Customer::select('name', 'email')->where('id', '=', $job->cus_id)->first();
				$usernameDriver = Driver::select('name', 'email')->where('id', '=', $job->driver_id)->first();
				$useremail = $username->email;
				$name = $username->name;
				$customer_id = $job->cus_id;
				$driver_id = $job->driver_id;
				$driver_name = $usernameDriver->name;
				$booking_id = $job->id;
				$total = round($job->total_amount, 2);

				$social_url = DB::table('settings')->first();
				$skype = $social_url->skype;
				$facebook = $social_url->facebook;
				$twitter = $social_url->twitter;
				$app_name = env("APP_NAME");
				$from_mail = env("MAIL_USERNAME");
				$website = $social_url->website_url;
				$email_to = $social_url->mail_to;
				$logo = $social_url->logo_url;
				$admin_mail = $social_url->email;
				$content = Email::select('content')->where('template_name', '=', 'Customer Trip Complete')->first();
				$content = str_replace("{{env('APP_NAME')}}", $app_name, $content->content);
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				$mail_header = array("name" => $name, 'customer_id' => $customer_id, 'driver_id' => $driver_id, 'driver_name' => $driver_name, 'booking_id' => $booking_id, 'total' => round($job->total_amount, 2), 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content, 'currency' => $currency->symbol);
				Mail::send('mails.customerTripComplete', $mail_header, function ($message)
					 use ($useremail, $from_mail, $app_name) {
						$message->from($from_mail, $app_name);
						$message->subject('Trip Complete');
						$message->to($useremail);

					});
				/////
				/////////Driver Trip Complete
				$social_url = DB::table('settings')->first();
				$skype = $social_url->skype;
				$facebook = $social_url->facebook;
				$twitter = $social_url->twitter;
				$app_name = env("APP_NAME");
				$from_mail = env("MAIL_USERNAME");
				$website = $social_url->website_url;
				$email_to = $social_url->mail_to;
				$logo = $social_url->logo_url;
				$admin_mail = $social_url->email;
				$driveremail = $usernameDriver->email;
				$content = Email::select('content')->where('template_name', '=', 'Driver Trip Complete')->first();
				$content = str_replace("{{env('APP_NAME')}}", $app_name, $content->content);
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				$mail_header = array("driver_name" => $driver_name, 'customer_name' => $name, 'customer_id' => $customer_id, 'driver_id' => $driver_id, 'driver_name' => $driver_name, 'booking_id' => $booking_id, 'total' => round($job->total_amount, 2), 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content, 'currency' => $currency->symbol);
				Mail::send('mails.driverTripComplete', $mail_header, function ($message)
					 use ($driveremail, $from_mail, $app_name) {
						$message->from($from_mail, $app_name);
						$message->subject('Trip Complete');
						$message->to($driveremail);

					});

				///////

				return response()->json($response, 200);
			} else {
				$message['code'] = 500;
				$message['error'] = 'Not saved.';
				$response['message'] = $message;
				return response()->json($response, 200);
			}
		} elseif ($input['mode'] == 'customer_trip_cancel') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
				'category_id' => 'required',
			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			$job = DriverTrip::where('id', $input['booking_id'])->first();
			//$job = DriverTrip::where('booking_id', $input['booking_id'])->first();
			$job->cancel_status = 1;
			$job->status = 5;
			if ($job->save()) {
				$response = [
					'result' => $input,
					'message' => "Request saved successfully.",
				];
				// $data['cancel_status'] = '1';
				// $data['cancel_trip'] ='Customer Cancelled';
				// //$data['status'] = 6;
				// $data['driver_id'] = $input['driver_id'];
				// $data['customer_id'] = $job->cus_id;
				// $data['service_status'] = 8;
				// $data['cancel_status']=1;
				// $data['drop_time']=$ctime->toTimeString();;
				// $this->updateFirebase($data);
				$this->driverStatusCancel($input['booking_id'], $input['category_id']);
			} else {
				$message['code'] = 500;
				$message['error'] = 'Not saved.';
				$response['message'] = $message;
				return response()->json($response, 200);
			}
			return response()->json($response, 200);
		} elseif ($input['mode'] == 'customer_cancel') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
				'driver_id' => 'required',
			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			$job = DriverTrip::where('id', $input['booking_id'])->first();

			$job->cancel_status = 1;
			$job->status = 8;
			if ($job->save()) {
				$drivercheck = DriverCheckin::where('driver_id', $input['driver_id'])->first();
				$drivercheck->booking_status = 0;
				$drivercheck->save();
				$response = [
					'result' => $input,
					'message' => "Request saved successfully.",
				];
				$data['cancel_status'] = '1';
				$data['cancel_trip'] = 'Customer Cancelled';
				//$data['status'] = 6;
				$data['driver_id'] = $input['driver_id'];
				$data['customer_id'] = $job->cus_id;
				$data['service_status'] = 8;
				$data['cancel_status'] = 1;
				$data['drop_time'] = $ctime->toTimeString();
				$this->updateFirebase($data);
			} else {
				$response['message'] = 'Not saved.';
				return response()->json($response, 200);
			}
			return response()->json($response, 200);
		} elseif ($input['mode'] == 'driver_cancel') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			$job = DriverTrip::where('id', $input['booking_id'])->first();

			$job->cancel_status = 1;
			$job->status = 9;
			if ($job->save()) {
				$drivercheck = DriverCheckin::where('driver_id', $input['driver_id'])->first();
				$drivercheck->booking_status = 0;
				$drivercheck->save();
				$response = [
					'result' => $input,
					'message' => "Request saved successfully.",
				];
				$data['cancel_status'] = '1';
				$data['cancel_trip'] = 'Driver Cancelled';
				//$data['status'] = 6;
				$data['driver_id'] = $input['driver_id'];
				$data['customer_id'] = $job->cus_id;
				$data['service_status'] = 9;
				$data['cancel_status'] = 1;
				$data['drop_time'] = $ctime->toTimeString();
				$this->updateFirebase($data);
			} else {

				$response['message'] = 'Not saved.';
				return response()->json($response, 200);
			}
			return response()->json($response, 200);
		} elseif ($input['mode'] == 'schedule_customer_cancel') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			$job = DriverTrip::where('id', $input['booking_id'])->first();

			$job->cancel_status = 1;
			$job->status = 10;
			if ($job->save()) {
				$response = [
					'result' => $input,
					'message' => "Request saved successfully.",
				];
			} else {
				$message['code'] = 500;
				$message['error'] = 'Not saved.';
				$response['message'] = $message;
				return response()->json($response, 200);
			}
			return response()->json($response, 200);
		} elseif ($input['mode'] == 'schedule_driver_cancel') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			$job = DriverTrip::where('id', $input['booking_id'])->first();

			$job->cancel_status = 1;
			$job->status = 11;
			if ($job->save()) {
				$response = [
					'result' => $input,
					'message' => "Request saved successfully.",
				];
				$data['cancel_status'] = '1';
				$data['cancel_trip'] = 'Driver Cancelled';
				//$data['status'] = 6;
				$data['driver_id'] = $input['driver_id'];
				$data['customer_id'] = $job->cus_id;
				$data['service_status'] = 11;
				$data['cancel_status'] = 1;
				$data['drop_time'] = $ctime->toTimeString();
				$this->updateFirebase($data);
			} else {
				$message['code'] = 500;
				$message['error'] = 'Not saved.';
				$response['message'] = $message;
				return response()->json($response, 200);
			}
			return response()->json($response, 200);
		} elseif ($input['mode'] == 'pick_time') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
				'pickup_lat' => 'required',
				'pickup_lon' => 'required',
			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			$job = DriverTrip::where('id', $input['booking_id'])->first();

			$job->driver_pick_start_lat = $input['pickup_lat'];
			$job->driver_pick_start_lon = $input['pickup_lon'];
			$job->driver_pick_start_time = $ctime;
			$job->status = 21;
			if ($job->save()) {
				$response = [
					'result' => $input,
					'message' => "Request saved successfully.",
				];
			} else {
				$message['code'] = 500;
				$message['error'] = 'Not saved.';
				$response['message'] = $message;
				return response()->json($response, 200);
			}
			return response()->json($response, 200);
		} elseif ($input['mode'] == 'wait_time') {
			$input = $request->all();
			$ctime = Carbon::now();
			$ctime->toTimeString();
			$validator = Validator::make($input, [
				'booking_id' => 'required',
				'pickup_lat' => 'required',
				'pickup_lon' => 'required',
			]);
			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}
			$job = DriverTrip::where('id', $input['booking_id'])->first();

			$job->driver_pick_end_time = $ctime;
			$job->driver_wait_start_lat = $input['pickup_lat'];
			$job->driver_wait_start_lon	 = $input['pickup_lon'];
			$job->driver_wait_start_time = $ctime;
			$job->status = 22;
			if ($job->save()) {
				$response = [
					'result' => $input,
					'message' => "Request saved successfully.",
				];
			} else {
				$message['code'] = 500;
				$message['error'] = 'Not saved.';
				$response['message'] = $message;
				return response()->json($response, 200);
			}
			return response()->json($response, 200);
		}
	}
	function rand_float($st_num = 0, $end_num = 1, $mul = 1000000) {
		if ($st_num > $end_num) {
			return false;
		}

		return mt_rand($st_num * $mul, $end_num * $mul) / $mul;
	}
	public function admin_assign(Request $request) {

		$input = $request->all();
		$ctime = Carbon::now();
		$ctime->toTimeString();

		if ($input['mode'] == 'ridenow') {

			$validator = Validator::make($input, [
				'customer_id' => 'required',
				'pick_up' => 'required',
				'pickup_lat' => 'required',
				'pickup_lon' => 'required',
				'drop_location' => 'required',
				'drop_lat' => 'required',
				'drop_lon' => 'required',
				'mode' => 'required',
				'vehicle_id' => 'required',
				'driver_id' => 'required',
			]);

			if ($validator->fails()) {
				return $this->sendError('Validation Error.' . $validator->errors());
			}

			$customer = Customer::find($input['customer_id']);

			if ($customer->status != 1) {
				$response['message'] = "Your account is blocked,kindly contact Admin";
				return response()->json($response, 200);
			}

			$drive = Driver::find($input['driver_id']);

			if ($drive->status != 1) {
				$response['message'] = "Driver account is blocked,kindly contact Admin";
				return response()->json($response, 200);
			}

			$drive_check = DriverCheckin::where('driver_id', $input['driver_id'])->first();

			if ($drive_check->checkin_status != 1) {
				$response['message'] = "This driver is check out recently";
				return response()->json($response, 200);
			} else if ($drive_check->booking_status == 1) {
				$response['message'] = "This driver is currently on trip";
				return response()->json($response, 200);
			}

			$input['trip_num'] = str_random(4);
			$input['cus_id'] = $input['customer_id'];
			$input['customer_name'] = $customer->name;
			$input['customer_lname'] = $customer->last_name;
			$input['customer_email'] = $customer->email;
			$input['customer_phone_number'] = $customer->country . $customer->phone_number;
			$input['today_date'] = $ctime->toDateString();
			$input['phone_num'] = $customer->phone_number;

			$input['added_by'] = isset($input['added_by']) ? $input['added_by'] : '0';

			$input['RequestFrom'] = 'Admin';
			$input['send_OTP'] = isset($input['send_OTP']) ? $input['send_OTP'] : '1';

			$input['pick_up_time'] = $ctime->toTimeString();
			$input['status'] = 1;
			$input['cancel_service_status'] = 0;
			$service = VehicleCategory::where('id', $input['vehicle_id'])->first();
			$input['vehicle_type'] = $service->vehicle_type;
			$input['price_km'] = number_format($service->price_per_km, 2);
			$input['customer_num'] = rand(0000, 9999);

			$digits = 5;
			$input['trip_num'] = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
			if ($input['booking_id'] != 0) {
				DriverTrip::where('id', $input['booking_id'])->update(['cus_id' => $input['customer_id'], 'today_date' => $input['today_date'], 'pick_up' => $input['pick_up'], 'pick_up_time' => $input['pick_up_time'], 'drop_location' => $input['drop_location'], 'vehicle_type' => $input['vehicle_type'], 'drop_lat' => $input['drop_lat'], 'drop_lon' => $input['drop_lon'], 'pickup_lat' => $input['pickup_lat'], 'pickup_lon' => $input['pickup_lon']]);

				$id = DriverTrip::where('id', $input['booking_id'])->first();
			} else {
				$id = DriverTrip::create($input);
			}
			$input['booking_id'] = $id->id;

			$customer->user_photo = env('IMG_URL') . $customer->user_photo;
			$trimmed = str_replace('/public', '', $customer->user_photo);
			$input['customer_profile'] = $trimmed;

			$job = DriverTrip::where('id', $input['booking_id'])->first();

			if (!is_object($job)) {
				$response['message'] = 'failure';
				return $response;
			}

			if ($job->status == 2) {
				$response['message'] = 'Trip Already Accepted';
				return $response;
			}

			$job->pick_up_time = $ctime->toTimeString();
			$job->status = 2;
			$job->driver_id = $input['driver_id'];

			$driver = Driver::find($input['driver_id']);
			$job->driver_name = $driver->name;
			$job->driver_lname = $driver->last_name;

			$checkin = DriverCheckin::where('driver_id', $input['driver_id'])->first();
			$checkin->booking_status = 1;
			$checkin->update();
			if ($job->save()) {
				$customer = Customer::where('id', $job->cus_id)->first();

				$response = [
					'result' => $input,
					'message' => "Trip Accept",
				];

				$username = Customer::select('name', 'email')->where('id', '=', $job->cus_id)->first();
				$useremail = $username->email;
				$name = $username->name;
				//$otp = $username->otp;
				$social_url = DB::table('settings')->first();
				$skype = $social_url->skype;
				$facebook = $social_url->facebook;
				$twitter = $social_url->twitter;
				$app_name = env("APP_NAME");
				$from_mail = env("MAIL_USERNAME");
				$website = $social_url->website_url;
				$email_to = $social_url->mail_to;
				$logo = $social_url->logo_url;
				$admin_mail = $social_url->email;
				$content = Email::select('content')->where('template_name', '=', 'Customer Trip Confirmation')->first();

				$content = str_replace("{{env('APP_NAME')}}", $app_name, $content->content);
				$mail_header = array("name" => $name, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content);
				Mail::send('mails.customerTripConfirm', $mail_header, function ($message)
					 use ($useremail, $from_mail, $app_name) {
						$message->from($from_mail, $app_name);
						$message->subject('Trip Confirm');
						$message->to($useremail);

					});

				$username1 = Driver::where('id', '=', $job->driver_id)->first();

				$driveremail = $username1->email;
				$name = $username1->name;
				$customer_id = $job->cus_id;
				$customer_name = $job->customer_name;
				$driver_id = $job->driver_id;
				$driver_name = $username1->name;
				$booking_id = $job->id;
				$social_url = DB::table('settings')->first();
				$skype = $social_url->skype;
				$facebook = $social_url->facebook;
				$twitter = $social_url->twitter;
				$app_name = env("APP_NAME");
				$from_mail = env("MAIL_USERNAME");
				$website = $social_url->website_url;
				$email_to = $social_url->mail_to;
				$logo = $social_url->logo_url;
				$admin_mail = $social_url->email;

				$content = Email::select('content')->where('template_name', '=', 'Driver Trip Notification')->first();
				$content = str_replace("{{env('APP_NAME')}}", $app_name, $content->content);

				$mail_header = array("name" => $name, 'customer_id' => $customer_id, 'customer_name' => $customer_name, 'driver_id' => $driver_id, 'driver_name' => $driver_name, 'booking_id' => $job->id, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content);
				Mail::send('mails.driverTripNotification', $mail_header, function ($message)
					 use ($driveremail, $from_mail, $app_name) {
						$message->from($from_mail, $app_name);
						$message->subject('Trip Notification');
						$message->to($driveremail);

					});
				////
				////customer trip Notification Details
				$social_url = DB::table('settings')->first();
				$skype = $social_url->skype;
				$facebook = $social_url->facebook;
				$twitter = $social_url->twitter;
				$app_name = env("APP_NAME");
				$from_mail = env("MAIL_USERNAME");
				$website = $social_url->website_url;
				$email_to = $social_url->mail_to;
				$logo = $social_url->logo_url;
				$admin_mail = $social_url->email;
				$content = Email::select('content')->where('template_name', '=', 'Customer Trip Notification')->first();
				$content = str_replace("{{env('APP_NAME')}}", $app_name, $content->content);
				$mail_header = array("customer_name" => $customer_name, 'customer_id' => $customer_id, 'customer_name' => $customer_name, 'driver_id' => $driver_id, 'driver_name' => $driver_name, 'booking_id' => $job->id, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content);
				Mail::send('mails.customerTripNotification', $mail_header, function ($message)
					 use ($useremail, $from_mail, $app_name) {
						$message->from($from_mail, $app_name);
						$message->subject('Trip Notification');
						$message->to($useremail);

					});
				/////

				////customer Dispatch Booking OTP

				if ($input['send_OTP'] == 1) {
					$social_url = DB::table('settings')->first();
					$skype = $social_url->skype;
					$facebook = $social_url->facebook;
					$twitter = $social_url->twitter;
					$app_name = env("APP_NAME");
					$from_mail = env("MAIL_USERNAME");
					$website = $social_url->website_url;
					$email_to = $social_url->mail_to;
					$logo = $social_url->logo_url;
					$admin_mail = $social_url->email;

					$content = Email::select('content')->where('template_name', '=', 'Customer Trip Start - OTP')->first();
					$content = str_replace("{{env('APP_NAME')}}", $app_name, $content->content);
					$mail_header = array("customer_name" => $customer_name, 'customer_id' => $customer_id, 'customer_name' => $customer_name, 'driver_id' => $driver_id, 'driver_name' => $driver_name, 'booking_id' => $job->id, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content, 'otp' => $job->trip_num);
					Mail::send('mails.customerTripStartOTP', $mail_header, function ($message)
						 use ($useremail, $from_mail, $app_name) {
							$message->from($from_mail, $app_name);
							$message->subject('OTP - Trip Start');
							$message->to($useremail);

						});
				}
				/////

				$data['booking_id'] = $job->id;
				$data['start_time'] = $ctime->toTimeString();
				$data['driver_id'] = $input['driver_id'];
				$data['customer_id'] = $job->cus_id;
				$data['service_status'] = 2;
				$data['driver_name'] = $username1->name;
				$data['driver_lname'] = $username1->last_name;

				$input['service_status'] = 2;
				$username1->photo = env('IMG_URL') . $username1->photo;
				$trimmed = str_replace('/public', '', $username1->photo);
				$data['profile_pic'] = $trimmed;

				$data['category_type'] = $username1->vehicle_type;
				$data['category_id'] = $username1->vehicle_id;
				$data['otp'] = $job->trip_num;
				$data['vehicle_number'] = $username1->vehicle_num;
				$data['phone'] = $username1->phone_number;
				$rating = DB::table('customer_feedbacks')
					->where('driver_id', $input['driver_id'])
					->avg('driver_rating');
				$avg_rating = round($rating, 1);
				$data['rating'] = $avg_rating;
				$this->updateadminFirebase($input);
				$this->updateFirebase($data);
				$this->deleteFirebase($input['booking_id']);
				return response()->json($response, 200);

			} else {
				$response['message'] = 'Not saved';
				return response()->json($response, 200);
			}
		}

	}
	public function driverStatusCancel($booking_id = '', $category_id = '') {
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();
		$stat_upd = $database->getReference('drivers_location' . '/' . $category_id);
		$stat_up = $stat_upd->getValue();

		foreach ($stat_up as $k => $driver) {
			$status_update = $database->getReference('drivers_trips' . '/' . $k)->getValue();

			if ($status_update['BookingId'] == $booking_id) {
				//print_r($k);
				$update1 = [
					'drivers_trips/' . $k . '/Status' => '0',
				];
				$st_up = $database->getReference()->update($update1);
			}

		}
		return TRUE;
	}
	protected function saveFirebase($input, $chk) {
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();
		$appsetting = AppSetting::first();
		foreach ($chk as $driver) {
			$resultqa['Status'] = "1";
			$resultqa['Title'] = "Trip Request";
			$resultqa['BookingId'] = $input['booking_id'];
			$resultqa['VehicleId'] = $input['vehicle_id'];
			$resultqa['CustomerId'] = $input['customer_id'];
			$resultqa['Profile'] = $input['customer_profile'];
			$resultqa['CustomerName'] = $input['customer_name'];
			$resultqa['CustomerLastName'] = $input['customer_lname'];
			$resultqa['PhoneNumber'] = isset($input['phone_num']) ? $input['phone_num'] : '""';
			$resultqa['DriverPhoneNumber'] = $driver['phone_number'];
			$resultqa['DriverName'] = $driver['name'];
			$resultqa['DriverLastName'] = $driver['last_name'];
			$resultqa['PickupLocation'] = $input['pick_up'];
			$resultqa['DropLocation'] = $input['drop_location'];
			$resultqa['PickupLatitude'] = $input['pickup_lat'];
			$resultqa['PickupLongitude'] = $input['pickup_lon'];
			$resultqa['DropLatitude'] = $input['drop_lat'];
			$resultqa['DropLongitude'] = $input['drop_lon'];
			$resultqa['CreatedTime'] = date('d/m/y H:i:s');
			$resultqa['RequestFrom'] = $input['RequestFrom'];
			$resultqa['RequestFromOtp'] = isset($input['send_OTP']) ? $input['send_OTP'] : '1';
			$resultpa['Status'] = 1;
			if ($driver['device_id'] != '0' && $driver['device_id'] != '') {
				$resultss = $this->sendFCMDriver($driver['device_id'], "Customer request for service");
			}

			$update1 = [
				'drivers_trips/' . $driver['id'] => $resultqa,
			];

			$update2 = [
				'customer_trips/' . $input['customer_id'] => $resultqa,
			];
			$newpost = $database->getReference() // this is the root reference
				->update($update1);
			$newpost = $database->getReference() // this is the root reference
				->update($update2);
		}
		return true;
	}

	protected function saveFirebaseCronCus($input, $cus_id) {
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();
		$appsetting = AppSetting::first();
		$resultqa['Status'] = "1";
		$resultqa['Title'] = "Scheduled Trip Request";
		$resultqa['BookingId'] = $input['BookingId'];
		$resultqa['CustomerId'] = $cus_id;
		$resultqa['Profile'] = $input['CustomerProfile'];
		$resultqa['CustomerName'] = $input['CustomerName'];
		$resultqa['CustomerLastName'] = $input['CustomerLastName'];
		$resultqa['PhoneNumber'] = $input['PhoneNumber'];
		$resultqa['PickupLocation'] = $input['PickupLocation'];
		$resultqa['DropLocation'] = $input['DropLocation'];
		$resultqa['PickupLatitude'] = $input['PickupLatitude'];
		$resultqa['PickupLongitude'] = $input['PickupLongitude'];
		$resultqa['DropLatitude'] = $input['DropLatitude'];
		$resultqa['DropLongitude'] = $input['DropLongitude'];
		$resultqa['CreatedTime'] = date('d/m/y H:i:s');
		//$resultss = $this->sendFCMDriver($driver['device_id'], "Customer request for service");

		$update2 = [
			'customer_trips/' . $cus_id => $resultqa,
		];

		$newpost = $database->getReference() // this is the root reference
			->update($update2);
		return true;
	}

	protected function saveFirebaseCron($input, $input1, $cus_id, $driver_id) {
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();
		$appsetting = AppSetting::first();

		$update1 = [
			'drivers_trips/' . $driver_id => $input1,
		];

		$update2 = [
			'schedule_trip/' . $cus_id . "/" . $input['BookingId'] => $input,
		];

		$newpost = $database->getReference() // this is the root reference
			->update($update1);
		$newpost = $database->getReference() // this is the root reference
			->update($update2);

		return true;
	}

	protected function scheduleSaveFirebase($input) {
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();
		$appsetting = AppSetting::first();
		//foreach($chk as $driver) {
		$resultqa['Status'] = "1";
		$resultqa['Title'] = "Trip Request";
		$resultqa['BookingId'] = $input['booking_id'];
		$resultqa['CustomerId'] = $input['customer_id'];
		$resultqa['Profile'] = "CustomerImages/" . $input['customer_profile'];
		$resultqa['CustomerName'] = $input['customer_name'];
		$resultqa['CustomerLastName'] = $input['customer_lname'];
		$resultqa['PhoneNumber'] = $input['phone_num'];
		$resultqa['PickupLocation'] = $input['pick_up'];
		$resultqa['DropLocation'] = $input['drop_location'];
		$resultqa['PickupLatitude'] = $input['pickup_lat'];
		$resultqa['PickupLongitude'] = $input['pickup_lon'];
		$resultqa['DropLatitude'] = $input['drop_lat'];
		$resultqa['DropLongitude'] = $input['drop_lon'];
		$resultqa['CreatedTime'] = date('d/m/y H:i:s');
		//$resultpa['status'] = 1;
		//$resultss = $this->sendFCMDriver($driver['device_id'],"Customer request for service");

		// $update1 = [
		// 	'drivers_status/' . $driver['id'] => $resultqa,
		// ];

		$update2 = [
			'customer_trips/' . $input['customer_id'] => $resultqa,
		];

		// $newpost = $database->getReference() // this is the root reference
		// 	->update($update1);
		$newpost = $database->getReference() // this is the root reference
			->update($update2);
		//  }
		return true;
	}

	protected function updateFirebase($data) {
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();
		if ($data['service_status'] == 3) {
			$field = 'start_time';

			$update = array('Status' => $data['service_status']);
			/*$update1 = [
					'customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
				];
				$updates = [
					'drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status'],
			*/
			$newpost = $database->getReference('customer_trips/' . $data['customer_id'])
				->update($update);

			$newpost = $database->getReference('drivers_trips/' . $data['driver_id'])
				->update($update);
			// return true;
		} elseif ($data['service_status'] == 1) {

			$update1 = array(
				'PickupLocation' => $data['pick_up'],
				'Status' => $data['service_status'],
				'DropLocation' => $data['drop_location'],
				'VehicleType' => $data['vehicle_type'],
				'DropLatitude' => $data['drop_lat'],
				'DropLongitude' => $data['drop_lon'],
				'PickupLatitude' => $data['pickup_lat'],
				'PickupLongitude' => $data['pickup_lon'],
				'PickupTime' => $data['pick_up_time'],
			);

			/*$update1 = [
				'customer_trips/' . $data['customer_id'] . '/PickupLocation' => $data['pick_up'],
				'customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
				'customer_trips/' . $data['customer_id'] . '/DropLocation' => $data['drop_location'],
				'customer_trips/' . $data['customer_id'] . '/VehicleType' => $data['vehicle_type'],
				'customer_trips/' . $data['customer_id'] . '/DropLatitude' => $data['drop_lat'],
				'customer_trips/' . $data['customer_id'] . '/DropLongitude' => $data['drop_lon'],
				'customer_trips/' . $data['customer_id'] . '/PickupLatitude' => $data['pickup_lat'],
				'customer_trips/' . $data['customer_id'] . '/PickupLongitude' => $data['pickup_lon'],
				'customer_trips/' . $data['customer_id'] . '/PickupTime' => $data['pick_up_time'],
			];*/
			$newpost = $database->getReference('customer_trips/' . $data['customer_id'])
				->update($update1);
		} elseif ($data['service_status'] == 4) {
			$field = 'reach_time';
			// $data['total_amount']=$total_amount;
			// 	$data['base_fare']=$base_fare;
			// 	$data['price_per_km']=$price_per_km;
			// 	$data['total_distance']=$job->total_distance;
			// 	$data['drop_location']=$job->drop_location;

			$update1 = array(
				'Status' => $data['service_status'],
				'TotalAmount' => $data['total_amount'],
				'BaseFare' => $data['base_fare'],
				'PricePerKm' => $data['price_per_km'],
				'TotalDistance' => $data['total_distance'],
				'Distance' => $data['distance'],
				'DropLocation' => $data['drop_location'],
				'CashStatus' => '0',
			);

			$newpost = $database->getReference('customer_trips/' . $data['customer_id'])
				->update($update1);

			$update1['CategoryType'] = $data['vehicle_type'];

			$newpost = $database->getReference('drivers_trips/' . $data['driver_id'])
				->update($update1);

			/*$update1 = [
				'customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
				'customer_trips/' . $data['customer_id'] . '/TotalAmount' => $data['total_amount'],
				'customer_trips/' . $data['customer_id'] . '/BaseFare' => $data['base_fare'],
				'customer_trips/' . $data['customer_id'] . '/PricePerKm' => $data['price_per_km'],
				'customer_trips/' . $data['customer_id'] . '/TotalDistance' => $data['total_distance'],
				'customer_trips/' . $data['customer_id'] . '/Distance' => $data['distance'],
				'customer_trips/' . $data['customer_id'] . '/DropLocation' => $data['drop_location'],
				'customer_trips/' . $data['customer_id'] . '/CashStatus' => '0',
			];*/
			/*$updates = [
				'drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status'],
				'drivers_trips/' . $data['driver_id'] . '/TotalAmount' => $data['total_amount'],
				'drivers_trips/' . $data['driver_id'] . '/BaseFare' => $data['base_fare'],
				'drivers_trips/' . $data['driver_id'] . '/PricePerKm' => $data['price_per_km'],
				'drivers_trips/' . $data['driver_id'] . '/TotalDistance' => $data['total_distance'],
				'drivers_trips/' . $data['driver_id'] . '/Distance' => $data['distance'],

				'drivers_trips/' . $data['driver_id'] . '/DropLocation' => $data['drop_location'],
				'drivers_trips/' . $data['driver_id'] . '/CashStatus' => '0',
				'drivers_trips/' . $data['driver_id'] . '/CategoryType' => $data['vehicle_type'],

			];*/

		} elseif ($data['service_status'] == 5) {
			$field = 'service_start_time';
		}

		if ($data['service_status'] == 2) {
			$field = 'start_time';
			//$field='profile';
			$update1 = array(
				'Status' => $data['service_status'],
				'DriverId' => $data['driver_id'],
				'DriverName' => $data['driver_name'],
				'DriverLastName' => $data['driver_lname'],
				'Profile' => isset($data['profile_pic']) ? $data['profile_pic'] : "DriverImages/",
				'CategoryType' => $data['category_type'],
				'CategoryId' => $data['category_id'],
				'TripOtp' => $data['otp'],
				'Rating' => $data['rating'],
				'VehicleNumber' => $data['vehicle_number'],
				'DriverPhoneNumber' => $data['phone'],
			);
			$newpost = $database->getReference('customer_trips/' . $data['customer_id'])
				->update($update1);

			$updates = array('Status' => $data['service_status'],
				'TripOtp' => $data['otp'],
			);
			$newpost = $database->getReference('drivers_trips/' . $data['driver_id'])
				->update($updates);

			$this->driverStatusUpdate($data['category_id'], $data['booking_id'], $data['driver_id']);
			/*$updates = [
				'drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status'],
				'drivers_trips/' . $data['driver_id'] . '/TripOtp' => $data['otp'],
			];*/
			// $data['driver_name']=$username1->name;
			// $data['profile_pic']=$username1->photo;
			// $data['category_type']=$username1->vehicle_type;
			// $data['otp']=$job->trip_num;
			// $data['vehicle_number']=$username1->vehicle_num;

			/*$update1 = [
				'customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
				'customer_trips/' . $data['customer_id'] . '/DriverId' => $data['driver_id'],
				'customer_trips/' . $data['customer_id'] . '/DriverName' => $data['driver_name'],
				'customer_trips/' . $data['customer_id'] . '/DriverLastName' => $data['driver_lname'],
				'customer_trips/' . $data['customer_id'] . '/Profile' => isset($data['profile_pic']) ? $data['profile_pic'] : "DriverImages/",
				'customer_trips/' . $data['customer_id'] . '/CategoryType' => $data['category_type'],
				'customer_trips/' . $data['customer_id'] . '/CategoryId' => $data['category_id'],
				'customer_trips/' . $data['customer_id'] . '/TripOtp' => $data['otp'],
				'customer_trips/' . $data['customer_id'] . '/Rating' => $data['rating'],
				'customer_trips/' . $data['customer_id'] . '/VehicleNumber' => $data['vehicle_number'],
				'customer_trips/' . $data['customer_id'] . '/DriverPhoneNumber' => $data['phone'],
			];*/

		}
		if ($data['service_status'] > 5) {
			if ($data['service_status'] == 6) {
				$field = 'total_amount';

				$updates = [
					'drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status'],
					'drivers_trips/' . $data['driver_id'] . '/PaymentName' => $data['payment_name'],
					//	'drivers_trips/' . $data['driver_id'] . '/TotalDistance' => $data['total_distance'],
					//	'drivers_trips/' . $data['driver_id'] . '/TotalAmount' => $data['total_amount'],
				];

				$update1 = [
					'customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
					'customer_trips/' . $data['customer_id'] . '/PaymentName' => $data['payment_name'],
					//	'customer_trips/' . $data['customer_id'] . '/TotalAmount' => $data['total_amount'],
				];
				$newpost = $database->getReference()
					->update($update1);
				$newpost = $database->getReference()
					->update($updates);

			} elseif ($data['service_status'] == 7) {
				$updates = ['drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status'],
				];
				$update1 = [
					'customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
				];

			} elseif ($data['service_status'] == 8) {
				$updates = ['drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status'],
				];
				$update1 = [
					'customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
				];
				$newpost = $database->getReference()
					->update($update1);
				$newpost = $database->getReference()
					->update($updates);

			} elseif ($data['service_status'] == 9) {
				$updates = ['drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status']];
				$update1 = ['customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status']];
				$newpost = $database->getReference()
					->update($update1);
				$newpost = $database->getReference()
					->update($updates);
			} elseif ($data['service_status'] == 10) {
				$update1 = ['customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
					'customer_trips/' . $data['customer_id'] . '/BalanceAmount' => $data['balance_amount'],
					'customer_trips/' . $data['customer_id'] . '/TotalAmount' => $data['total_amount']];
				$updates = ['drivers_trips/' . $data['driver_id'] . '/BalanceAmount' => $data['balance_amount'],
					'drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status'],
					'drivers_trips/' . $data['driver_id'] . '/ExtraCharge' => $data['material_fee'],
					'drivers_trips/' . $data['driver_id'] . '/MiscCharge' => $data['misc_charge'],
					'drivers_trips/' . $data['driver_id'] . '/DriverDiscount' => $data['driver_discount'],
					'drivers_trips/' . $data['driver_id'] . '/TotalAmount' => $data['total_amount']];

			} elseif ($data['service_status'] == 14) {
				$updates = ['drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status'],
				];
				$update1 = [
					'customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
				];

			} elseif ($data['service_status'] == 15) {
				$updates = ['drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status']];
				$update1 = ['booking_trips/' . $data['customer_id'] . '/Status' => $data['service_status']];
			} else {
				$updates = [
					'drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status'],
				];
				$update1 = [
					'customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
				];
			}
		}
		//  else {
		// 	$updates = [
		// 		'drivers_status/' . $data['driver_id'] .'/status' => $data['service_status'],
		// 		'drivers_status/' . $data['driver_id']  .'/'. $field => $data[$field],
		// 	];
		// 	$update1 = [
		// 		'customer_trips/' . $data['customer_id'] .'/status' => $data['service_status'],
		// 		'customer_trips/' . $data['customer_id']  .'/'.$field => $data[$field],
		// 	];
		// }
		// $newpost = $database->getReference() // this is the root reference
		// 	->update($updates);
		// $newpost = $database->getReference() // this is the root reference
		// 	->update($update1);

		return true;
	}

	protected function updateadminFirebase($input) {
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();

		if ($input['service_status'] == 2) {
			$field = 'start_time';
			$resultqa['Status'] = "1";
			$resultqa['Title'] = "Trip Accepted";
			$resultqa['BookingId'] = $input['booking_id'];
			$resultqa['CustomerId'] = $input['customer_id'];
			$resultqa['Profile'] = $input['customer_profile'];
			$resultqa['CustomerName'] = $input['customer_name'];
			$resultqa['CustomerLastName'] = $input['customer_lname'];
			$resultqa['PhoneNumber'] = $input['phone_num'];
			$resultqa['PickupLocation'] = $input['pick_up'];
			$resultqa['DropLocation'] = $input['drop_location'];
			$resultqa['PickupLatitude'] = $input['pickup_lat'];
			$resultqa['PickupLongitude'] = $input['pickup_lon'];
			$resultqa['DropLatitude'] = $input['drop_lat'];
			$resultqa['DropLongitude'] = $input['drop_lon'];
			$resultqa['CreatedTime'] = date('d/m/y H:i:s');
			$resultpa['Status'] = 2;
			$resultqa['driver_id'] = $input['driver_id'];
			$resultqa['added_by'] = 'admin';
			$resultqa['RequestFrom'] = 'Admin';
			$resultqa['RequestFromOtp'] = isset($input['send_OTP']) ? $input['send_OTP'] : '1';

			$driver = Driver::where('id', $input['driver_id'])->first()->toArray();

			$resultqa['DriverPhoneNumber'] = $driver['phone_number'];
			$resultqa['DriverName'] = $driver['name'];
			$resultqa['DriverLastName'] = $driver['last_name'];

			if ($driver['device_id'] != '0' && $driver['device_id'] != '') {
				$resultss = $this->sendFCMDriver($driver['device_id'], "Admin added a trip");
			}

			$update1 = [
				'drivers_trips/' . $input['driver_id'] => $resultqa,
			];

			$update2 = [
				'customer_trips/' . $input['customer_id'] => $resultqa,
			];
			$newpost = $database->getReference() // this is the root reference
				->update($update1);
			$newpost = $database->getReference() // this is the root reference
				->update($update2);
		}

		return true;
	}
	public function driverStatusUpdate($vehicle_id = '', $booking_id = '', $driver_id = '') {
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();
		$stat_upd = $database->getReference('drivers_location' . '/' . $vehicle_id);
		$stat_up = $stat_upd->getValue();

		foreach ($stat_up as $k => $driver) {
			$status_update = $database->getReference('drivers_trips' . '/' . $k)->getValue();
			if ($k != $driver_id) {
				if (isset($status_update['BookingId']) && $status_update['BookingId'] != '' && $status_update['BookingId'] == $booking_id) {
					//print_r($k);
					$update1 = [
						'drivers_trips/' . $k . '/Status' => '0',
					];
					$st_up = $database->getReference()->update($update1);
				}
			}
		}
		return TRUE;
	}

	public function TimeoutUpdate($data) {

		$vehicle_id = $data['vehicle_id'];
		$booking_id = $data['booking_id'];

		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();
		$stat_upd = $database->getReference('drivers_location' . '/' . $vehicle_id);
		$stat_up = $stat_upd->getValue();

		foreach ($stat_up as $k => $driver) {
			$status_update = $database->getReference('drivers' . '/' . $k . '/request')->getValue();
			if (isset($status_update['booking_id']) && $status_update['booking_id'] != '' && $status_update['booking_id'] == $booking_id) {
				$update1 = [
					'drivers/' . $k . '/request/status' => '0',
				];
				$st_up = $database->getReference()->update($update1);
			}
		}
		return TRUE;
	}

	protected function sendFCMDriver($token, $msg) {
		$optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60 * 20);

		$notificationBuilder = new PayloadNotificationBuilder(env('APP_NAME'));
		$notificationBuilder->setBody($msg)
			->setSound('default');

		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['a_data' => 'my_data']);

		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();

		$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
	}
	protected function available_drivers($data) {
		$c_lat = $data['latitude'];
		$c_lon = $data['longitude'];
		$result = array();
		//$drivers = DriverCheckin::where('vehicle_id', $data['vehicle_id'])->where('checkin_status', 1)->where('booking_status', 0)->get();
		//$data['message'] = "New Booking";
		$serviceAccount = ServiceAccount::fromJsonFile(config_path(env('FIREBASE_KEY')));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
		$database = $firebase->getDatabase();
		$drivers = $database->getReference('drivers_location/' . $data['vehicle_id'])->getValue();
		//print_r($drivers);exit;
		//$unit = "K";
		$driverLists = [];

		if (is_array($drivers)) {
			foreach ($drivers as $key => $driver) {

				$driver_profile = Driver::where('id', $key)->first();

				if (is_object($driver_profile)) {

					if ($driver_profile->status == 1) {

						$radius = DB::table('app_settings')->where('id', '1')->value('radius');

						if ($radius != '' && $radius != '0') {
							$radius = $radius;
						} else {
							$radius = 1.5;
						}

						if ($driver != "") {
							if (@$driver['status'] == 1 && $driver['l'][0] != 0 && $driver['l'][1] != 0) {
								$distance = $this->getDistanceBetweenTwoLocations($c_lat, $c_lon, $driver['l'][0], $driver['l'][1], "Km");
								info("driver distance");
								info($distance);
								if ($distance <= $radius) {
									if (isset($data["favorite_driver_id"]) && $key == $data["favorite_driver_id"]) {
										$result = array();
										$result[] = $driver_profile->toArray();
										return $result;
									} else {
										$result[] = $driver_profile->toArray();
									}
								}
							}
						}
					}
				}
			}
		}

		return $result;
	}

	public function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {
		$earth_radius = 6371;

		$dLat = deg2rad($latitude2 - $latitude1);
		$dLon = deg2rad($longitude2 - $longitude1);

		$a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
		$c = 2 * asin(sqrt($a));
		$d = $earth_radius * $c;

		return $d;
	}

	public function getCommission($amount) {
		$commissionSetting = \App\CommissionCalculationSetting::orderBy('id','desc')->get();	
		foreach ($commissionSetting as $key => $setting) {
			if($amount >= $setting->trip_charge_start && $amount <= $setting->trip_charge_end){
				return $setting->commission;
			}
		}
	}
	public function getTripFare($tripId) {
		$trip = DriverTrip::where('id', $tripId)->first();
		$total_fare = 0;
		if($trip){
			$pick_mileage = $this->getDistanceBetweenTwoLocations($trip->driver_pick_start_lat, $trip->driver_pick_start_lon, $trip->pickup_lat, $trip->pickup_lon);
			$to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $trip->driver_pick_end_time);
			$from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $trip->driver_pick_start_time);
			$pick_time = $to->diffInMinutes($from);
			$trip_mileage = $trip->total_distance;
			$fareSetting = \App\FareCalculationSetting::orderBy('id','desc')->first();	
			$wait_to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $trip->driver_wait_end_time);
			$wait_from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $trip->driver_wait_start_time);
			$wait_time = $wait_to->diffInMinutes($wait_from);
			$drop_off = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $trip->drop_off_time);
			$drive_time = $wait_to->diffInMinutes($drop_off);
			if($fareSetting){
				$total_fare = $pick_mileage * $fareSetting->pick_mileage +
				$pick_time * $fareSetting->pick_time;
				$mileage_limit = $fareSetting->mileage_limit;
				if($trip_mileage <= $mileage_limit){
					$total_fare += $wait_time * $fareSetting->wait_time;
					$total_fare += $trip_mileage * $fareSetting->drive_mileage;
					$total_fare += $drive_time * $fareSetting->drive_time;
				}
				else{
					$total_fare += $wait_time * $fareSetting->wait_time_al;
					$total_fare += $trip_mileage * $fareSetting->drive_mileage_al;
					$total_fare += $drive_time * $fareSetting->drive_time_al;
				}
			}
			if($total_fare < $fareSetting->min_fare)
			{
				return $fareSetting->min_fare;
			}
		}
		return $total_fare;
	}
	public function getDistanceBetweenTwoLocations($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') {
		$theta = $longitude1 - $longitude2; 
		$distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
		$distance = acos($distance); 
		$distance = rad2deg($distance); 
		$distance = $distance * 60 * 1.1515; 
		switch($unit) { 
			case 'Mi': 
			break; 
			case 'Km' : 
			$distance = $distance * 1.609344; 
		} 
		return (round($distance,2)); 
	}
	protected function driverList($data) {
		$c_lat = $data['latitude'];
		$c_lon = $data['longitude'];
		$drivers = DriverCheckin::where('vehicle_id', $data['vehicle_id'])->where('checkin_status', 1)->where('booking_status', 0)->get();
		$data['message'] = "New Booking";
		$unit = "K";
		$driverLists = [];
		foreach ($drivers as $driver) {
			$driver_id = $driver->driver_id;
			$serviceAccount = ServiceAccount::fromJsonFile(env('FIREBASE_KEY'));
			$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
			$database = $firebase->getDatabase();
			$drivers = $database->getReference('drivers_location/' . $data['vehicle_id'] . '/' . $driver_id)->getValue();

			/*print_r($driver_id);echo '<br>';
			print_r($drivers);*/
			//print_r($drivers);
			//print_r($drivers[$driver_id][l][0]);exit;
			//echo $c_lat.'<br>'.$c_lon.'<br>';
			if (@$drivers['status'] == 0) {
				$driver_profile = Driver::where('id', $driver_id)->first();
				if (is_object($driver_profile)) {
					if ($driver_profile->status == 1) {
						/*$d_lon = $driver->d_lon;
					$d_lat = $driver->d_lat;*/
						$d_lat = $drivers['l'][0];
						$d_lon = $drivers['l'][1];
						$theta = $d_lon - $c_lon;
						$dist = sin(deg2rad($d_lat)) * sin(deg2rad($c_lat)) + cos(deg2rad($d_lat)) * cos(deg2rad($c_lat)) * cos(deg2rad($theta));
						$dist = acos($dist);
						$dist = rad2deg($dist);
						$miles = $dist * 60 * 1.1515;
						if ($unit == "K") {
							$distance = ($miles * 1.609344);
						} else {
							$distance = $miles;
						}
						if ($distance <= 5) {
							$driverLists[] = $driver->driver;
						}
					}
				}
			}
		}
		return $driverLists;
	}

	public function getphone(Request $request) {
		$q = $request->get('query');
		return Customer::where('phone_number', 'like', '%' . $q . '%')->get(['phone_number as data', DB::raw('phone_number as value')]);
		exit;
	}

	public function getemail(Request $request) {
		$q = $request->get('query');
		return Customer::where('email', 'like', '%' . $q . '%')->get(['email as data', DB::raw('email as value')]);
		exit;
	}

	public function calculateEstimate(Request $request) {
		$input = $request->all();
		$distance = $this->findDistance($input['from_lat'], $input['from_lng'], $input['to_lat'], $input['to_lng']);
		$fares = DB::table('vehicle_categories')->where('id', $input['vehicle_id'])->first();
		$distance_unit = DB::table('app_settings')->where('id', 1)->value('distance_unit');
		$total_fare = $fares->base_fare + ($fares->price_per_km * $distance);
		$result['fare'] = number_format($total_fare, 2);
		$result['distance'] = number_format($distance, 2) . ' ' . $distance_unit;

		return json_encode($result);
	}

	public function findDistance($from_lat, $from_lng, $to_lat, $to_lng) {
		$ch = curl_init('https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=' . $from_lat . ',' . $from_lng . '&destinations=' . $to_lat . ',' . $to_lng . '&key=' . env('GOOGLE_MAP_API_KEY'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, '');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result);
		$miles = (float) $result->rows[0]->elements[0]->distance->text;
		return $miles * 1.60934;
	}

}