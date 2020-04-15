<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ContactusExport;
use App\Contactu;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ContactusController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Contact Us');
			$content->description('List');

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

			$content->header('Contact Us');
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

			$content->header('Contact Us');
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
		return Admin::grid(Contactu::class, function (Grid $grid) {

			$grid->name('Name')->sortable();
			$grid->email('Email')->sortable();
			$grid->phone('Phone Number')->sortable();
			$grid->message('Message');
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
				$filter->like('name', 'Name');
				$filter->like('email', 'Email');
				$filter->like('phone', 'Phone Number');
				$filter->like('message', 'Message');

			});
			$grid->exporter(new ContactusExport());
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(Contactu::class, function (Form $form) {

			$form->display('id', 'ID');

			$form->display('created_at', 'Created At');
		});
	}
}
