<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleCategory extends Model
{
    public function DriverCheckin()
    {
        return $this->hasMany(DriverCheckin::class);
    }    

   public function TripVehicle()
    {
        return $this->hasMany(DriversTrips::class);
    }
}
