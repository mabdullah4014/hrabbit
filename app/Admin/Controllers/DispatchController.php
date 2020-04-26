<?php

namespace App\Admin\Controllers;

use App\User;
use App\Driver;
use App\Customer;

use App\State;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
class DispatchController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $data['phone_numbers'] = DB::table('customers')->select('phone_number')->get();
            $data['vehicle'] = DB::table('vehicle_categories')->pluck('vehicle_type','id');
            $content->header('Dispatch System');
            $content->description('Dashboard');
            $content->body(view('dispatch',['data' => $data]));
        });
    }

    // public function show(){

    // }

    public function load_customer(Request $request){
        $customer = DB::table('customers')->where('phone_number',$request->phone)->first();
        echo json_encode($customer);
    }
    
    public function load_email(Request $request){
        $customer = DB::table('customers')->where('email',$request->email)->first();
        if(is_object($customer)){
            echo json_encode($customer);
        } else {
           echo 'Failure';
        }
    }

    public function load_phone(Request $request){
        $customer = DB::table('customers')->where('phone_number','like','%'.$request->phone.'%')->first();
        if(is_object($customer)){
            echo json_encode($customer);
        } else {
            echo 'Failure';
        }
    }    


    public function load_number(Request $request){
        $customer = DB::table('customers')->where('phone_number',$request->phone)->first();
        if(is_object($customer)){
            echo json_encode($customer);
        } else {
            echo 'Failure';
        }
    }


    public function drivercheck(Request $request){

        $input  =  $request->all();
        $data['latitude']   = $input['pickup_lat'];
        $data['longitude']  = $input['pickup_lon'];
        // $data['vehicle_id'] = 1;
        $data['vehicle_id'] = $input['vehicle_id'];
        $drive_list = $this->available_drivers($data);

        if(!empty($drive_list)){
            $data = "<option value=''> --Not Selected--</option>";
            foreach ($drive_list as $key => $value) {
              $data.="<option value='".$value['id']."'>".$value['name'].' '.$value['last_name']."</option>";
            }
            echo $data;
        } else {
            $data = "<option value=''> --Drivers not available--</option>";
            echo $data;
        }
    }

    protected function available_drivers($data) {
        $c_lat = $data['latitude'];
        $c_lon = $data['longitude'];
        $result = array();
        //$drivers = DriverCheckin::where('vehicle_id', $data['vehicle_id'])->where('checkin_status', 1)->where('booking_status', 0)->get();
        //$data['message'] = "New Booking";
        $serviceAccount = ServiceAccount::fromJsonFile(config_path(env('FIREBASE_KEY')));
        $firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
        $database = $firebase->getDatabase();
        $drivers = $database->getReference('drivers_location/'.$data['vehicle_id'])->getValue();
        //print_r($drivers);exit;
        //$unit = "K";
        $driverLists = [];

        if(is_array($drivers)){
            foreach ($drivers as $key => $driver) {
 
                $driver_profile=Driver::where('id',$key)->first();
                
                if(is_object($driver_profile)){

                    if($driver_profile->status==1) {

                        $radius = DB::table('app_settings')->where('id','1')->value('radius');

                        if($radius!='' && $radius!='0'){
                            $radius = $radius;
                        } else {
                            $radius = 5;
                        }
                        
                        if($driver != ""){
                            if(@$driver['status'] == 1 && $driver['l'][0] != 0 && $driver['l'][1] != 0){
                                $distance = $this->getDistance( $c_lat, $c_lon, $driver['l'][0], $driver['l'][1] );
                                if($distance <= $radius){
                                    $result[] = $driver_profile->toArray();
                                }
                            }
                        }
                    }   
                }
            }
        }

        return $result;
    }

    public function getDistance( $latitude1, $longitude1, $latitude2, $longitude2 ) {  
        $earth_radius = 6371;

        $dLat = deg2rad( $latitude2 - $latitude1 );  
        $dLon = deg2rad( $longitude2 - $longitude1 );  

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
        $c = 2 * asin(sqrt($a));  
        $d = $earth_radius * $c;  

        return $d;  
    }

// Request $request
    public function getphone(){
       $q = $request->get('query');
       return  Customer::where('phone_number','like','%'.$q.'%')->get(['phone_number as data', DB::raw('phone_number as value')]);
       exit;
    }

    public function getemail(Request $request){
       $q = $request->get('query');
       return  Customer::where('email','like','%'.$q.'%')->get(['email as data', DB::raw('email as value')]);
       exit;
    }
}
