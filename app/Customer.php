<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'last_name', 'password', 'email', 'user_photo', 'phone_number', 'cus_dob', 'state', 'country', 'postal_code', 'city', 'address', 'otp', 'salt', 'customerProfileId', 'customerPaymentProfileId', 'device_id', 'points',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'remember_token', // 'password',
	];
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
