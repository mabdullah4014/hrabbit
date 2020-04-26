<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverTrip extends Model
{
  protected $fillable = [
        'name','last_name','phone_number','referral_code','invite_code', 'email', 'password','country_code','wallet_balance','status','device_token',"currency",'language','otp','user_photo','phone','dob','state','country_name','postal_code','address','city','devicetoken','device_id','created_at','modified','status','vehicle_num','vehicle_type','vehicle_id','license_no','id','updated_at','driver_id','cus_id','booking_id','customer_name','customer_lname','trip_num','today_date','ride_date','ride_time','pick_up','pick_up_time','pick_time','drop_location','vehicle_type','price_km','driver_id','driver_name','driver_lname','drop_lat','drop_lon','pickup_lat','pickup_lon','customer_num','phone_num','added_by'];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class,'cus_id');
    }  
}
