<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

use App\DriverCheckin;

use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class LocationController extends Controller {
	use ModelForm;

	public function index() {
		return Admin::content(function (Content $content) {
			// optional
			$content->header('Active Driver');

			// optional
			$content->description('Locations');
			$serviceAccount = ServiceAccount::fromJsonFile(public_path() .'/'. env('FIREBASE_KEY'));
			$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
			$database = $firebase->getDatabase();
			$newPost = $database->getReference('drivers_location');
			$locations = $newPost->getValue();
			$statuscheck = $database->getReference('drivers_status');
			$availabledrivers = $statuscheck->getValue();
			// $count = count($locations);
			
			$dat =  array();
            $i = 0;
            $count = 0;

            if($locations!='' && count($locations) > 0) {
            	foreach ($locations as $k => $driver) {
            		foreach($availabledrivers as $v => $data) {
						if(isset($locations[$k]) &&  $locations[$k]!='' && isset($locations[$k][$v])){
							$driv = DriverCheckin::where('driver_id',$v)->where('checkin_status','1')->count();
							if(isset($locations[$k][$v]['l'][0]) && $locations[$k][$v]['l'][0]!='' && isset($data['status']) && $data['status']=='available' && $driv!='0'){
								$count++;
								$temp = array();
					            $temp['driver_name'] = isset($data['fname']) ? $data['fname'] . ' ' . $data['lname'] : 'Name not found';
				                $temp['email'] = isset($data['email']) ? $data['email'] : '';
				                $temp['category'] = isset($data['category']) ? $data['category'] : '';
		                    
				                $temp['driver_current_lat'] = isset($locations[$k][$v]['l'][0]) ? $locations[$k][$v]['l'][0] : 0;
				                $temp['driver_current_lon'] = isset($locations[$k][$v]['l'][1]) ? $locations[$k][$v]['l'][1] : 0;
			                   	$temp['service_status'] = isset($data['status']) ? $data['status'] : 0;

			                   	$temp['status'] = isset($locations[$k][$v]['status']) ? $locations[$k][$v]['status'] : 0;
			                   	$dat['latlon'][$i] = $temp;						
			                   	$i++;
		                	}
						}
            		}
	            }
        	}

            if($count=='0'){
            	$dat['latlon']  = array();
            }		

			/*if ($count > 0) {
				$finalData = array();
				$i = "0";
				$driverids = array();
				$drivername = array();
				$catgeoryall = array();
				$firebasecategory = array();
				foreach ($locations as $category => $value) {
					$catgeoryall[] = '"' . $category . '"';
					if ($value != '') {
						foreach ($value as $key => $var) {
							$c = array_key_exists($key, $avali_only);
							if (array_key_exists($key, $avali_only)) {
								$status_check_all = isset($avali_only[$key]['status']) ? $avali_only[$key]['status'] : 0;
								if (isset($var)) {
									foreach ($var as $set => $finalresult) {
										foreach ($avali_only as $key => $okstatus) {
											if (isset($okstatus['status']) && $okstatus['status'] == 'available') {
												$drivername[] = '"' . $avali_only[$key]['fname'] . '"';
												$firebasecategory[$i] = $category;
												$driverids[] = '"' . $key . '"';
												if ($set == "l") {
													if ($finalresult['0'] != "0.0" && $finalresult['1'] != "0.0") {
														$finalData[$i][] = $finalresult['0'];
														$finalData[$i][] = $finalresult['1'];
														$i++;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
				$finalData = json_encode($finalData);
				$driverid = implode(',', $driverids);
				$cat_implode = implode(',', $catgeoryall);
				$All_driver_Name = implode(',', $drivername);
			}

			$data['cat_implode'] = $cat_implode;
			$data['finalData'] = $finalData;
			$data['driverid'] = $driverid;
			$data['All_driver_Name'] = $All_driver_Name;*/
		

			//echo "<pre>";print_r($data); exit;
			$content->body(view('Admin.location.live_map',$dat));
		});
	}
}