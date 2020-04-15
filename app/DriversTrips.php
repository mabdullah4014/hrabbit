<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriversTrips extends Model {
	protected $table = 'driver_trips';
	public function driver() {
		return $this->belongsTo(Driver::class);
	}

	public function vehicle() {
		return $this->belongsTo('App\VehicleCategory', 'vehicle_type');
	}
}
