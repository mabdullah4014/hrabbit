<?php

namespace App\Admin\Controllers;

use App\AppSetting;
use App\Currency;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class AppSettingController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Application Settings');
			$content->description('All settings');

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

			$content->header('Application Settings');
			$content->description('All settings');

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

			$content->header('Application Settings');
			$content->description('All settings');

			$content->body($this->form());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(AppSetting::class, function (Grid $grid) {

			$grid->id('ID')->sortable();
			$grid->app_name('Application Name');
			$grid->app_version('Version');
			$grid->admin_contact('Admin Contact');
			$grid->admin_email('Admin Email');
			$grid->disableCreateButton();
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableView();
			});
			$grid->disableFilter();
			$grid->disableExport();
			$grid->disableRowSelector();
			$grid->disablePagination();
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
		return Admin::form(AppSetting::class, function (Form $form) {
			$currency = Currency::where('status', 1)->pluck('currency', 'id');
			$form->text('app_name', 'Application name');
			$form->image('app_logo')->move('app_logo')->uniqueName();
			$form->text('app_version', 'Version');
			$form->text('app_author', 'Author');
			$form->mobile('admin_contact', 'Admin Contact');
			$form->email('admin_email', 'Admin Email');
			$form->url('app_website', 'App Website');
			$form->textarea('app_description', 'Description');
			$form->select('currency', 'Currency')->options($currency)->rules('required');
			$form->select('distance_unit', 'Distance Unit')->options(['km'=>'KM','mi'=>'MI'])->rules('required');
			/* $form->text('radius', 'Distance from Pickup');
			   $form->text('schedule_min_time', 'Minimum Time for Scheduled Booking');*/
			$form->divide();

			/*$form->text('google_api_key', 'Google Api Key');
			$form->text('firebase_api', 'Firebase Api Key');
			$form->url('firebase_url', 'Firebase URL');
			$form->text('firebase_project_id', 'Firebase Project ID');
			$form->divide();

			$form->text('paypal_name', 'Paypal Name');
			$form->text('paypal_type', 'Paypal Type');
			$form->divide();

			$form->text('cc_access_code', 'CC Access Code');
			$form->text('cc_merchant_id', 'CC Merchant ID');
			$form->url('cc_redirect_url', 'CC Redirect URL');
			$form->url('cc_cancel_url', 'CC Cancel URL');
			$form->url('cc_rsaKey_url', 'CC RSA Key URL');
			$form->text('time_period', 'Time Period');
			$form->divide();*/

			$form->disableReset();
			$form->tools(function (Form\Tools $tools) {

				// Disable `List` btn.
				//$tools->disableList();

				// Disable `Delete` btn.
				$tools->disableDelete();

				// Disable `Veiw` btn.
				$tools->disableView();

				// Add a button, the argument can be a string, or an instance of the object that implements the Renderable or Htmlable interface
				//$tools->add('<a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;delete</a>');
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
