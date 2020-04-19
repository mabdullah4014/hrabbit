<?php

namespace App\Http\Controllers;

use App\Country;
use App\Customer;
use App\CustomerFeedback;
use App\State;
use App\Url;
use App\VehicleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
// use PayPal\Api\URL;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

class CustomerController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
// 	public function __construct() {
	// /** PayPal api context **/
	// 		$paypal_conf = \Config::get('paypal');
	// 		$this->_api_context = new ApiContext(new OAuthTokenCredential(
	// 			$paypal_conf['client_id'],
	// 			$paypal_conf['secret'])
	// 		);
	// 		$this->_api_context->setConfig($paypal_conf['settings']);
	// 	}
	public function viewCustomer(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$profile = Customer::where('id', $input['id'])->first();
		$profile->user_photo = env('IMG_URL') . $profile->user_photo;
		$trimmed = str_replace('/public', '', $profile->user_photo);

		$profile['user_photo'] = $trimmed;
		$country_id = $profile->country;
		$state_id = $profile->state;
		$country = Country::where('id', $country_id)->first();
		if (is_object($country)) {
			$country_name = $country->name;

		} else {
			$country_name = "";
		}

		$state = State::where('id', $state_id)->first();
		if (is_object($state)) {
			$state_name = $state->state_name;

		} else {
			$state_name = "";
		}

		if (is_object($profile)) {
			$profile['country_name'] = $country_name;
			$profile['state_name'] = $state_name;
			$profile['dob'] = $profile->cus_dob;
			$response['result'] = $profile;
			$response['message'] = 'Success';
			return response()->json($response, 200);
		} else {
			$response['code'] = 404;
			$response['message'] = 'No Profile Found.';
			//$response['message'] = $message;
			return response()->json($response, 200);
		}
	}

	public function sendError($message) {
		$message = [
			'code' => 401,
			'error' => $message,
		];
		$response['message'] = $message;
		return response()->json($response, 200);
	}

	public function updateCustomer(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$profile = Customer::where('id', $input['id'])->first();
		if (is_object($profile)) {
			$profile->name = isset($input['name']) ? $input['name'] : $profile->name;
			$profile->last_name = isset($input['last_name']) ? $input['last_name'] : $profile->last_name;
			$profile->cus_dob = isset($input['cus_dob']) ? $input['cus_dob'] : $profile->cus_dob;
			$profile->state = isset($input['state']) ? $input['state'] : $profile->state;
			$profile->country = isset($input['country']) ? $input['country'] : $profile->country;
			$profile->postal_code = isset($input['postal_code']) ? $input['postal_code'] : $profile->postal_code;
			$profile->address = isset($input['address']) ? $input['address'] : $profile->address;
			$profile->city = isset($input['city']) ? $input['city'] : $profile->city;
			if ($profile->save()) {
				$response['result'] = $profile;
				$response['message'] = 'Profile updated successfully.';
				return response()->json($response, 200);
			} else {
				$response['code'] = 403;
				$response['message'] = 'Update Failure.';
				//$response['message'] = $message;
				return response()->json($response, 200);
			}
		} else {
			$response['code'] = 404;
			$response['message'] = 'No Profile Found.';

			return response()->json($response, 200);
		}
	}

	public function uploadImage(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'id' => 'required',
			'avatar' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$customer = Customer::find($request->input('id'));
		if (is_object($customer)) {
			$file = $request->file('avatar');
			$path = $request->avatar->move('uploads/CustomerImages');
			$prefix = 'downloads/';
			if (substr($path, 0, strlen($prefix)) == $prefix) {
				$path = substr($path, strlen($prefix));
			}
			$customer->user_photo = $path;
			$customer->save();
			$customer->user_photo = env('IMG_URL') . 'public/' . $customer->user_photo;
			$trimmed = str_replace('/public', '', $customer->user_photo);
			//$trimmed1 = str_replace('/uploads', '', $trimmed);
			$customer['user_photo'] = $trimmed;
			$response['result'] = $customer;
			$response['message'] = 'image uploaded.';
			return response()->json($response, 200);
		} else {
			$response['code'] = 404;
			$response['message'] = 'No Profile Found.';
			//$response['message'] = $message;
			return response()->json($response, 200);
		}
	}

	public function addFeedback(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'driver_id' => 'required',
			'customers_id' => 'required',
			'driver_rating' => 'required',
			'cab_rating' => 'required',
			'overall_rating' => 'required',
			'comments' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$input['status'] = 1;
		$input['comments'] = str_replace("%20", " ", $input['comments']);
		$feedback = CustomerFeedback::create($input);
		if ($feedback) {
			$response['message'] = 'feedback saved.';
			return response()->json($response, 200);
		} else {
			$response['code'] = 403;
			$response['message'] = 'feedback not saved.';
			//$response['message'] = $message;
			return response()->json($response, 200);
		}
	}

	public function vehicleCategory(Request $request) {
		$category = VehicleCategory::where('status', 1)->get();
		if (count($category) > 0) {
			$response['result'] = $category;
			$response['message'] = 'category listed successfully.';
			return response()->json($response, 200);
		} else {
			$response['code'] = 404;
			$response['message'] = 'No Category Found.';
			//$response['message'] = $message;
			return response()->json($response, 200);
		}
	}

	public function payWithpaypal(Request $request) {
		$payer = new Payer();
		$payer->setPaymentMethod('paypal');
		$item_1 = new Item();
		$item_1->setName('Item 1') /** item name **/
			->setCurrency('USD')
			->setQuantity(1)
			->setPrice($request->get('amount')); /** unit price **/
		$item_list = new ItemList();
		$item_list->setItems(array($item_1));
		$amount = new Amount();
		$amount->setCurrency('USD')
			->setTotal($request->get('amount'));
		$transaction = new Transaction();
		$transaction->setAmount($amount)
			->setItemList($item_list)
			->setDescription('Your transaction description');
		$redirect_urls = new RedirectUrls();
		$redirect_urls->setReturnUrl(URL::route('status')) /** Specify return URL **/
			->setCancelUrl(URL::route('status'));
		$payment = new Payment();
		$payment->setIntent('Sale')
			->setPayer($payer)
			->setRedirectUrls($redirect_urls)
			->setTransactions(array($transaction));
		/** dd($payment->create($this->_api_context));exit; **/
		try {
			$payment->create($this->_api_context);
		} catch (\PayPal\Exception\PPConnectionException $ex) {
			if (\Config::get('app.debug')) {
				\Session::put('error', 'Connection timeout');
				return Redirect::route('paywithpaypal');
			} else {
				\Session::put('error', 'Some error occur, sorry for inconvenient');
				return Redirect::route('paywithpaypal');
			}
		}
		foreach ($payment->getLinks() as $link) {
			if ($link->getRel() == 'approval_url') {
				$redirect_url = $link->getHref();
				break;
			}
		}
		/** add payment ID to session **/
		Session::put('paypal_payment_id', $payment->getId());
		if (isset($redirect_url)) {
			/** redirect to paypal **/
			return Redirect::away($redirect_url);
		}
		\Session::put('error', 'Unknown error occurred');
		return Redirect::route('paywithpaypal');
	}

	public function getPaymentStatus() {
		/** Get the payment ID before session clear **/
		$payment_id = Session::get('paypal_payment_id');
		/** clear the session payment ID **/
		Session::forget('paypal_payment_id');
		if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {
			\Session::put('error', 'Payment failed');
			return Redirect::route('/');
		}
		$payment = Payment::get($payment_id, $this->_api_context);
		$execution = new PaymentExecution();
		$execution->setPayerId(Input::get('PayerID'));
		/**Execute the payment **/
		$result = $payment->execute($execution, $this->_api_context);
		if ($result->getState() == 'approved') {
			\Session::put('success', 'Payment success');
			return Redirect::route('/');
		}
		\Session::put('error', 'Payment failed');
		return Redirect::route('/');
	}

}
