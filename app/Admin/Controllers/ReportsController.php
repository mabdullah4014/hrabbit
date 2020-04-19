<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DriverWiseEarning;
use App\Admin\Extensions\EarningReportExport;
use App\AppSetting;
use App\Currency;
use App\CustomerFeedback;
use App\Driver;
use App\DriversTrips;
use App\Http\Controllers\Controller;
use DB;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ReportsController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('header');
			$content->description('description');

			$content->body($this->grid());
		});
	}

	public function earning() {
		return Admin::content(function (Content $content) {

			$content->header('Earning');
			$content->description('Report');

			$content->body($this->gride());
		});
	}

	public function driverList() {
		return Admin::content(function (Content $content) {

			$content->header('Drivers');
			$content->description('Report');

			$content->body($this->gridedriver());
		});
	}

	public function driverEarning() {
		return Admin::content(function (Content $content) {

			$content->header('Driver Wise Earning');
			$content->description('Report');

			$content->body($this->griddw());
		});
	}

	/**
	 * Edit interface.
	 *
	 * @param $id
	 * @return Content
	 */
	public function edit($id) {
		return Admin::content(function (Content $content) use ($id) {

			$content->header('header');
			$content->description('description');

			$content->body($this->form()->edit($id));
		});
	}

	/**
	 * Create interface.
	 *
	 * @return Content
	 */
	public function create() {
		return Admin::content(function (Content $content) {

			$content->header('header');
			$content->description('description');

			$content->body($this->form());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function gride() {
		return Admin::grid(DriversTrips::class, function (Grid $grid) {

			$grid->id('ID')->sortable();
			$grid->model()->whereIn('status', ['3','4','6']);
			$grid->trip_num('Trip Number')->sortable();
			$grid->driver_name('Driver First Name')->sortable();
			$grid->driver_lname('Driver Last Name')->sortable();
			$grid->today_date('Trip Date')->sortable();
			$grid->pick_up('Pickup Location')->sortable();
			$grid->drop_location('Drop Location')->sortable();
			$grid->total_amount()->display(function ($total_amount) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (is_object($currency)) {
					if($total_amount==''){
						return $currency->symbol . 0;
					}
					return $currency->symbol . $total_amount;
				} else {
					return $total_amount;
				}
			});
			$grid->commission()->display(function ($commission) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (is_object($currency)) {
					if($commission==''){
						return $currency->symbol . 0;
					}
					return $currency->symbol .round($commission,2) ;
				} else {
					return $commission;
				}
			});			
			$grid->status('Trip Status')->display(function ($status) {
				
				if  ($status == 3) {
					return "<span class='label label-success'>Trip Started</span>";
				}
				elseif ($status == 4) {
					return "<span class='label label-success'>Trip Completed</span>";
				}
				elseif ($status == 6) {
					return "<span class='label label-success'>Payment Completed</span>";
				}
			});
			//$grid->total_amount('Total Amount')->sortable();
			//$grid->site_commission('Comission')->sortable();
			$grid->disableCreateButton();
			$grid->disableActions();
			//grid->disableRowSelector();
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
			$grid->filter(function ($filter) {

				// Remove the default id filter
				$filter->disableIdFilter();

				// Add a column filter
				$filter->like('driver_name', 'Driver First Name');
				$filter->like('driver_lname', 'Driver Last Name');
				$filter->like('trip_num', 'Trip Number');
				$filter->like('pick_up', 'Pickup Location');
				$filter->like('drop_location', 'Drop Location');
				$filter->between('today_date', 'Trip Date')->date();
			});
			$grid->exporter(new EarningReportExport());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function griddw() {
		return Admin::grid(DriversTrips::class, function (Grid $grid) {

			$grid->id('ID')->sortable();
			$grid->model()->where('driver_id', '=', $_REQUEST['id']);
			$grid->model()->whereIn('status', ['3','4','6']);
			
			$grid->trip_num('Trip Number')->sortable();
			$grid->driver_name('Driver First Name')->sortable();
			$grid->driver_lname('Driver Last Name')->sortable();
			$grid->today_date('Trip Date')->sortable();
			$grid->pick_up('Pickup Location')->sortable();
			$grid->drop_location('Drop Location')->sortable();
			$grid->total_amount('Total Amount')->sortable();
			//$grid->site_commission('Comission')->sortable();
			$grid->status('Trip Status')->display(function ($status) {
				
				if  ($status == 3) {
					return "<span class='label label-success'>Trip Started</span>";
				}
				elseif ($status == 4) {
					return "<span class='label label-success'>Trip Completed</span>";
				}
				elseif ($status == 6) {
					return "<span class='label label-success'>Payment Completed</span>";
				}
			});
			$grid->disableCreateButton();
			$grid->disableActions();
			//$grid->disableRowSelector();
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
			$grid->filter(function ($filter) {

				// Remove the default id filter
				$filter->disableIdFilter();

				// Add a column filter
				$filter->like('driver_name', 'Driver First Name');
				$filter->like('driver_lname', 'Driver Last Name');
				$filter->between('today_date', 'Trip Date')->date();
			});
			$grid->exporter(new DriverWiseEarning());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(DriversTrips::class, function (Grid $grid) {

			$grid->id('ID')->sortable();

			$grid->created_at();
			$grid->updated_at();
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function gridedriver() {
		return Admin::grid(Driver::class, function (Grid $grid) {
			$grid->id('ID')->sortable();
			$grid->name('First Name')->sortable();
			$grid->last_name('Last Name')->sortable();
			$grid->email('Email')->sortable();
			$grid->phone_number('Phone Number')->sortable();
			$grid->vehicle_num('Vehicle Number')->sortable();
			$grid->vehicle_type('Vehicle Type')->sortable();
			//$grid->vehicle_type('Vehicle Type')->display(function($vehicle_type) {
			//$value = VehicleCategory::find($vehicle_type)->vehicle_type;
			//return $value;
			//});

			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableEdit();
				$actions->disableView();
				// append an action.
				$actions->append('<a title="Driver History" href="driverEarning?id=' . $actions->getKey() . '"><i class="fa fa-eye"></i></a>');
			});

			$grid->disableCreateButton();
			$grid->disableRowSelector();
			$grid->filter(function ($filter) {

				// Remove the default id filter
				$filter->disableIdFilter();

				// Add a column filter				
				$filter->like('name', 'Driver First Name');
				$filter->like('last_name', 'Driver Last Name');
				$filter->like('email', 'Driver Email');
				$filter->like('phone_number', 'Phone Number');
				$filter->like('vehicle_num', 'License Plate No');
				$filter->like('vehicle_type', 'Vehicle Type');
				$filter->between('created_at', 'Created Date')->date();	

			});
			$grid->created_at();
			$grid->updated_at();
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(DriversTrips::class, function (Form $form) {

			$form->display('id', 'ID');

			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
		});
	}

	public function driverRating() {
		return Admin::content(function (Content $content) {
			$content->header('Driver Rating');
			$content->description('List');
			$data['driverRatings'] = CustomerFeedback::select('driver_id', 'drivers.name', 'drivers.last_name', DB::raw('avg(driver_rating) AS drating'), DB::raw('avg(cab_rating) AS crating'), DB::raw('avg(overall_rating) AS orating'))
				->join('drivers', 'customer_feedbacks.driver_id', '=', 'drivers.id')
				->groupBy('driver_id')
				->get();

			$content->body(view('Admin.driver.rating', $data));
		});
	}

	/**
	 * Edit interface.
	 *
	 * @param $id
	 * @return Content
	 */
	public function ratingDetail($id) {
		return Admin::content(function (Content $content) use ($id) {

			$content->header('Driver Ratings');
			$content->description('Detail');

			$content->body($this->griddrating($id));
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function griddrating($id) {
		//echo $id;exit;
		return Admin::grid(CustomerFeedback::class, function (Grid $grid) use ($id) {
			//$id = $_REQUEST['id'];
			$grid->model()->where('driver_id', '=', $id);
			$grid->id('ID')->sortable();
			$grid->column('customer.name', 'Customer Name');
			$grid->driver_rating('Driver Rating');
			$grid->cab_rating('Cab Rating');
			$grid->overall_rating('Overall Rating');
			//
			$grid->comments()->display(function ($comments) {
				return str_replace("%20", " ", $comments);
			});
			// $grid->comments('Comments')->sortable();
			$grid->created_at('Commented On');
			$grid->disableActions();
			$grid->filter(function ($filter) {
				// Remove the default id filter
				$filter->disableIdFilter();	
				
				//$filter->like('customer.name', 'Customer Name');
				$filter->equal('driver_rating', 'Driver Rating');
				$filter->equal('cab_rating', 'Cab Rating');
				$filter->equal('overall_rating', 'Overall Rating');				
				
			});
			$grid->disableCreateButton();
			$grid->disableRowSelector();
			$grid->disableExport();
			
		});
	}
}