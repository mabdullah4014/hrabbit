<?php

namespace App\Admin\Controllers;

use App\CommissionCalculationSetting;
use App\Http\Controllers\Controller;
use App\Status;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class CommissionCalculationSettingController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Commission Calculation Settings');
			$content->description('Manage');

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

			$content->header('Commission Calculation Settings');
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

			$content->header('Commission Calculation Settings');
			$content->description('Create');

			$content->body($this->form());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(CommissionCalculationSetting::class, function (Grid $grid) {

			$grid->trip_charge_start('Trip Charge($) Start');
			$grid->trip_charge_end('Trip Charge($) End');
			$grid->commission('Commission($)');
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableView();
			});
			$grid->disableFilter();
			$grid->disableExport();
			$grid->disableRowSelector();
			$grid->disablePagination();
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(CommissionCalculationSetting::class, function (Form $form) {
			$status = Status::pluck('mode', 'id');
			$form->text('trip_charge_start', 'Trip Charge($) Start');
			$form->text('trip_charge_end', 'Trip Charge($) End');
			$form->text('commission', 'Commission($)');
			$form->tools(function (Form\Tools $tools) {
				$tools->disableDelete();
				$tools->disableView();
			});
			$form->footer(function ($footer) {		
				$footer->disableViewCheck();	
				$footer->disableEditingCheck();		
				$footer->disableCreatingCheck();
				});
				
		});
	}
}
