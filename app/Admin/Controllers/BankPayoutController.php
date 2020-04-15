<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DriverBankDetails;
use App\Admin\Extensions\DriverPaypalDetails;
use App\Driver;
use App\BankPayouts;
use App\Http\Controllers\Controller;
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
use Illuminate\Support\Facades\DB;

class BankPayoutController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Driver Payout');
			$content->description('Detail');

			$content->body($this->grid());
		});
	}

	public function driver_paypal_details() {
		return Admin::content(function (Content $content) {

			$content->header('Driver Paypal');
			$content->description('Detail');

			$content->body($this->grid_paypal());
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

	public function viewBank() {

		return Admin::content(function (Content $content) {

			$content->header('Bank');
			$content->description('Details');

			$content->row(function (Row $row) {
				$script = <<<'EOT'
$('.form-history-back').on('click', function (event) {
    event.preventDefault();
	window.location.href = '/admin/bank';
});
EOT;
				Admin::script($script);
				$text = trans('admin.back');
				$link = <<<EOT
<div class="btn-group pull-right" style="margin-right: 10px">
    <a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;$text</a>
</div>
EOT;

				$inv = BankPayouts::find($_REQUEST['id']);
				$headers = [];
				$rows = [
					['Bank Name', $inv->bankname],
					['Account Number', $inv->account_num],
					['Bank Code', $inv->bank_code],
					['Date', $inv->created_at],
					['First Name', $inv->bank_fname],
					['Last Name', $inv->bank_lname],
					['Email', $inv->bank_email],
					['Date of Birth', $inv->bank_dob],
					['Phone', $inv->bank_phone],
				];

				$table = new Table($headers, $rows);

				$box = new Box('Bank Detail', $table);
				$box->style('default');
				$box->solid();
				$row->column(12, $link);
				$row->column(3, '');
				$row->column(6, $box);
				$row->column(3, '');
			});
		});
	}

	public function paypalAmount() {
		return Admin::content(function (Content $content) {

			$content->header('Paypal');
			$content->description('to be paid...');

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
				$sql = "select `driver_trips`.*, `drivers`.`name`,paypal_payouts.* from `driver_trips` left join `drivers` on `drivers`.`id` = `driver_trips`.`driver_id` left join `paypal_payouts` on `paypal_payouts`.`driver_id` = `driver_trips`.`driver_id` where `driver_trips`.`payment_name` = 'paypal' and `driver_trips`.`payment_status` = 'paid'";

				$drivers = DB::select($sql);

				if (count($drivers) > 0) {
					foreach ($drivers as $k => $driver) {
						$driversArr[] = $driver->driver_id;
						$driverName[$driver->driver_id]['name'] = $driver->name;
						$driverName[$driver->driver_id]['paypalid'] = $driver->paypal_id;
						$driverName[$driver->driver_id]['vt'] = $driver->vehicle_type;
					}
					$driversArr = array_unique($driversArr);
					$ids = implode(',', array_filter($driversArr));

					$cat = VehicleCategory::all()->toArray();
					foreach ($cat as $k3 => $vc) {
						$vcArr[$vc['vehicle_type']]['commission'] = $vc['commission_percentage'];
					}
					$sql1 = "SELECT `dt`.`driver_id`,SUM(`total_amount`) AS `totalamount` FROM `driver_trips`as `dt` JOIN `paypal_payouts` as `po`  JOIN `drivers` as `dn` on`po`.`driver_id`=`dt`.`driver_id` and `dn`.`id`=`dt`.`driver_id` WHERE `dt`.`driver_id`in ($ids) and `dt`.`payment_name`='paypal' and `dt`.`payment_status`='paid' GROUP BY `dt`.`driver_id`";

					$amount = DB::select($sql1);

					if (count($amount) > 0) {
						$result = array();
						$headers = ['DriverId', 'DriverName', 'Status', 'Paypal Email', 'Bill Amount', 'commission', 'Payable'];
						foreach ($amount as $k1 => $damt) {
							$rows[] = [$damt->driver_id,
								$driverName[$damt->driver_id]['name'],
								'Paid',
								$driverName[$damt->driver_id]['paypalid'],
								$damt->totalamount,
								$damt->totalamount * $vcArr[$driverName[$damt->driver_id]['vt']]['commission'] / 100,
								$damt->totalamount - ($damt->totalamount * $vcArr[$driverName[$damt->driver_id]['vt']]['commission'] / 100),
								// '<a title="view" href="paypaltransfer?id=' . $damt->driver_id . '&amount=' . $damt->totalamount - ($damt->totalamount * $vcArr[$driverName[$damt->driver_id]['vt']]['commission'] / 100) . '" class="btn btn-xs btn-success"><i class="ace-icon fa fa-eye bigger-120"></i></a>'
							];
						}
						$table = new Table($headers, $rows);
					}
				} else {
					$headers = ['DriverId', 'DriverName', 'Status', 'Paypal Email', 'Bill Amount', 'commission', 'Payable'];
					$rows = [];
					$table = new Table($headers, $rows);
				}
				$box = new Box('Paypal', $table);
				$box->style('default');
				$box->solid();
				// $row->column(12,$link);
				$row->column(12, $box);
			});
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(BankPayouts::class, function (Grid $grid) {
			$grid->model()->where('type', 'bank');
			$grid->id('ID')->sortable();
			$grid->driverid('Driver ID')->sortable();
			$grid->bankname("Bank Name");
			$grid->bank_fname('First Name');
			$grid->bank_lname('Last name');
			$grid->created_at();
			//$grid->updated_at();

			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableEdit();
				$actions->disableView();
				// append an action.
				$actions->append('<a title="View Bank Details" href="viewBank?id=' . $actions->getKey() . '"><i class="fa fa-eye"></i></a>');
			});
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
			$grid->filter(function ($filter) {

				// Remove the default id filter
				$filter->disableIdFilter();

				// Add a column filter				
				$filter->like('driverid', 'Driver ID');
				$filter->like('bankname', 'Bank Name');
				$filter->like('bank_fname', 'First Name');
				$filter->like('bank_lname', 'Last name');
				$filter->between('created_at', 'Created Date')->date();
				

			});
			$grid->disableCreateButton();
			$grid->exporter(new DriverBankDetails());
		});
	}

	protected function grid_paypal() {
		return Admin::grid(BankPayouts::class, function (Grid $grid) {
			$grid->model()->where('type', 'paypal');
			$grid->id('ID')->sortable();
			$grid->driverid('Driver ID')->sortable();
			$grid->bank_email('Paypal Email');
			
			$grid->driver()->name('Driver Name');
			//$grid->driver()->name();
			$grid->disableActions();
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableEdit();
				$actions->disableView();
				// append an action.
				//$actions->append('<a title="View Bank Details" href="viewBank?id=' . $actions->getKey() . '"><i class="fa fa-eye"></i></a>');
			});
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
			$grid->filter(function ($filter) {

				// Remove the default id filter
				$filter->disableIdFilter();

				// Add a column filter				
				$filter->like('driverid', 'Driver ID');
				$filter->like('bank_email', 'Paypal Email');
				//$filter->like('bank_fname', 'Driver Name');
				$filter->between('created_at', 'Created Date')->date();
			});
			$grid->disableCreateButton();
			$grid->exporter(new DriverPaypalDetails());
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(BankPayouts::class, function (Form $form) {

			$form->display('id', 'ID');

			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
		});
	}
}
