<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;
use App\Driver;
class DriverPayoutExport extends AbstractExporter {
	public function export() {

		Excel::create('driver_payout', function ($excel) {

			$excel->sheet('driver_payout', function ($sheet) {

				$sheet->row(1, ['S.No', 'Driver Name', 'Type', 'Reference Number', 'Date']);

				$sheet->row(2, ['', '', '', '', '', '', '']);

				$rows = collect($this->getData())->map(function ($item, $key) {
					$data['s.no'] = $key + 1;
					$data['driver_name'] = Driver::where('id',$item['driver_id'])->value('name');
					$data['type'] = $item['type'];
					$data['ref_no'] = $item['ref_no'];
					$data['date'] = $item['date'];

					return $data;
				});

				$sheet->rows($rows);

			});

		})->export('csv');
	}

}