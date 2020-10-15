<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefundTransaction extends Model
{
    protected $table = 'refund_transactions';
	protected $fillable = [
		'settled', 'transaction_id', 'trip_id','amount','retries'
	];
	public function trip() {
		return $this->belongsTo(DriverTrip::class, 'trip_id');
	}
}
