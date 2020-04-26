<?php

namespace App\Admin\Extensions;

use Admin;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class DriverWiseEarning extends AbstractExporter {
	public function export() {

		Excel::create('driver_wise_earning', function ($excel) {

			$excel->sheet('Sheetname', function ($sheet) {

				// This logic get the columns that need to be exported from the table data
				$sheet->row(1, ['S.No', 'Trip Number', 'Driver First Name', 'Driver Last Name', 'Trip Date', 'Pickup Location', 'Drop Location', 'Driver Earned Amount', 'Admin Commission', 'Total Amount','Trip Status']);
				$sheet->row(2, ['', '', '', '', '', '', '', '', '','','']);

				$rows = collect($this->getData())->map(function ($item, $key) {
					$status = $item['status'];
						if  ($status == 3) {
							$item['status'] ='Trip Started';
						}
						elseif ($status == 4) {
							$item['status']='Trip Completed';
						}
						elseif ($status == 6) {
							$item['status']='Payment Completed';
						}
					$data['s.no'] = $key + 1;
					$data['trip_number'] = $item['trip_num'];
					$data['driver_first_name'] = $item['driver_name'];
					$data['driver_last_name'] = $item['driver_lname'];
					$data['trip_date'] = $item['today_date'];
					$data['pick_up_location'] = $item['pick_up'];
					$data['drop_location'] = $item['drop_location'];
					$data['driver_Amount'] = $item['total_amount'] - ($item['commission']);
					$data['site_commission'] = $item['commission'];
					$data['total_amount'] = $item['total_amount'];
					$data['status'] = $item['status'];
					return $data;

					//return array_only($item, ['first_name','last_name','phone','vehicle_number','license_number','status','added_by']);
				});

				$sheet->rows($rows);

			});

		})->export('csv');
	}

}