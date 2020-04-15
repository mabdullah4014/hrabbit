<?php

namespace App\Admin\Extensions;

use Admin;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class DriverCheckinExport extends AbstractExporter {
	public function export() {
		Excel::create('drivers_checkin_details', function ($excel) {

			$excel->sheet('Sheetname', function ($sheet) {

				// This logic get the columns that need to be exported from the table data
				$sheet->row(1, ['S.No', 'First Name', 'Last Name', 'Checkin Date', ' Checkin Time', 'Vehicle Type']);
				$sheet->row(2, ['', '', '', '', '', '']);

				$rows = collect($this->getData())->map(function ($item, $key) {

					$data['s.no'] = $key + 1;
					$data['driver_first_name'] = $item['name'];
					$data['driver_last_name'] = $item['lname'];
					$data['checkin_date'] = $item['today_date'];
					$data['checkin_time'] = $item['checkin_time'];
					$data['vehicle_type'] = $item['vehicle_type'];
					return $data;

					//return array_only($item, ['first_name','last_name','phone','vehicle_number','license_number','status','added_by']);
				});

				$sheet->rows($rows);

			});

		})->export('csv');
	}

}