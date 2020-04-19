<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ContactusExport extends AbstractExporter {
	public function export() {

		Excel::create('customer_details', function ($excel) {

			$excel->sheet('customer_details', function ($sheet) {

				$sheet->row(1, ['S.No', 'Name', 'Email', 'Mobile Number', 'Message']);

				$sheet->row(2, ['', '', '', '', '', '', '']);

				$rows = collect($this->getData())->map(function ($item, $key) {
					$data['s.no'] = $key + 1;
					$data['name'] = $item['name'];
					$data['last_name'] = $item['email'];
					$data['phone'] = chop($item['phone'], '_');
					$data['email'] = $item['message'];

					return $data;
				});

				$sheet->rows($rows);

			});

		})->export('csv');
	}

}