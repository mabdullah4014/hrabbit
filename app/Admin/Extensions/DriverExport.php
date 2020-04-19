<?php

namespace App\Admin\Extensions;

use Admin;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DriverExport extends AbstractExporter {
	public function export() {
		Excel::create('drivers', function ($excel) {

			$excel->sheet('Sheetname', function ($sheet) {

				// This logic get the columns that need to be exported from the table data
				$sheet->row(1, ['S.No', 'First Name', 'Last Name', 'Mobile Number','Email', 'Vehicle Number', 'License Number', 'status']);
				$sheet->row(2, ['', '', '', '', '', '', '', '']);

				$rows = collect($this->getData())->map(function ($item, $key) {

					$data['s.no'] = $key + 1;
					$data['first_name'] = $item['name'];
					$data['last_name'] = $item['last_name'];
					// $data['phone'] = $this->hide_phone(chop($item['phone_number'], '_'));
					$data['phone'] = chop($item['phone_number'],'_');
					$data['email'] = $item['email'];
					$data['vehicle_number'] = $item['vehicle_num'];
					$data['license_number'] = $item['license_no'];

					$data['status'] = DB::table('statuses')->where('id', $item['status'])->value('status');

					//$data['added_by'] = DB::table('admin_users')->where('id', $item['added_by'])->value('username');

					return $data;

					//return array_only($item, ['first_name','last_name','phone','vehicle_number','license_number','status','added_by']);
				});

				$sheet->rows($rows);

			});

		})->export('csv');

	}

	public function hide_email($email){
        $em = explode("@",$email);
        $name = $em[0];
        $len = strlen($name);
        $showLen = floor($len/4);
        $str_arr = str_split($name);
        for($ii=$showLen;$ii<$len;$ii++){
            $str_arr[$ii] = '*';
        }
        $em[0] = implode('',$str_arr); 
        return $hidden_email = implode('@',$em);
    }
	public function hide_phone($phone)
    {
        $times=strlen(trim(substr($phone,4,5)));
        $star='';
        for ($i=0; $i <$times ; $i++) { 
            $star.='*';
        }
        return $star;
    }

}