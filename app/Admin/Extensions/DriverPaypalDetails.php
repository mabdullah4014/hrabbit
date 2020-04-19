<?php

namespace App\Admin\Extensions;

use Admin;
use App\BankPayouts;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class DriverPaypalDetails extends AbstractExporter {
	public function export() {

		if (substr($_REQUEST['_export_'], 0, 9) == "selected:") {

			Excel::create('driver_bank_details', function ($excel) {

				$excel->sheet('Sheetname', function ($sheet) {

					$ids = substr($_REQUEST['_export_'], 9, strlen($_REQUEST['_export_']));
					$ids = explode(',', $ids);
					$data = BankPayouts::whereIn('id', $ids)->get();
					$data = $data->toArray();

					$sheet->row(1, ['S.No', 'Driver Name', 'Paypal Email']);
					$sheet->row(2, ['', '', '']);

					$rows = collect($this->getData())->map(function ($item, $key) {
						
						$data['s.no'] = $key + 1;
						$data['bank_fname'] = $item['driver']['name'];
						//$data['driver_name'] = $data->driver->name;
						$data['bank_email'] = $item['bank_email'];

						return $data;
					});

					$sheet->rows($rows);

				});

			})->export('csv');

		} else {
			Excel::create('driver_bank_details', function ($excel) {

				$excel->sheet('Sheetname', function ($sheet) {

					// This logic get the columns that need to be exported from the table data
					$sheet->row(1, ['S.No', 'Driver Name', 'Paypal Email']);
					$sheet->row(2, ['', '', '']);

					$rows = collect($this->getData())->map(function ($item, $key) {
						
						$data['s.no'] = $key + 1;
						$data['bank_fname'] =$item['driver']['name'];
					//	$data['driver_name'] = 
						$data['bank_email'] = $item['bank_email'];
						return $data;
					});

					$sheet->rows($rows);

				});

			})->export('csv');
		}

	}

}