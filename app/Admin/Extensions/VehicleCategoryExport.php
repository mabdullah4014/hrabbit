<?php

namespace App\Admin\Extensions;

use Admin;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class VehicleCategoryExport extends AbstractExporter {
	public function export() {

		Excel::create('vehicle_category_export', function ($excel) {

			$excel->sheet('Sheetname', function ($sheet) {

				// This logic get the columns that need to be exported from the table data
				$sheet->row(1, ['S.No', 'Vehicle Type', 'Base Fare', 'Price Per KM', 'Status']);
				$sheet->row(2, ['', '', '', '', '']);

				$rows = collect($this->getData())->map(function ($item, $key) {

					$data['s.no'] = $key + 1;
					$data['vehicle_type'] = $item['vehicle_type'];
					$data['base_fare'] = $item['base_fare'];
					$data['price_per_km'] = $item['price_per_km'];
					$data['status'] = DB::table('statuses')->where('id', $item['status'])->value('status');

					return $data;

					//return array_only($item, ['first_name','last_name','phone','vehicle_number','license_number','status','added_by']);
				});

				$sheet->rows($rows);

			});

		})->export('csv');
	}

}