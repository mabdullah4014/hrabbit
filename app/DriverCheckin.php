<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverCheckin extends Model
{

    protected $fillable = [
        'driver_id', 'd_lat', 'd_lon','today_date','vehicle_type','vehicle_id','checkin_status','checkin_time','booking_status','waiting_status','name','lname','updated_at','created_at','otp','salt'
    ];
    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function Category()
    {
        return $this->belongsTo(VehicleCategory::class);
    }
}
