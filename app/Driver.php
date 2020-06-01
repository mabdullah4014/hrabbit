<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model {
	protected $fillable = [
		'name', 'last_name', 'password', 'email', 'country_name', 'user_photo', 'vehicle_id', 'phone_number', 'device_id', 'vin_num', 'vehicle_type', 'license_no', 'city', 'address', 'otp', 'id_proof', 'experience', 'insurance', 'reg_sticker', 'referral_code', 'vehicle_color_id', 'vehicle_make_id', 'vehicle_model_id', 'approved', 'img_car_photo',
	];

	public function DriverCheckin() {
		return $this->hasMany(DriverCheckin::class);
	}

	public function payout() {
		return $this->hasOne(BankPayouts::class);
	}

	public function paypal() {
		return $this->hasOne(PaypalPayout::class);
	}

	public function trip() {
		return $this->hasMany(DriverTrips::class);
	}
	public function hide_email($email) {
		$em = explode("@", $email);
		$name = $em[0];
		$len = strlen($name);
		$showLen = floor($len / 4);
		$str_arr = str_split($name);
		for ($ii = $showLen; $ii < $len; $ii++) {
			$str_arr[$ii] = '*';
		}
		$em[0] = implode('', $str_arr);
		return $hidden_email = implode('@', $em);
	}
	public function hide_phone($phone) {
		$times = strlen(trim(substr($phone, 4, 5)));
		$star = '';
		for ($i = 0; $i < $times; $i++) {
			$star .= '*';
		}
		return $star;
	}

}
