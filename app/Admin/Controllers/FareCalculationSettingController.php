<?php

namespace App\Admin\Controllers;

use App\FareCalculationSetting;
use App\Http\Controllers\Controller;
use App\Status;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class FareCalculationSettingController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Fare Calculation Settings');
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

			$content->header('Fare Calculation Settings');
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

			$content->header('Fare Calculation Settings');
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
		return Admin::grid(FareCalculationSetting::class, function (Grid $grid) {

			$grid->pick_mileage('Pick Mileage (price per mile)');
			$grid->pick_time('Pick Time (price per minute)');
			$grid->drive_mileage('Drive Mileage (price per mile)');
			$grid->wait_time('Wait Time (price per minute)');
			$grid->drive_time('Drive Time (price per minute)');
            $grid->mileage_limit('Mileage Limit');
            $grid->drive_mileage_al('Drive Mileage (price per mile) When Above Limit');
			$grid->wait_time_al('Wait Time (price per minute) When Above Limit');
			$grid->drive_time_al('Drive Time (price per minute) When Above Limit');
			$grid->min_fare('Min Fare($)');
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
		return Admin::form(FareCalculationSetting::class, function (Form $form) {
			$status = Status::pluck('mode', 'id');
			$form->text('pick_mileage', 'Pick Mileage (price per mile)');
			$form->text('pick_time', 'Pick Time (price per minute)');
			$form->text('drive_mileage', 'Drive Mileage (price per mile)');
			$form->text('wait_time', 'Wait Time (price per minute)');
			$form->text('drive_time', 'Drive Time (price per minute)');
			$form->text('mileage_limit', 'Mileage Limit');
			$form->text('drive_mileage_al', 'Drive Mileage (price per mile) When Above Limit');
			$form->text('wait_time_al', 'Wait Time (price per minute) When Above Limit');
			$form->text('drive_time_al', 'Drive Time (price per minute) When Above Limit');
			$form->text('min_fare', 'Min Fare($)');
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
