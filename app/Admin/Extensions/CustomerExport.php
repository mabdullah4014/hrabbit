<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class CustomerExport extends AbstractExporter {
	public function export() {

		Excel::create('customer_details', function ($excel) {

			$excel->sheet('customer_details', function ($sheet) {

				$sheet->row(1, ['S.No', 'First Name', 'Last Name', 'Mobile Number', 'Email']);

				$sheet->row(2, ['', '', '', '', '', '', '']);

				$rows = collect($this->getData())->map(function ($item, $key) {
					$data['s.no'] = $key + 1;
					$data['name'] = $item['name'];
					$data['last_name'] = $item['last_name'];
					$data['phone'] = chop($item['phone_number'], '_');
					$data['email'] = $item['email'];

					return $data;
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