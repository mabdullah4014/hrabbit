<?php
namespace App\Admin\Controllers;

use App\AppSetting;
use App\Currency;
use App\DriversTrips;
use App\DriverCheckin;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Location;
use App\Status;
use App\Driver;
use App\VehicleCategory;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
class DriverTripsController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Trips');
			$content->description('Current');

			$content->body($this->grid());
		});
	}

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function completed() {
		return Admin::content(function (Content $content) {

			$content->header('Trips');
			$content->description('List');

			$content->body($this->gridc());
		});
	}

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function transaction() {
		return Admin::content(function (Content $content) {

			$content->header('Transaction');
			$content->description('Completed');

			$content->body($this->gridpay());
		});
	}

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function unpaid() {
		return Admin::content(function (Content $content) {

			$content->header('Trips');
			$content->description('Un Paid');

			$content->body($this->gridup());
		});
	}

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function paid() {
		return Admin::content(function (Content $content) {

			$content->header('Trips');
			$content->description('Paid');

			$content->body($this->gridp());
		});
	}

	public function scheduled() {
		return Admin::content(function (Content $content) {

			$content->header('Scheduled Trips');
			$content->description('List...');

			$content->body($this->gridschedule());
		});

	}

	public function canceled() {
		return Admin::content(function (Content $content) {

			$content->header('Canceled Trips');
			$content->description('List...');

			$content->body($this->gridcanceled());
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

			$content->header('Drivers Trips');
			$content->description('Update');

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

			$content->header('Drivers Trips');
			$content->description('Create');

			$content->body($this->form());
		});
	}

	/**
	 * Create interface.
	 *
	 * @param  Request  $request
	 * @return Content
	 */
	public function generate() {

		return Admin::content(function (Content $content) {

			$content->header('Generate Invoice');
			$content->description('Create');

			$content->body($this->form());
		});
	}
	public function messages()
{
    return [
        'title.required' => 'A title is required',
        'body.required'  => 'A message is required',
    ];
}

	public function generateInvoice(Request $request) {
		$tripid = $request->input('u_id');
		
		if ($tripid != '') {
			if($request->input('payment_status') == "" ||$request->input('total_amount') == ""){
				$error = new MessageBag([
					'title'   => 'Error',
					'message' => 'Please select values...!',
				]);
				return back()->with(compact('error'))->withInput();
			}
			$inv = Invoice::find($tripid);
			if ($inv) {
				
				$inv->amount = $request->input('total_amount');
				$inv->status = $request->input('payment_status');
				$inv->save();
				$redirecturl = 'admin/unpaid';
			} else {
				$trip = DriversTrips::find($tripid);
				if ($request->input('payment_status') == 1) {
					$trip->status = 6;
					$trip->payment_status = 1;
					$driver_id=$trip->driver_id;
					$customer_id=$trip->cus_id;
					$driver_details=Driver::where('id',$driver_id)->first();
					$trip->total_amount= $request->input('total_amount');
					$commission_percent=VehicleCategory::where('id',$trip->vehicle_id)->first();
					$commission_percentage=$commission_percent->commission_percentage;
					$commission=$request->input('total_amount')*$commission_percentage;
					$total_commission=$commission/100;
					$trip->commission=$total_commission;
					$trip->save();
					if( $request->input('payment_name')==1){
						$total=$trip->total_amount-$total_commission;
						$driver_details->wallet=$driver_details->wallet+$total;
						$driver_details->save();
					}
					else{
						$driver_details->wallet=$driver_details->wallet-$total_commission;
						$driver_details->save();
					}
					$check_in=DriverCheckin::where('driver_id',$driver_id)->first();
					$check_in->booking_status=0;
					$check_in->save();
					$redirecturl = 'admin/paid';

				} else {
					$redirecturl = 'admin/unpaid';
				}

				$rand_id = 'FMI' . rand(000, 999);
				$invoice = new Invoice();
				$invoice->u_id = $tripid;
				//    $invoice->customer_id = $trip->cus_id;
				//    $invoice->driver_id = $trip->driver_id;
				$invoice->driver_name = $trip->driver_name;
				$invoice->driver_lname = $trip->driver_lname;
				$invoice->today_date = $trip->today_date;
				$invoice->invoice_id = $rand_id;
				$invoice->amount = $trip->total_amount;
				$invoice->status = $request->input('payment_status');
				$invoice->save();
				$data['service_status']=6;
				$data['customer_id']=$customer_id;
				$data['driver_id']=$driver_id;
				// $form->saved(function (Form $form) {

			// 	/// Update in firebase
				$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/'.env('FIREBASE_KEY'));
				$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
				$database = $firebase->getDatabase();

				$update1 = [
					'customer_trips/' . $data['customer_id'] . '/Status' => $data['service_status'],
				];
				$updates = [
					'drivers_trips/' . $data['driver_id'] . '/Status' => $data['service_status'],
				];
				$newpost = $database->getReference()
					->update($update1);
				$newpost = $database->getReference()
					->update($updates);

			// });
			}
		}
		return redirect($redirecturl);
	}

	public function viewInvoice(Request $request) {

		return Admin::content(function (Content $content) {

			$content->header('Invoice');
			$content->description('View...');

			$content->row(function (Row $row) {
				$script = <<<'EOT'
$('.form-history-back').on('click', function (event) {
    event.preventDefault();
    history.back(1);
});
EOT;
				Admin::script($script);
				$text = trans('admin.back');
				$link = <<<EOT
					<div class="btn-group pull-right" style="margin-right: 10px">
						<a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;$text</a>
					</div>
EOT;
				$inv = Invoice::where('id', '=', $_REQUEST['id'])->firstOrFail();
				$headers = [];
				$rows = [
					['Invoice ID', $inv->invoice_id],
					['Name', $inv->driver_name . ' ' . $inv->driver_lname],
					['Date', $inv->invoice_date],
					// ['Pickup Location',1],
					//   ['Drop Location',1],
					['Amount', ($inv->amount != '') ? $inv->amount : "-"],
					['Status', ($inv->status == 1) ? 'Un Paid' : 'Paid'],
				];

				$table = new Table($headers, $rows);

				$box = new Box('Invoice', $table);
				$box->style('default');
				$box->solid();
				$row->column(12, $link);
				$row->column(3, '');
				$row->column(6, $box);
				$row->column(3, '');
			});
		});
	}

	public function viewTransaction(Request $request) {

		return Admin::content(function (Content $content) {

			$content->header('Transaction');
			$content->description('View...');

			$content->row(function (Row $row) {
				$script = <<<'EOT'
$('.form-history-back').on('click', function (event) {
    event.preventDefault();
	
	window.location.href = '/admin/transaction';
});
EOT;
				Admin::script($script);
				$text = trans('admin.back');
				$link = <<<EOT
<div class="btn-group pull-right" style="margin-right: 10px">
    <a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;$text</a>
</div>
EOT;

				$inv = DriversTrips::with('vehicle')->find($_REQUEST['id']);
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				$headers = [];
				$rows = [
					['Trip Number', $inv->id],
					['Driver Name', $inv->driver_name . ' ' . $inv->driver_lname],
					['Customer Name', $inv->customer_name . ' ' . $inv->customer_lname],
					['Pickup Location', $inv->pick_up],
					['Drop Location', $inv->drop_location],
					['Vehicle Type', str_replace("_", " ", $inv->vehicle_type)],
					['Pickup Time', $inv->pick_up_time],
					['Drop Time', $inv->drop_time],
					['Total Distance', ($inv->total_distance != '') ? $inv->total_distance . " KM" : "-"],
					['Price Per KM', ($inv->price_km != '') ? $currency->symbol . " " . $inv->price_km : "-"],
					['Total Amount', ($inv->total_amount != '') ? $currency->symbol . " " . round($inv->total_amount, 2) : "-"],
					['Commission', ($inv->commission != '') ? $currency->symbol . round($inv->commission,2) : "-"],
					['payment_name',$inv->payment_name],
					['Payment Status', ($inv->site_commission == 1) ? 'Un Paid' : 'Paid'],
				];
				$table = new Table($headers, $rows);
				$box = new Box('Transaction', $table);
				$box->style('default');
				$box->solid();
				$row->column(12, $link);
				$row->column(3, '');
				$row->column(6, $box);
				$row->column(3, '');
			});
		});
	}
	public function viewPaid(Request $request) {

		return Admin::content(function (Content $content) {

			$content->header('Paid');
			$content->description('View...');

			$content->row(function (Row $row) {
				$script = <<<'EOT'
$('.form-history-back').on('click', function (event) {
    event.preventDefault();
    history.back(1);
});
EOT;
				Admin::script($script);
				$text = trans('admin.back');
				$link = <<<EOT
<div class="btn-group pull-right" style="margin-right: 10px">
    <a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;$text</a>
</div>
EOT;

				$inv = DriversTrips::with('vehicle')->find($_REQUEST['id']);
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				//echo "<pre>";print_r($inv); exit;echo"</pre>";
				$headers = [];

				$rows = [
					['Trip Number', $inv->trip_num],
					['Driver Name', $inv->driver_name . ' ' . $inv->driver_lname],
					['Customer Name', $inv->customer_name . ' ' . $inv->customer_lname],
					['Pickup Location', $inv->pick_up],
					['Drop Location', $inv->drop_location],
					['Vehicle Type', str_replace("_", " ", $inv->vehicle_type)],
					['Pickup Time', $inv->pick_up_time],
					['Drop Time', $inv->drop_time],
					['Total Distance', ($inv->total_distance != '') ? number_format($inv->total_distance,2) . " KM" : "-"],
					['Price Per KM', ($inv->price_km != '') ? $currency->symbol . " " . number_format($inv->price_km,2) : "-"],
					['Total Amount', ($inv->total_amount != '') ? $currency->symbol . " " . number_format($inv->total_amount,2) : "-"],
					['Commission', ($inv->commission != '') ? $currency->symbol . " " . number_format($inv->commission,2) : "-"],
					['Payment Status', ($inv->status == 1) ? 'Un Paid' : 'Paid'],
				];

				$table = new Table($headers, $rows);

				$box = new Box('view', $table);
				$box->style('default');
				$box->solid();
				$row->column(12, $link);
				$row->column(3, '');
				$row->column(6, $box);
				$row->column(3, '');
			});
		});
	}
	public function deleteInvoice() {
		$inv = Invoice::where('trip_id', '=', $_REQUEST['id'])->firstOrFail();
		$inv->delete();
		return redirect('admin/paid');
	}
	public function currency() {
		$curr = AppSetting::select('currency')->first();

	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(DriversTrips::class, function (Grid $grid) {

			if(Admin::user()->id != 1){
				$grid->model()->where('added_by', '!=', '0');
			}
			$grid->id('ID')->sortable();
			$grid->model()->whereIn('status', [ 2, 3]);
			//$grid->trip_num('Trip Number')->sortable();
			$grid->today_date('Trip Date')->sortable();
			$grid->driver_name('Driver Name')->sortable();
			$grid->customer_name('Customer Name')->sortable();
			$grid->pick_up('Pickup Point')->sortable();
			$grid->drop_location('Drop Point')->sortable();
			$grid->pick_up_time('Pickup Time')->sortable();
			$grid->drop_time('Drop Time')->sortable();
			$grid->total_distance('Total Distance')->sortable();
			$grid->total_amount('Total Amount')->sortable();
			$grid->status('Trip Status')->display(function ($status) {
				
				if ($status == 2) {
					return "<span class='label label-success'>Accepted</span>";
				} else {
					return "<span class='label label-danger'>Trip Started</span>";
				}
			});
			$grid->disableCreateButton();
			$grid->disableActions();
			$grid->filter(function ($filter) {
				// Remove the default id filter
				$filter->disableIdFilter();	
				$filter->between('today_date', 'Trip Date')->date();						
				$filter->like('driver_name', 'Driver Name');
				$filter->like('customer_name', 'Customer Name');	
				$filter->like('pick_up', 'Pickup Point');
				$filter->like('drop_location', 'Drop Point');			
				
			});
			$grid->disableExport();
			$grid->disableRowSelector();
		//	$grid->disablePagination();
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function gridschedule() {
		return Admin::grid(DriversTrips::class, function (Grid $grid) {

			if(Admin::user()->id != 1){
				$grid->model()->where('added_by', '!=', '0');
			}

			$date = date('Y-m-d');
			$time = date('H:i:s');
			$grid->model()->where('today_date','>=', $date);
			$grid->model()->where('pick_time','>', $time);
			$grid->model()->where('status',0);
			$grid->id('Trip ID')->sortable();
			//$grid->driver_name('Driver Name')->sortable();
			$grid->customer_name('Customer Name')->sortable();
			$grid->today_date('Trip Date')->sortable();
			$grid->pick_up_time('Pickup Time')->sortable();
			$grid->pick_up('Pickup Point')->sortable();
			$grid->drop_location('Drop Point')->sortable();
			$grid->status('Trip Status')->display(function ($status) {
				
					return "<span class='label label-success'>Scheduled</span>";
				
			});

			$grid->disableCreateButton();
			$grid->disableActions();
			$grid->filter(function ($filter) {
				// Remove the default id filter
				$filter->disableIdFilter();	
				$filter->between('today_date', 'Trip Date')->date();						
				//$filter->like('driver_name', 'Driver Name');
				$filter->like('customer_name', 'Customer Name');
				$filter->like('pick_up', 'Pickup Point');
				$filter->like('drop_location', 'Drop Point');				
				
			});
			$grid->disableExport();
			$grid->disableRowSelector();
			//$grid->disablePagination();
		});
	}

	protected function gridcanceled() {
		return Admin::grid(DriversTrips::class, function (Grid $grid) {

			if(Admin::user()->id != 1){
				$grid->model()->where('added_by', '!=', '0');
			}
			
			$date = date('Y-m-d');		
			
			$grid->model()->whereIn('status', [8,9]);
			$grid->id('Trip ID')->sortable();
			$grid->driver_name('Driver Name')->sortable();
			$grid->customer_name('Customer Name')->sortable();
			$grid->today_date('Trip Date')->sortable();
			$grid->pick_up_time('Pickup Time')->sortable();
			$grid->pick_up('Pickup Point')->sortable();
			$grid->drop_location('Drop Point')->sortable();
			$grid->status('Canceled By')->display(function ($status) {
				
				if ($status == 8) {
					return "<span class='label label-success'>Customer</span>";
				} else{
					return "<span class='label label-danger'>Driver</span>";
				}
			});

			$grid->disableCreateButton();
			$grid->disableActions();
			$grid->filter(function ($filter) {
				// Remove the default id filter
				$filter->disableIdFilter();	
				$filter->between('today_date', 'Trip Date')->date();
				$filter->like('pick_up_time', 'Pickup Time');						
				$filter->like('driver_name', 'Driver Name');
				$filter->like('customer_name', 'Customer Name');
				$filter->like('pick_up', 'Pickup Point');
				$filter->like('drop_location', 'Drop Point');				
				
			});
			$grid->disableExport();
			$grid->disableRowSelector();
			//$grid->disablePagination();
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function gridc() {
		return Admin::grid(DriversTrips::class, function (Grid $grid) {

			if(Admin::user()->id != 1){
				$grid->model()->where('added_by', '!=', '0');
			}

			$grid->model()->where('status', '=', 6);

			$grid->id('ID')->sortable();
			
			//$grid->id('Trip Number')->sortable();
			$grid->today_date('Trip Date')->sortable();
			$grid->driver_name('Driver Name')->sortable();
			$grid->customer_name('Customer Name')->sortable();
			$grid->pick_up('Pickup Point')->sortable();
			$grid->drop_location('Drop Point')->sortable();
			$grid->pick_up_time('Pickup Time')->sortable();
			$grid->drop_time('Drop Time')->sortable();			
			
			// $grid->total_distance('Distance')->sortable();
			$grid->total_distance()->display(function ($total_distance) {
				return $total_distance . " KM";
			})->sortable();
			$grid->total_amount()->display(function ($total_amount) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (is_object($currency)) {
					return $currency->symbol . " " . round($total_amount, 2);
				} else {
					return round($total_amount, 2);
				}

			})->sortable();
			$grid->commission()->display(function ($commission) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (is_object($currency)) {
					return $currency->symbol . " " . round($commission, 2);
				} else {
					return round($commission, 2);
				}

			})->sortable();
			$grid->status('Trip Status')->display(function ($status) {
				
				if ($status == 6) {
					return "<span class='label label-success'>Completed</span>";
				} else {
					return "<span class='label label-danger'>Pending</span>";
				}
			});

			$grid->disableCreateButton();
			$grid->disableActions();
			$grid->filter(function ($filter) {
				// Remove the default id filter
				$filter->disableIdFilter();	
				$filter->between('today_date', 'Trip Date')->date();						
				$filter->like('driver_name', 'Driver Name');
				$filter->like('customer_name', 'Customer Name');
				$filter->like('pick_up', 'Pickup Point');
				$filter->like('drop_location', 'Drop Point');				
				
			});
			$grid->disableExport();
			$grid->disableRowSelector();
			// $grid->disablePagination();
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function gridup() {
		return Admin::grid(DriversTrips::class, function (Grid $grid) {

			$grid->id('ID')->sortable();
			$grid->model()->whereNotIn("status", [5, 6, 8, 9, 10])->where('status', '=', 4);
			$grid->trip_num('Trip Number')->sortable();
			$grid->today_date('Trip Date')->sortable();
			$grid->driver_name('Driver Name')->sortable();
			//  $grid->total_distance('Distance')->sortable();
			$grid->total_distance()->display(function ($total_distance) {
				return number_format($total_distance,2) . " KM";
			});
			$grid->total_amount()->display(function ($total_amount) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (isset($total_amount)) {
					if (is_object($currency)) {
						return $currency->symbol . " " . number_format($total_amount, 2);
					} else {
						return number_format($total_amount, 2);
					}
				} else {
					return 0;
				}
			});
			$grid->site_comission()->display(function ($site_comission) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (isset($site_comission)) {
					if (is_object($currency)) {
						return $currency->symbol . " " . number_format($site_comission, 2);
					} else {
						return number_format($site_comission, 2);
					}
				} else {
					return 0;
				}

			});
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableEdit();
				$actions->disableView();
				// append an action.
				$actions->append('<a title="Generate" href="generate?id=' . $actions->getKey() . '"><i class="fa fa-plus"></i></a>');
			});
			$grid->disableCreateButton();
			$grid->filter(function ($filter) {
				// Remove the default id filter
				$filter->disableIdFilter();	
				$filter->like('trip_num', 'Trip Number');
				$filter->between('today_date', 'Trip Date')->date();						
				$filter->like('driver_name', 'Driver Name');
				
			});
			$grid->disableExport();
			$grid->disableRowSelector();
			//$grid->disablePagination();
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function gridpay() {
		return Admin::grid(DriversTrips::class, function (Grid $grid) {
			$curr = AppSetting::select('currency')->first();
			$currency = Currency::select('currency')->where('id', $curr->currency)->first();
			$currency1 = $currency->currency;

			$grid->id('ID')->sortable();
			$grid->model()->where('status', '=', 6);
			//    $grid->booking_id('Booking ID')->sortable();
			$grid->customer_name('First Name')->sortable();
			$grid->customer_lname('Last Name')->sortable();
			$grid->driver_name('Driver First Name')->sortable();
			$grid->driver_lname('Driver Last Name')->sortable();

			$grid->total_amount()->display(function ($total_amount) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (is_object($currency)) {
					$currency1 = $currency->symbol;
					$amount = $currency1 . " " . round($total_amount, 2);
				} else {
					$amount = round($total_amount, 2);
				}
				return $amount;
			});
			$grid->commission()->display(function ($commission) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (is_object($currency)) {
					$currency1 = $currency->symbol;
					$amount = $currency1 . " " . round($commission, 2);
				} else {
					$amount = round($commission, 2);
				}
				return $amount;
			});
			$grid->created_at('Created At')->sortable();
			$grid->updated_at('Updated At')->sortable();
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableEdit();
				$actions->disableView();
				// append an action.
				$actions->append('<a title="View Transaction" href="viewTransaction?id=' . $actions->getKey() . '"><i class="fa fa-eye"></i></a>');
			});
			$grid->disableCreateButton();
			$grid->disableRowSelector();
			$grid->disableExport();
			$grid->filter(function ($filter) {

				// Remove the default id filter
				$filter->disableIdFilter();

				// Add a column filter
				$filter->like('customer_name', 'First Name');
				$filter->like('customer_lname', 'Last Name');
				$filter->like('driver_name', 'Driver First Name');
				$filter->like('driver_lname', 'Driver Last Name');
				$filter->between('created_at', 'Created Date')->date();	

			});
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function gridp() {
		return Admin::grid(DriversTrips::class, function (Grid $grid) {
			$curr = AppSetting::select('currency')->first();
			$currency = Currency::select('currency')->where('id', $curr->currency)->first();
			$grid->id('ID')->sortable();
			$grid->model()->where('payment_status', '=', 1);
			$grid->trip_num('Trip Number')->sortable();
			$grid->today_date('Trip Date')->sortable();
			$grid->driver_name('Driver Name')->sortable();
			//$grid->total_distance('Distance')->sortable();
			$grid->total_distance()->display(function ($total_distance) {
				return number_format($total_distance,2) . " KM";
			});
			$grid->total_amount()->display(function ($total_amount) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (isset($total_amount)) {
					if (is_object($currency)) {
						return $currency->symbol . " " . number_format($total_amount, 2);
					} else {
						return number_format($total_amount, 2);
					}
				} else {
					return 0;
				}

			});
			$grid->commission()->display(function ($commission) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (isset($commission)) {
					if (is_object($currency)) {
						return $currency->symbol . " " . number_format($commission, 2);
					} else {
						return number_format($commission, 2);
					}
				} else {
					return 0;
				}

			});
			//  $grid->site_commission('Commission')->sortable();
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableEdit();
				$actions->disableView();
				// append an action.
				$actions->append('<a title="View" href="viewPaid?id=' . $actions->getKey() . '"><i class="fa fa-eye"></i></a>');
				// $actions->append('<a style="color:red;" title="Delete" href="deleteInvoice?id='.$actions->getKey().'"><i class="fa fa-trash"></i></a>');
			});

			$grid->disableCreateButton();
			$grid->filter(function ($filter) {
				// Remove the default id filter
				$filter->disableIdFilter();	
				$filter->like('trip_num', 'Trip Number');
				$filter->between('today_date', 'Trip Date')->date();						
				$filter->like('driver_name', 'Driver Name');
				
			});
			$grid->disableExport();
			$grid->disableRowSelector();
			//$grid->disablePagination();
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(DriversTrips::class, function (Form $form) {
			$status = Status::pluck('paystatus', 'id');
			$form->setAction('/admin/generateInvoice');
			$opt=['cash','paypal'];
			$form->text('total_amount', 'Amount')->rules('required');
			//$amount = DriversTrips::where('id', $_GET['id'])->value('total_amount');
		//	$form->display('total_amount', 'Amount')->value($amount);
			$form->hidden('u_id')->default($_GET['id']);
			$form->select('payment_status', 'Status')->options($status)->rules('required');
			$form->select('payment_name', 'Payment Type')->options($opt)->rules('required');
			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
			$form->tools(function (Form\Tools $tools) {
				// Disable `List` btn.
				$tools->disableList();

			});
			$form->footer(function ($footer) {
				// disable reset btn
				//$footer->disableReset();
				// disable submit btn
				//$footer->disableSubmit();
				// disable `View` checkbox
				$footer->disableViewCheck();
				// disable `Continue editing` checkbox
				$footer->disableEditingCheck();
				// disable `Continue Creating` checkbox
				$footer->disableCreatingCheck();
			});
		});
	}

}
