<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankPayouts extends Model {
	protected $table = 'bank_payouts';
	protected $fillable = [
		'type', 'bank_email', 'account_num', 'bank_code', 'bank_fname', 'bank_lname', 'bank_email', 'bank_dob', 'bank_phone', 'driverid', 'address', 'otp', 'id_proof',
	];
	public function driver() {
		return $this->belongsTo(Driver::class, 'driverid');
	}
}
