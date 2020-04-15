<?php

namespace App\Http\Controllers;

use App\BankPayouts;
use App\Country;
use App\DriverPayout;
use App\CustomerFeedback;
use App\Driver;
use App\DriverCheckin;
use App\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function viewDriver(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}

		$profile = Driver::where('id', $input['id'])->first();
		if (is_object($profile)) {
			$rating = DB::table('customer_feedbacks')
				->where('driver_id', $input['id'])
				->avg('driver_rating');
			$avg_rating = round($rating, 1);
			$country_id = $profile->country_name;
			$state_id = $profile->state;
			$dob = $profile->dob;
			$country1 = Country::where('id', $country_id)->first();
			if (is_object($country1)) {
				//$profile['country'] = $country1->country_name;
				$country_name = $country1->name;

			} else {
				$country_name = "";
			}
			$state = State::where('id', $state_id)->first();
			if (is_object($state)) {
				$state_name = $state->state_name;

			} else {
				$state_name = "";
			}
			$profile['country_id'] = $country_id;
			$profile['country_name'] = $country_name;
			$profile['state_name'] = $state_name;
			$profile->photo = env('IMG_URL')  . $profile->photo;
			$trimmed = str_replace('/public', '', $profile->photo);

			// $trim='/storage'.$profile->photo;
			// $external_link       = env('APP_URL').  $profile->photo;
        	// //$external_link_admin = env('APP_URL') .$profile->photo;
			// if (@getimagesize($external_link)) {
			// 	$res['photo'] = env('APP_URL') .;
			// } else {
			// 	$res['photo'] = $external_link;
			// }


			$profile['photo'] = $trimmed;
			$profile['rating'] = $avg_rating;
			$response['result'] = $profile;
			$response['message'] = 'Profile listed successfully.';
			return response()->json($response, 200);

		}

		$response['message'] = 'failure.';
		return response()->json($response, 200);
	}
	public function viewBank(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'driver_id' => 'required',
			'type' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		if ($input['type'] == "bank" || $input['type'] == "paypal") {
			$bank_details = BankPayouts::where('driverid', $input['driver_id'])->where('type', $input['type'])->first();
			if (is_object($bank_details)) {
				// foreach ($bank_details as $key ) {
				//     $new[]=$key;
				// }
				$response['bank_details'] = $bank_details;
				$response['message'] = 'success';
				return response()->json($response, 200);
			}
			$response['message'] = 'failure';
			return response()->json($response, 200);
		} else {
			$response['message'] = 'Undefined Mode';
			return response()->json($response, 200);
		}
	}
	public function addBank(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'driverid' => 'required',
			'type' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}

		if ($input['type'] == "bank") {
			$validator = Validator::make($input, [
				'driverid' => 'required',
			]);

			if ($validator->fails()) {
				return $this->sendError('Invalid Params.', $validator->errors());
			}
			$bank_detailCheck = BankPayouts::where('driverid', $input['driverid'])->where('type', '=', 'bank')->first();
			if (is_object($bank_detailCheck)) {
				$bank_detailCheck->bank_email = isset($input['bank_email']) ? $input['bank_email'] : $bank_detailCheck->bank_email;
				$bank_detailCheck->account_num = isset($input['account_num']) ? $input['account_num'] : $bank_detailCheck->account_num;
				$bank_detailCheck->bank_code = isset($input['bank_code']) ? $input['bank_code'] : $bank_detailCheck->bank_code;
				$bank_detailCheck->bank_fname = isset($input['bank_fname']) ? $input['bank_fname'] : $bank_detailCheck->bank_fname;
				$bank_detailCheck->bank_lname = isset($input['bank_lname']) ? $input['bank_lname'] : $bank_detailCheck->bank_lname;
				$bank_detailCheck->bank_dob = isset($input['bank_dob']) ? $input['bank_dob'] : $bank_detailCheck->bank_dob;
				$bank_detailCheck->bank_phone = isset($input['bank_phone']) ? $input['bank_phone'] : $bank_detailCheck->bank_phone;
				$bank_detailCheck->bankname = isset($input['bankname']) ? $input['bankname'] : $bank_detailCheck->bankname;
				if ($bank_detailCheck->update()) {
					$response['result'] = $bank_detailCheck;
					$response['message'] = 'success';
					return response()->json($response, 200);
				}

			} else {
				$bank_payouts = new BankPayouts;

		        //$bank_payouts->name = $request->name;
		        $bank_payouts->bank_email = $input['bank_email'];
		        $bank_payouts->account_num = $input['account_num'];
		        $bank_payouts->bank_code = $input['bank_code'];
		        $bank_payouts->type = $input['type'];
		        $bank_payouts->bank_fname = $input['bank_fname'];
		        $bank_payouts->bank_lname = $input['bank_lname'];
		        $bank_payouts->bank_dob = $input['bank_dob'];
		        $bank_payouts->bank_phone = $input['bank_phone'];
		        $bank_payouts->bankname = $input['bankname'];
		        $bank_payouts->driverid = $input['driverid'];
		        $bank_payouts->created = date('Y-m-d H:i:s');
		        $bank_payouts->save();
		        $bank_detail = BankPayouts::where('driverid',$input['driverid'])->first();
				//$bank_detail = BankPayouts::create($input);
				if ($bank_detail) {
					$response['result'] = $bank_detail;
					$response['message'] = 'success';
					return response()->json($response, 200);
				} else {
					$response['message'] = 'failure';
					return response()->json($response, 200);
				}
			}

		} elseif ($input['type'] == "paypal") {
			{
				$validator = Validator::make($input, [
					'bank_email' => 'required',
				]);
				if ($validator->fails()) {
					return $this->sendError('Invalid Params.', $validator->errors());
				}
				$bank_detailCheck = BankPayouts::where('driverid', $input['driverid'])->where('type', '=', 'paypal')->first();
				if (is_object($bank_detailCheck)) {
					$bank_detailCheck->bank_email = isset($input['bank_email']) ? $input['bank_email'] : $bank_detailCheck->bank_email;
					$bank_detailCheck->account_num = isset($input['account_num']) ? $input['account_num'] : $bank_detailCheck->account_num;
					$bank_detailCheck->bank_code = isset($input['bank_code']) ? $input['bank_code'] : $bank_detailCheck->bank_code;
					$bank_detailCheck->bank_fname = isset($input['bank_fname']) ? $input['bank_fname'] : $bank_detailCheck->bank_fname;
					$bank_detailCheck->bank_lname = isset($input['bank_lname']) ? $input['bank_lname'] : $bank_detailCheck->bank_lname;
					$bank_detailCheck->bank_dob = isset($input['bank_dob']) ? $input['bank_dob'] : $bank_detailCheck->bank_dob;
					$bank_detailCheck->bank_phone = isset($input['bank_phone']) ? $input['bank_phone'] : $bank_detailCheck->bank_phone;
					$bank_detailCheck->bankname = isset($input['bankname']) ? $input['bankname'] : $bank_detailCheck->bankname;
					if ($bank_detailCheck->update()) {
						$response['result'] = $bank_detailCheck;
						$response['message'] = 'success';
						return response()->json($response, 200);
					}

				} else {
					$bank_detail = BankPayouts::create($input);
					if ($bank_detail) {
						$response['result'] = $bank_detail;
						$response['message'] = 'success';
						return response()->json($response, 200);
					} else {
						$response['message'] = 'failure';
						return response()->json($response, 200);
					}
				}
			}
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

	public function updateDriver(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$profile = Driver::where('id', $input['id'])->first();
		if (is_object($profile)) {
			$profile->name = isset($input['name']) ? $input['name'] : $profile->name;
			$profile->last_name = isset($input['last_name']) ? $input['last_name'] : $profile->last_name;
			$profile->dob = isset($input['dob']) ? $input['dob'] : $profile->dob;
			$profile->state = isset($input['state']) ? $input['state'] : $profile->state;
			$profile->country_name = isset($input['country_name']) ? $input['country_name'] : $profile->country_name;
			$profile->postal_code = isset($input['postal_code']) ? $input['postal_code'] : $profile->postal_code;
			$profile->address = isset($input['address']) ? $input['address'] : $profile->address;
			$profile->city = isset($input['city']) ? $input['city'] : $profile->city;
			$profile->vehicle_type = isset($input['vehicle_type']) ? $input['vehicle_type'] : $profile->vehicle_type;
			$profile->vehicle_id = isset($input['vehicle_id']) ? $input['vehicle_id'] : $profile->vehicle_id;
			$profile->license_no = isset($input['license_no']) ? $input['license_no'] : $profile->license_no;
			$profile->vehicle_num = isset($input['vehicle_num']) ? $input['vehicle_num'] : $profile->vehicle_num;
			$profile->default_payment=isset($input['default_payment']) ? $input['default_payment'] : $profile->default_payment;
			if ($profile->save()) {
				$checkin = DriverCheckin::where('driver_id', $input['id'])->first();
				if (is_object($checkin)) {
					$checkin->vehicle_type = isset($input['vehicle_type']) ? $input['vehicle_type'] : $checkin->vehicle_type;
					$checkin->vehicle_id = isset($input['vehicle_id']) ? $input['vehicle_id'] : $checkin->vehicle_id;
					$checkin->save();
				}
				$response['result'] = $profile;
				$response['message'] = 'Profile updated successfully.';
				return response()->json($response, 200);
			} else {
				$response['code'] = 403;
				$response['message'] = 'Update Failure.';
				// $response['message'] = $message;
				return response()->json($response, 200);
			}
		} else {

			$response['code'] = 404;
			$response['message'] = 'No Profile Found.';
			//$response['message'] = $message;
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
		$driver = Driver::find($request->input('id'));
		if (is_object($driver)) {
			$file = $request->file('avatar');
			$path = $request->avatar->move('uploads/DriverImages');
			$prefix = 'downloads/';
			if (substr($path, 0, strlen($prefix)) == $prefix) {
				$path = substr($path, strlen($prefix));
			}
			$driver->photo = $path;
			$driver->save();
			$driver->photo = env('IMG_URL')  . $driver->photo;
			$trimmed = str_replace('/public', '', $driver->photo);
			//$trimmed1 = str_replace('/uploads', '', $trimmed);
			$driver['photo'] = $trimmed;
			$response['result'] = $driver;
			$response['message'] = 'driver image updated successfully.';
			return response()->json($response, 200);
		} else {
			$response['code'] = 404;
			$response['message'] = 'No Profile Found.';
			// $response['message'] = $message;
			return response()->json($response, 200);
		}
	}

	public function uploadProof(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'id' => 'required',
			'proof' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$driver = Driver::find($request->input('id'));
		if (is_object($driver)) {
			$file = $request->file('proof');
			$path = $request->proof->store('DriverProof');
			$prefix = 'downloads/';
			if (substr($path, 0, strlen($prefix)) == $prefix) {
				$path = substr($path, strlen($prefix));
			}
			$driver->id_proof = $path;
			$driver->save();
			$driver->id_proof = env('IMG_URL') . $driver->id_proof;
			$response['result'] = $driver;
			$response['message'] = 'driver proof updated successfully.';
			return response()->json($response, 200);
		} else {
			$response['code'] = 404;
			$response['message'] = 'No Profile Found.';
			//  $response['message'] = $message;
			return response()->json($response, 200);
		}
	}
	public function viewFeedback(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->sendError('Invalid Params.', $validator->errors());
		}
		$feedback = CustomerFeedback::where('driver_id', $input['id'])->get();
		if (is_object($feedback)) {

			$response['result'] = $feedback;
			$response['message'] = 'feedback listed successfully.';
			return response()->json($response, 200);
		} else {
			$response['code'] = 404;
			$response['message'] = 'No feedback Found.';
			// $response['message'] = $message;
			return response()->json($response, 200);
		}
	}

	public function mass_payment(Request $request){

        foreach ($request->input('ids') as $key => $id) {
       
       
        /********************************/
        $sender_batch_id = "90205".time() + $id;
        $email_subject = "You have a payout!";
        $email_message = "You have received a payout! Thanks for using our service!";
        
        /********************************/
        $receivers = array();
        $recipient_type = "EMAIL";
        $value = $this->get_driver_wallet($id);
        $currency = "USD";
        $note = "Thanks for your patronage!";
        $sender_item_id = "90206".time() + $id;
        $receiver = $this->getPaypalEmail($id);
       
        /********************************/
        if($receiver != "" && $value > 0){
        	$access_token = "A21AAHqjQV4iyfJGz2g5sXjjq_Y_rQppM2llk9OsMXzVkBfTIyOUwCuzxVzS12B6JUXoZWBuxGL9yBQoTcFsMBgP6NpezRogw";
        
	        //print_r($post_data);exit;
	        $curl = curl_init();

	          curl_setopt_array($curl, array(
	          CURLOPT_URL => "https://api.sandbox.paypal.com/v1/payments/payouts",
	          CURLOPT_RETURNTRANSFER => true,
	          CURLOPT_ENCODING => "",
	          CURLOPT_MAXREDIRS => 10,
	          CURLOPT_TIMEOUT => 30,
	          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	          CURLOPT_CUSTOMREQUEST => "POST",
	          CURLOPT_POSTFIELDS => "{\n\"sender_batch_header\": {\n\"sender_batch_id\": \"$sender_batch_id\",\n\"email_subject\": \"$email_subject\",\n\"email_message\": \"$email_message\"\n},\n\"items\": [\n{\n\"recipient_type\": \"$recipient_type\",\n\"amount\": {\n\"value\": \"$value\",\n\"currency\": \"$currency\"\n},\n\"note\": \"$note\",\n\"sender_item_id\": \"$sender_item_id\",\n\"receiver\": \"$receiver\"\n}\n]\n}",
	          //CURLOPT_POSTFIELDS => $post_params,
	          CURLOPT_HTTPHEADER => array(
	            "authorization: Bearer $access_token",
	            "cache-control: no-cache",
	            "content-type: application/json",
	            "postman-token: cb8a6de6-26b2-cf9e-213b-38392376a5d8"
	          ),
	        ));

	        $response = curl_exec($curl);
	        $err = curl_error($curl);

	        curl_close($curl);

	        if ($err) {
	          echo "cURL Error #:" . $err;
	        } else {
	          $results = json_decode($response);
	          if($results->batch_header->payout_batch_id){
	             if($this->update_wallet_details($id,$value,$results->batch_header->payout_batch_id,$results)){
	                    $wallet = $this->get_driver_wallet($id);
	                    $deduct_amount = $wallet - $value;
	                    $this->update_wallet_amount($id,$deduct_amount);
	                    //admin_toastr('Successfully Paid', 'success');
	                    //return redirect('/admin/pay_to_driver');
	                    
	             }
	          }else{
	            //admin_toastr('Something went wrong', 'warning');
	            //return redirect('/admin/pay_to_driver');
	          }
	        }
        }
        
        

        }
        return 1;
    }

    public function get_driver_wallet($id){
        return Driver::where('id',$id)->value('wallet');
    }

     public function getPaypalEmail($id){
        return BankPayouts::where('type','paypal')->where('driverid',$id)->value('bank_email');
    }

    public function update_wallet_amount($driver_id,$amount){
        Driver::where('id',$driver_id)->update([
            'wallet'=>$amount
        ]);
    }

    public function update_wallet_details($id,$amount,$ref,$results){
        $driver_payout = new DriverPayout;
        $driver_payout->driver_id = $id;
        $driver_payout->amount = $amount;
        $driver_payout->type = 'paypal';
        $driver_payout->ref_no = $ref;
        $driver_payout->date = date('Y-m-d H:i:s');
        $driver_payout->status = 1;
        $driver_payout->paypal_results = serialize($results);
        $driver_payout->save();
        return TRUE;
    }
}
