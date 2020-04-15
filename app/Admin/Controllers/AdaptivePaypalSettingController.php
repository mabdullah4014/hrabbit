<?php

namespace App\Admin\Controllers;

use App\AdaptivePaypalSetting;
use App\Http\Controllers\Controller;
use App\Status;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class AdaptivePaypalSettingController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Paypal Setting');
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

			$content->header('Paypal Setting');
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

			$content->header('Paypal Setting');
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
		return Admin::grid(AdaptivePaypalSetting::class, function (Grid $grid) {

			//$grid->id('ID')->sortable();
			$grid->paypal_username('User Name');
			$grid->paypal_password('Password');
			$grid->paypal_email('Email');
			$grid->paypal_signature('Signature');
			$grid->paypal_appid('Application ID');
			$grid->success_url('Success URL');
			$grid->cancel_url('Cancel URL');
			$grid->paypal_option('Paypal Option');
			// $grid->mode('Status');
			$grid->mode('Status')->display(function ($mode) {
				$value = Status::find($mode)->mode;
				if ($mode == 1) {
					return "<span class='label label-success'>$value</span>";
				} else {
					return "<span class='label label-danger'>$value</span>";
				}

			});
			$grid->disableCreateButton();
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableView();
			});
			$grid->disableFilter();
			$grid->disableExport();
			$grid->disableRowSelector();
			$grid->disablePagination();
			//$grid->created_at();
			//$grid->updated_at();
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(AdaptivePaypalSetting::class, function (Form $form) {
			$status = Status::pluck('mode', 'id');
			///$form->display('id', 'ID');
			$form->text('paypal_username', 'User Name');
			$form->password('paypal_password', 'Password');
			$form->text('paypal_client_id', 'Client Id');
			$form->text('paypal_secret', 'Secret');
			$form->email('paypal_email', 'Email');
			$form->text('paypal_signature', 'Signature');
			$form->text('paypal_appid', 'Application ID');
			$form->url('success_url', 'Success URL');
			$form->url('cancel_url', 'Cancel URL');
			$form->text('paypal_option', 'Option');
			$form->select('mode', 'Status')->options($status)->rules('required');

			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
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
			$form->submitted(function (Form $form) {
				$mode = $form->model()->mode;
				if($mode==1)
					$mode=0;
				else
					$mode=1;
			    DB::table('app_settings')			       
			        ->update(['paypal_status'=> $mode]);

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
