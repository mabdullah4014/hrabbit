<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DriverCheckinExport;
use App\DriverCheckin;
use App\Http\Controllers\Controller;
use App\VehicleCategory;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class DriverCheckinController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Drivers');
			$content->description('Checked In');

			$content->body($this->grid());
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
	protected function grid() {
		return Admin::grid(DriverCheckin::class, function (Grid $grid) {

			$grid->id('ID')->sortable();
			$grid->name('First Name');
			$grid->lname('Last Name');
			$grid->today_date('Checkin Date')->display(function ($today_date) {
				return date('d-M-Y', strtotime($today_date));
			});
			$grid->checkin_time('Checkin Time');
			/* $grid->vehicle_type()->display(function($vehicle_type) {
			$v = VehicleCategory::find($vehicle_type);
			if(is_object($v))
			return $v->vehicle_type;
			else
			return '';
			})->sortable();*/
			$grid->vehicle_type('Vehicle Type')->display(function ($vehicle_type) {
				return str_replace('_', ' ', $vehicle_type);
			})->sortable();
			$grid->created_at();
			$grid->updated_at();
			$grid->disableCreateButton();
			$grid->disableActions();
			$grid->disableRowSelector();
			$grid->model()->where('checkin_status', '=', 1);
			$grid->filter(function ($filter) {
				// Remove the default id filter
				$filter->disableIdFilter();

				$service_category = VehicleCategory::pluck('vehicle_type', 'id');
				// Add a column filter
				$filter->like('name', 'First Name');
				$filter->like('lname', 'Last Name');
				$filter->equal('vehicle_type', 'Vehicle Type')->select($service_category);
				$filter->between('today_date', 'Checked-in Dates')->date();
			});
			$grid->exporter(new DriverCheckinExport());
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(DriverCheckin::class, function (Form $form) {

			$form->display('id', 'ID');
			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
		});
	}
}
