<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Email;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;


class EmailController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Email Templates');
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

			$content->header('Email Templates');
			$content->description('Template Edit');

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

			$content->header('Admin Mail& Social Icon');
			$content->description('Details Create');

			$content->body($this->form());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(Email::class, function (Grid $grid) {

			$grid->id('ID');
			$grid->template_name('Template Name');
			//$grid->content('Body');
			
			$grid->status('Status')->display(function ($status) {
				
				if ($status == 0) {
					return "<span class='label label-success'>Active</span>";
				} else {
					return "<span class='label label-danger'>Inactive</span>";
				}

			});
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableView();
				//
			});
			$grid->filter(function ($filter) {
				$filter->disableIdFilter();
				$filter->like('template_name', 'Template Name');
				$filter->like('content', 'Body');						
			});
			$grid->disableFilter();
			
			$grid->disableExport();
			$grid->disableRowSelector();
			//$grid->disablePagination();
			$grid->disableCreateButton();
			
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(Email::class, function (Form $form) {


			//Form::extend('ckeditor', CKEditor::class);
			
			$form->text('template_name', 'Template Name')->disable();
			$form->ckeditor('content', 'Body');
			//$form->select('status', 'Status')->options(['0'=>'Active','1'=>'Inactive']);
			
			//$form->disableReset();
			$form->tools(function (Form\Tools $tools) {

				$tools->disableDelete();

				// Disable `Veiw` btn.
				$tools->disableView();

			
			});
			$form->saving(function (Form $form) {
				//$form->website_url = env("APP_URL");
				//$form->mail_to = "mailto:".$form->email;
				//$form->logo_url = env("APP_URL")."/".$form->logo_url;
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
