<?php

namespace App\Http\Controllers;

use App\AppSetting;
use App\Customer;
use App\Driver;
use App\DriverCheckin;
use App\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Mail;

class AuthController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	//Customer Related Login and Register Process starts
	public function login() {
		$validator = Validator::make($_REQUEST, [
			'phone_number' => 'required',
			'password' => 'required',
			'type' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		if ($_REQUEST['type'] == "customer") {
			$options = [
				'cost' => 12,
			];
			$credentials = request(['phone_number', 'password', 'otp']);
			$customer = Customer::where('phone_number', request(['phone_number']))->first();
			if (!($customer)) {
				$response['code'] = 401;
				$response['message'] = 'Phone Number Not Found.';
				return response()->json($response, 200);
			}
			if (Hash::check($credentials['password'], $customer->password)) {
				// $success['token'] =  $customer->createToken('MyApp')-> accessToken;
				if (isset($_REQUEST['device_id'])) {
					$customer->device_id = $_REQUEST['device_id'] ? $_REQUEST['device_id'] : $customer->device_id;
				}

				if (isset($_REQUEST['device'])) {
					$customer->device = $_REQUEST['device'] ? $_REQUEST['device'] : $customer->device;
				}

				$customer->save();
				return $this->responseCustomer($customer);
			} else {
				$response['code'] = 401;
				$response['message'] = 'Password Mismatch.';
				return response()->json($response, 200);
			}
		} elseif ($_REQUEST['type'] == "driver") {
			$options = [
				'cost' => 12,
			];
			$credentials = request(['phone_number', 'password', 'otp']);
			$driver = Driver::where('phone_number', request(['phone_number']))->first();

			if (!($driver)) {
				$response['code'] = 401;
				$response['message'] = 'Mail Not Found.';
				return response()->json($response, 200);
			}
			if (Hash::check($credentials['password'], $driver->password)) {
				if (isset($_REQUEST['device_id'])) {
					$driver->device_id = $_REQUEST['device_id'] ? $_REQUEST['device_id'] : $driver->device_id;
				}

				if (isset($_REQUEST['device'])) {
					$driver->device = $_REQUEST['device'] ? $_REQUEST['device'] : $driver->device;
				}

				if (!$driver->verified) {
					$response['code'] = 402;
					$response['message'] = 'User not verified.';
					return $response;
				}
				if (!$driver->approved) {
					$response['code'] = 403;
					$response['message'] = 'User not approved.';
					return $response;
				}
				$driver->save();
				array_walk_recursive($driver, function (&$item, $key) {
					$item = null === $item ? '' : $item;
				});
				//$driver->vehicle_id = $driver->vehicle_type;
				//$vehicle_type=DB::table('vehicle_categories')->where('id',$driver->vehicle_type)->first();
				//$driver->vehicle_type = $vehicle_type->vehicle_type;
				return $this->responseDriver($driver);
			} else {
				$response['code'] = 401;
				$response['message'] = 'Password Mismatch.';
				return response()->json($response, 200);
			}
		}
	}

	protected function responseCustomer($customer) {
		return response()->json([
			'result' => $customer,
			'message' => "Login Successfull",
		]);
	}
	// protected function sendOTP(Request $request) {
	// 	$input = $request->all();
	// 	$validator = Validator::make($input, [
	// 		'email' => 'required',
	// 		//'password' => 'required',
	// 		'phone_number' => 'required',

	// 	]);
	// 	if ($validator->fails()) {
	// 		return $this->sendError('Invalid Params.', $validator->errors());
	// 	}
	// 	$email = $request->input('email');
	// 	$digits = 4;
	// 	$avl = $this->checkAvailability($input);
	// 	if ($avl['available']) {
	// 		// Check otp endbaled or not
	// 			$otpcheck=MobileVerification::where('id',1)->first();
	// 			if($otpcheck->mode=="1"){
	// 				$name = $request->input('name');
	// 				$otp = $this->getOTP();
	// 				$mail_header = array("name" => $name, "otp" => $otp);
	// 				Mail::send('mails.addDriver', $mail_header, function ($message)
	// 					use ($name, $otp, $email) {
	// 						$message->from('pudgybuddyteam@gmail.com', 'SpotnRides');
	// 						$message->subject('Registration');
	// 						$message->to($email);

	// 					});
	// 				$request['otp'] = $otp;

	// 				$result['otp'] = $otp;
	// 				$result['email'] = $request->input('email');
	// 				$result['phone_number'] = $request->input('phone_number');
	// 				$response['result'] = $result;
	// 				$response['message'] = "Otp sent to your mail.";
	// 				return $response;
	// 			}
	// 			else{
	// 				$otp = $this->getOTP();
	// 				$result['otp'] = $otp;
	// 				$result['email'] = $request->input('email');
	// 				$result['phone_number'] = $request->input('phone_number');
	// 				$response['result'] = $result;
	// 				$response['message'] = "Otp Disabled.";
	// 				return $response;
	// 			}

	// 	} else {
	// 		$response['message'] = $avl['message'];
	// 		return $response;
	// 	}
	// }

	protected function sendOTP(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'email' => 'required',
			'phone_number' => 'required',

		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$email = $request->input('email');
		$digits = 4;

		$otpcheck = AppSetting::where('id', 1)->first();
		if ($otpcheck->otp == '1') {
			$avl = $this->checkAvailability($input);
			if ($avl['available']) {
				$name = $request->input('name');
				$otp = $this->getOTP();
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
				$content = Email::select('content')->where('template_name', '=', 'Driver Signup')->first();
				$content = str_replace("{{$otp}}", $otp, $content->content);

				$mail_header = array("name" => $name, "otp" => $otp, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content);
				Mail::send('mails.addDriver', $mail_header, function ($message)
					 use ($name, $otp, $email, $from_mail, $app_name) {
						$message->from($from_mail, $app_name);
						$message->subject('Registration');
						$message->to($email);

					});
				$request['otp'] = $otp;
				$result['otp'] = $otp;
				$result['email'] = $request->input('email');
				$result['phone_number'] = $request->input('phone_number');
				$response['result'] = $result;
				$response['message'] = "Otp sent to your mail";
				return $response;
			} else {
				$result['email'] = $request->input('email');
				$result['phone_number'] = $request->input('phone_number');
				$response['result'] = $result;
				$response['message'] = $avl['message'];
				return $response;
			}
		} else {
			$avl = $this->checkAvailability($input);
			if ($avl['available']) {
				$name = $request->input('name');
				$otp = $this->getOTP();
				$result['otp'] = $otp;
				$result['email'] = $request->input('email');
				$result['phone_number'] = $request->input('phone_number');
				$response['result'] = $result;
				$response['message'] = "OTP disabled";
				return $response;
			} else {
				$result['email'] = $request->input('email');
				$result['phone_number'] = $request->input('phone_number');
				$response['result'] = $result;
				$response['message'] = $avl['message'];
				return $response;
			}
		}
	}

	public function checkAvailability($input) {
		$customer = Customer::where('email', $input['email'])->first();
		if (is_object($customer)) {
			$result['message'] = "Email already exists.";
			$result['available'] = false;
			return $result;
		}
		$driver = Driver::where('email', $input['email'])->first();
		if (is_object($driver)) {
			$result['message'] = "Email already exists.";
			$result['available'] = false;
			return $result;
		}
		$customerphone = Customer::where('phone_number', $input['phone_number'])->first();
		if (is_object($customerphone)) {
			$result['message'] = "Phone Number already exists.";
			$result['available'] = false;
			return $result;
		}
		$driverphone = Driver::where('phone_number', $input['phone_number'])->first();
		if (is_object($driverphone)) {
			$result['message'] = "Phone Number already exists.";
			$result['available'] = false;
			return $result;
		}

		$result['message'] = "Email & Phone Number available.";
		$result['available'] = true;
		return $result;
	}

	public function addCustomer(Request $request) {
		/**
		 * @SWG\Get(
		 *     path="/create",
		 *     description="Return a user's first and last name",
		 *     @SWG\Parameter(
		 *         name="firstname",
		 *         in="query",
		 *         type="string",
		 *         description="Your first name",
		 *         required=true,
		 *     ),
		 *     @SWG\Parameter(
		 *         name="lastname",
		 *         in="query",
		 *         type="string",
		 *         description="Your last name",
		 *         required=true,
		 *     ),
		 *     @SWG\Response(
		 *         response=200,
		 *         description="OK",
		 *     ),
		 *     @SWG\Response(
		 *         response=422,
		 *         description="Missing Data"
		 *     )
		 * )
		 */

		$input = $request->all();
		$validator = Validator::make($input, [
			'email' => 'required',
			// 'password' => 'required',
			'phone_number' => 'required',
			'name' => 'required',
			'last_name' => 'required',
			//	'cus_dob' => 'required',
			//'state' => 'required',
			//'country' => 'required',
			//	'postal_code' => 'required',
			//	'address' => 'required',
			//	'city' => 'required',
			'device_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		//	if ($input['mode'] == "add") {
		$id = $request->input('devicetoken');
		$email = $request->input('email');
		// $password = $request->input('password');
		$user_details["devicetoken"] = $id;
		$user_details["email"] = $email;
		// $user_details["password"] = $password;
		$chk = $this->storeCustomer($request);
		if (!$chk['exist']) {
			$customerObj = $this->getCustomer($request);
			$response['result'] = $customerObj;
			$response['message'] = "Registered Successfully";
			$otp_enabled = \Config::get('constants.enable_otp');
			if ($otp_enabled) {
				send_sms("Your OTP is: " . $customerObj->otp, $customerObj->phone_number);
			}
			// $this->sendMail1($request);
			return $response;
		} else {
			//$response['message'] = $chk['error'];
			//	$message['code'] = 401;
			$response['message'] = 'email or phone number already exists.';
			return response()->json($response, 200);
		}
		/*} else {
			$message['code'] = 401;
			$message['error'] = 'Undefined Mode.';
			$response['message'] = $message;
			return response()->json($response, 200);
		}*/
	}
	public function sendMail1($request) {

		$email = $request->input('email');
		$user = Customer::where('email', '=', $email)->first();
		if (is_object($user)) {
			// $link = url("/api/email_verification/".$token);
			$username = Customer::select('name', 'otp')->where('email', '=', $email)->first();
			$name = $username->name;
			$otp = $username->otp;
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
			$content = Email::select('content')->where('template_name', '=', 'Customer Signup')->first();

			$content = str_replace("{{env('APP_NAME')}}", $app_name, $content->content);
			$mail_header = array("name" => $name, "otp" => $otp, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content);
			Mail::send('mails.addCustomer', $mail_header, function ($message)
				 use ($user, $from_mail, $app_name) {
					$message->from($from_mail, $app_name);
					$message->subject('Registration');
					$message->to($user->email);

				});

			$response['message'] = "Mail sent successfully";
			return $response;
		} else {
			$response['message'] = "Invalid Mail id";
			return $response;
		}
	}
	public function getCustomer($request) {
		$email = $request->input('email');
		$result = customer::where('email', $email)->first();
		$response = $result;
		return $response;
	}

	public function sendResponse($result, $message) {
		$response = [
			'success' => true,
			'result' => $result,
			'message' => $message,
		];
		return response()->json($response, 200);
	}

	public function storeCustomer(Request $request) {
		$options = [
			'cost' => 12,
		];
		$input = $request->all();
		$otp = $this->getOTP();
		$input['otp'] = $otp;
		$email = $request['email'];

		$check = $this->checkCustomer($request);
		if (!$check['exist']) {
			// $input['password'] = password_hash($request->password, PASSWORD_DEFAULT, $options);
			$input['status'] = 1;
			//$input['country']=$request->input('country_code');
			$user = customer::create($input);
		}
		return $check;
	}

	public function sendError($message) {
		$message = [
			//'code' => 401,
			'error' => $message,
		];
		$response['message'] = $message['error'];
		return response()->json($response, 200);
	}

	public function checkCustomer(Request $request) {
		$emailfromUser = $request->input('email');
		$phone_numberfromUser = $request->input('phone_number');
		$check_user = customer::select('id')->where('email', '=', $emailfromUser)->orWhere('phone_number', '=', $phone_numberfromUser)->first();
		if (!empty($check_user)) {
			$response['code'] = 401;
			$response['message'] = 'email or phone number already exists.';
			$response['exist'] = true;
			return $response;
		} else {
			$response['message'] = '';
			$response['exist'] = false;
			return $response;
		}
	}

	public function changePass(Request $request) {
		$options = [
			'cost' => 12,
		];
		$input = $request->all();
		$validator = Validator::make($input, [
			'id' => 'required',
			'password' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$user = Customer::where('id', $input['id'])->first();
		if (is_object($user)) {
			$input['password'] = password_hash($input["password"], PASSWORD_DEFAULT, $options);
			$user->password = $input['password'];
			if ($user->save()) {
				$response['message'] = "Success";
				return $response;
			} else {
				$response['code'] = 401;
				$response['message'] = 'Failure.';

				return response()->json($response, 200);
			}
		} else {
			$response['code'] = 404;
			$response['message'] = 'Not Found.';

			return response()->json($response, 200);
		}
	}
	// Customer related Login register Process Ends

	//Driver related Login  register starts

	protected function responseDriver($driver) {
		return response()->json([
			'result' => $driver,
			'message' => 'Login Successfull',
		]);
	}

	public function addDriver(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'email' => 'required',
			'password' => 'required',
			'phone_number' => 'required',
			'name' => 'required',
			'last_name' => 'required',
			'device_id' => 'required',
			'vin_num' => 'required',
			'vehicle_type' => 'required',
			'vehicle_id' => 'required',
			'license_no' => 'required',
			'document' => 'required',
			'img_insurance' => 'required',
			'img_reg_sticker' => 'required',
			'img_car_photo' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		//if ($input['mode'] == "add") {
		$id = $request->input('devicetoken');
		$email = $request->input('email');
		$password = $request->input('password');
		$user_details["devicetoken"] = $id;
		$user_details["email"] = $email;
		$user_details["password"] = $password;
		$chk = $this->storeDriver($request);
		if (!$chk['exist']) {
			$driverObj = $this->getDriver($request);
			$response['result'] = $driverObj;
			$response['message'] = "Registered Successfully";
			$otp_enabled = \Config::get('constants.enable_otp');
			if ($otp_enabled) {
				send_sms("Your OTP is: " . $driverObj->otp, $driverObj->phone_number);
			}

			// $this->sendMail($request);
			return $response;
		} else {
			// $response['message'] = $chk;
			// $response['message'] = "User Email/Phone Number already Exists";
			// return $response;
			$response['message'] = 'email or phone number already exists.';
			return response()->json($response, 200);
		}
		/*} else {
			$response['message'] = "Undefined Mode";
			return $response;
		}*/
	}

	public function getDriver($request) {
		$email = $request->input('email');
		$result = Driver::where('email', $email)->first();

		$result->id_proof = env('IMG_URL') . $result->id_proof;
		$trimmed = str_replace('/public', '', $result->id_proof);
		$result['id_proof'] = $trimmed;

		$response = $result;
		return $response;
	}
	public function sendMail($request) {

		$email = $request->input('email');
		$user = Driver::where('email', '=', $email)->first();
		if (is_object($user)) {
			// $link = url("/api/email_verification/".$token);
			$username = Driver::select('name', 'otp')->where('email', '=', $email)->first();
			$name = $username->name;
			$otp = $username->otp;
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
			$content = Email::select('content')->where('template_name', '=', 'Driver Signup')->first();
			$content = str_replace('{{$otp}}', $otp, $content->content);

			$mail_header = array("name" => $name, "otp" => $otp, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content);
			Mail::send('mails.addDriver2', $mail_header, function ($message)
				 use ($user, $from_mail, $app_name) {
					$message->from($from_mail, $app_name);
					$message->subject('Registration');
					$message->to($user->email);

				});

			$response['message'] = "Mail sent successfully";
			return $response;
		} else {
			$response['message'] = "Invalid Mail id";
			return $response;
		}
	}

	public function storeDriver(Request $request) {
		$options = [
			'cost' => 12,
		];
		$input = $request->all();
		//$input['vehicle_type'] = $input['vehicle_id'];
		$digits = 4;
		$otp = $this->getOTP();
		$input['otp'] = $otp;
		$email = $request['email'];
		if ($request->hasFile('document')) {
			$file = $request->document;
			$photoName1 = 'uploads/DriverProof/' . time() . '_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
			$path = $request->document->move('uploads/DriverProof', $photoName1);
			$input['id_proof'] = $photoName1;
		}
		if ($request->hasFile('img_insurance')) {
			$file = $request->img_insurance;
			$photoName1 = 'uploads/DriverProof/' . time() . '_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
			$path = $request->img_insurance->move('uploads/DriverProof', $photoName1);
			$input['insurance'] = $photoName1;
		}
		if ($request->hasFile('img_reg_sticker')) {
			$file = $request->img_reg_sticker;
			$photoName1 = 'uploads/DriverProof/' . time() . '_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
			$path = $request->img_reg_sticker->move('uploads/DriverProof', $photoName1);
			$input['reg_sticker'] = $photoName1;
		}
		if ($request->hasFile('img_experience')) {
			$file = $request->img_experience;
			$photoName1 = 'uploads/DriverProof/' . time() . '_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
			$path = $request->img_experience->move('uploads/DriverProof', $photoName1);
			$input['experience'] = $photoName1;
		}
		if ($request->hasFile('img_car_photo')) {
			$file = $request->img_car_photo;
			$photoName1 = 'uploads/DriverProof/' . time() . '_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
			$path = $request->img_car_photo->move('uploads/DriverProof', $photoName1);
			$input['img_car_photo'] = $photoName1;
		}

		$check = $this->checkDriver($request);
		if (!$check['exist']) {
			$input['password'] = password_hash($request->password, PASSWORD_DEFAULT, $options);
			$input['status'] = 1;
			//$input['country']=$request->input('country_code');
			$user = Driver::create($input);
		}
		return $check;
	}

	public function checkDriver(Request $request) {
		$emailfromUser = $request->input('email');
		$phone_numberfromUser = $request->input('phone_number');
		$check_user = Driver::select('id')->where('email', '=', $emailfromUser)->orWhere('phone_number', '=', $phone_numberfromUser)->first();
		if (!empty($check_user)) {
			$response['code'] = 401;
			$response['message'] = 'email or phone number already exists.';
			$response['exist'] = true;
			return $response;
		} else {
			$response['message'] = '';
			$response['exist'] = false;
			return $response;
		}
	}

	public function changePassDriver(Request $request) {
		$options = [
			'cost' => 12,
		];
		$input = $request->all();
		$validator = Validator::make($input, [
			'id' => 'required',
			'password' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$user = Driver::where('id', $input['id'])->first();
		if (is_object($user)) {
			$input['password'] = password_hash($input["password"], PASSWORD_DEFAULT, $options);
			$user->password = $input['password'];
			if ($user->save()) {
				$response['message'] = "Success";
				return $response;
			} else {
				$response['code'] = 401;
				$response['message'] = 'Failure.';
				//$response['message'] = $message;
				return response()->json($response, 200);
			}
		} else {
			$response['code'] = 404;
			$response['message'] = 'Not Found.';
			//$response['message'] = $message;
			return response()->json($response, 200);
		}
	}
	public function forgetPasswordDriver(Request $request) {

		$email = $request->input('email');
		$user = Driver::where('email', '=', $email)->first();
		$digits = 4;

		if (is_object($user)) {
			// $link = url("/api/email_verification/".$token);
			$username = Driver::select('id', 'name', 'otp', 'email')->where('email', '=', $email)->first();
			$name = $username->name;
			//$otp = $username->otp;
			$otp = $this->getOTP();
			$username->otp = $otp;
			$username->save();
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
			$content = Email::select('content')->where('template_name', '=', 'Forgot Password')->first();
			$content = str_replace('{{$otp}}', $otp, $content->content);

			$mail_header = array("name" => $name, "otp" => $otp, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content);
			Mail::send('mails.forgotPassword', $mail_header, function ($message)
				 use ($user, $otp, $name, $from_mail, $app_name) {
					$message->from($from_mail, $app_name);
					$message->subject('Forgot Password');
					$message->to($user->email);

				});

			$response['message'] = "Mail sent successfully";
			$response['result'] = $username;
			return $response;
		} else {
			$response['message'] = "Invalid Mail id";
			return $response;
		}
	}
	public function forgetPasswordCustomer(Request $request) {

		$email = $request->input('email');
		$user = Customer::where('email', '=', $email)->first();
		$digits = 4;
		if (is_object($user)) {
			// $link = url("/api/email_verification/".$token);
			$username = Customer::select('id', 'name', 'otp', 'email')->where('email', '=', $email)->first();
			$name = $username->name;
			//$otp = $username->otp;
			$otp = $this->getOTP();
			$username->otp = $otp;
			$username->save();
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
			$content = Email::select('content')->where('template_name', '=', 'Forgot Password')->first();
			$content = str_replace('{{$otp}}', $otp, $content->content);
			//print_r($content);
			$mail_header = array("name" => $name, "otp" => $otp, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, "content" => $content);

			Mail::send('mails.forgotPassword', $mail_header, function ($message)
				 use ($user, $otp, $name, $app_name, $from_mail) {
					$message->from($from_mail, $app_name);
					$message->subject('Forgot Password');
					$message->to($user->email);
				});

			$response['message'] = "Mail sent successfully";
			$response['result'] = $username;
			return $response;
		} else {
			$response['message'] = "Invalid Mail id";
			return $response;
		}
	}

	public function logoutCustomer(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'user_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$customer = Customer::find($input['user_id']);
		if (is_object($customer)) {
			$customer->device_id = 0;
			$customer->update();
			$response['message'] = "Customer logout Successfull.";
		} else {
			$response['message'] = "Invalid user id.";
		}
		return $response;
	}

	public function logoutDriver(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'driver_id' => 'required',
		]);

		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . \Config::get('constants.firebase_key'));
		$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(\Config::get('constants.firebase_db'))->create();
		$database = $firebase->getDatabase();
		$appsetting = AppSetting::first();

		$customer = Driver::find($input['driver_id']);
		if (is_object($customer)) {
			$drivercheckin = DriverCheckin::where('driver_id', $input['driver_id'])->first();
			if (is_object($drivercheckin)) {
				$drivercheckin->checkin_status = 0;
				$drivercheckin->update();} // update checkin status in db
			$customer->device_id = 0;
			$customer->update(); /// update driver device id

			$response['message'] = "Driver logout Successfull.";

			/// update checkin status in firebase
			$location_update = [
				'drivers_location/' . $customer->vehicle_id . "/" . $input['driver_id'] . "/l/0" => "0",
				'drivers_location/' . $customer->vehicle_id . "/" . $input['driver_id'] . "/l/1" => "0",
			];
			$status_update = [
				'drivers_status/' . $input['driver_id'] . "/status" => "not available",
			];
			$newpost = $database->getReference() // this is the root reference
				->update($location_update);
			$newpost = $database->getReference() // this is the root reference
				->update($status_update);
		} else {
			$response['message'] = "Invalid driver id.";
		}
		return $response;
	}
	protected function verifyUser(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'phone_number' => 'required',
			'device_id' => 'required',
			'type' => 'required',
			'device' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$type = $request->input('type');
		$model = "App\Customer";
		if ($type == "driver") {
			$model = "App\Driver";
		}
		$user = $model::where('device_id', $request->input('device_id'))->where('phone_number', $request->input('phone_number'))->first();

		//Signin
		if ($user && $user->verified) {
			if ($type == "driver") {
				if (!$user->approved) {
					$response['code'] = 403;
					$response['message'] = 'User not approved.';
					return $response;
				}
			}
			$response['message'] = 200;
			return $response;
		}
		//OTP
		$user = $model::where('phone_number', $request->input('phone_number'))->first();
		if ($user) {
			if ($type == "driver") {
				if (!$user->approved) {
					$response['code'] = 403;
					$response['message'] = 'User not approved.';
					return $response;
				}
			}
			$digits = 4;
			$user->otp = $this->getOTP();
			$user->verified = 0;
			$user->device_id = $request->input('device_id');
			$user->save();
			$otp_enabled = \Config::get('constants.enable_otp');
			if ($otp_enabled) {
				send_sms("Your OTP is: " . $user->otp, $user->phone_number);
			}
			$response['message'] = 300;
			return $response;
		}
		$response['message'] = 100;
		return $response;

	}

	protected function verifyOtp(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'phone_number' => 'required',
			'otp' => 'required',
			'type' => 'required',
			'device_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$type = $request->input('type');
		$model = "App\Customer";
		if ($type == "driver") {
			$model = "App\Driver";
		}
		$user = $model::where('phone_number', $request->input('phone_number'))->first();
		if ($user) {
			if ($user->otp == $request->input('otp') && $user->device_id == $request->input('device_id')) {
				$user->verified = 1;
				$user->save();
				$response['message'] = "200";
				$response['result'] = $user;
				return $response;
			}
		}
		$response['message'] = "401";
		return $response;
	}

	private function getOTP() {
		// return 1111;
		$digits = 4;
		return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
	}
}