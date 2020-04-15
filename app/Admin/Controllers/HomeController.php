<?php

namespace App\Admin\Controllers;
use App\Customer;
use App\Driver;
use App\DriversTrips;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;

class HomeController extends Controller {
	/*public function index()
		    {
		        return Admin::content(function (Content $content) {

		            $content->header('Dashboard');
		            $content->description('Description...');

		            $content->row(Dashboard::title());

		            $content->row(function (Row $row) {

		                $row->column(4, function (Column $column) {
		                    $column->append(Dashboard::environment());
		                });

		                $row->column(4, function (Column $column) {
		                    $column->append(Dashboard::extensions());
		                });

		                $row->column(4, function (Column $column) {
		                    $column->append(Dashboard::dependencies());
		                });
		            });
		        });
	*/

	public function index() {
		if(Admin::user()->id != 1){
			return redirect('admin/dispatch');
			exit;
		}
		return Admin::content(function (Content $content) {

			$content->header('Dashboard');
			$content->description('.....');
			$data = array();
			$data['customerTotal'] = Customer::count();
			$data['driverTotal'] = Driver::count();
			$data['driversCheckedin'] = \App\DriverCheckin::where(['checkin_status' => 1])->count();
			$data['offlinedrivers'] = $data['driverTotal'] - $data['driversCheckedin'];
			$data['tripCount'] = DriversTrips::where('status', '=', 6)->count();

			$monthlyusers = Driver::select('id', 'created_at')->whereYear('created_at',date('Y'))
				->get()
				->groupBy(function ($val) {
					return Carbon::parse($val->created_at)->format('M');
				});
			$temp = [];
			foreach ($monthlyusers as $mu) {
				$temp[Carbon::parse($mu[0]->created_at)->format('M')] = count($mu);
			}

			$month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
			$growth = [];
			foreach ($month as $m) {
				if (isset($temp[$m])) {
					$growth[] = $temp[$m];
				} else {
					$growth[] = 0;
				}

			}
			$data['dgrowth'] = implode(",", $growth);

			$monthlyusers = Customer::select('id', 'created_at')->whereYear('created_at',date('Y'))	
				->get()
				->groupBy(function ($val) {
					return Carbon::parse($val->created_at)->format('M');
				});
			$temp = [];
			foreach ($monthlyusers as $mu) {
				$temp[Carbon::parse($mu[0]->created_at)->format('M')] = count($mu);
			}

			$month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
			$growth = [];
			foreach ($month as $m) {
				if (isset($temp[$m])) {
					$growth[] = $temp[$m];
				} else {
					$growth[] = 0;
				}

			}
			$data['cgrowth'] = implode(",", $growth);

			$content->body(view('Admin.charts.bar', $data));
		});
	}
}
