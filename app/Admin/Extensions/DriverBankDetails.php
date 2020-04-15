<?php

namespace App\Admin\Extensions;

use Admin;
use App\BankPayouts;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class DriverBankDetails extends AbstractExporter {
	public function export() {

		if (substr($_REQUEST['_export_'], 0, 9) == "selected:") {

			Excel::create('driver_bank_details', function ($excel) {

				$excel->sheet('Sheetname', function ($sheet) {

					$ids = substr($_REQUEST['_export_'], 9, strlen($_REQUEST['_export_']));
					$ids = explode(',', $ids);
					$data = BankPayouts::whereIn('id', $ids)->get();
					$data = $data->toArray();

					$sheet->row(1, ['S.No', 'First Name', 'Last Name', 'Bank Name', 'Account Number']);
					$sheet->row(2, ['', '', '']);

					$rows = collect($data)->map(function ($item, $key) {

						$data['s.no'] = $key + 1;
						$data['first_name'] = $item['bank_fname'];
						$data['last_name'] = $item['bank_lname'];
						$data['bank_name'] = $item['bankname'];
						$data['account_number'] = $item['account_num'];
						return $data;
					});

					$sheet->rows($rows);

				});

			})->export('csv');

		} else {
			Excel::create('driver_bank_details', function ($excel) {

				$excel->sheet('Sheetname', function ($sheet) {

					// This logic get the columns that need to be exported from the table data
					$sheet->row(1, ['S.No', 'First Name', 'Last Name', 'Bank Name', 'Account Number']);
					$sheet->row(2, ['', '', '']);

					$rows = collect($this->getData())->map(function ($item, $key) {

						$data['s.no'] = $key + 1;
						$data['first_name'] = $item['bank_fname'];
						$data['last_name'] = $item['bank_lname'];
						$data['bank_name'] = $item['bankname'];
						$data['account_number'] = $item['account_num'];
						return $data;
					});

					$sheet->rows($rows);

				});

			})->export('csv');
		}

	}

}