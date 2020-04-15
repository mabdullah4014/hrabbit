<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model {

	protected $fillable = [
		'driver_id', 'customers_id', 'driver_rating', 'cab_rating', 'overall_rating', 'comments', 'status',
	];

	public function customer() {
		return $this->belongsTo(Customer::class, 'customers_id');
	}

	public function driver() {
		return $this->belongsTo(Driver::class, 'driver_id');
	}
}
