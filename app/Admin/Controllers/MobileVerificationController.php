<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\MobileVerification;
use App\Status;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class MobileVerificationController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Mobile Verification Setting');
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

			$content->header('Mobile Verification Setting');
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

			$content->header('Mobile Verification Setting');
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
		return Admin::grid(MobileVerification::class, function (Grid $grid) {

			//$grid->id('ID')->sortable();
			$grid->nexmo_key('Nexmo Key');
			$grid->nexmo_secret('Nexmo Secret');
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
		return Admin::form(MobileVerification::class, function (Form $form) {
			$status = Status::pluck('mode', 'id');
			$form->display('id', 'ID');
			$form->text('nexmo_key', 'Nexmo Key');
			$form->text('nexmo_secret', 'Nexmo Secret');
			$form->select('mode', 'Status')->options($status);
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
