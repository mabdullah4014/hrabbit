<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Settings;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class SettingsController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Admin Mail& Social Icon');
			$content->description('Details');

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

			$content->header('Admin Mail& Social Icon');
			$content->description('Details Edit');

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
		return Admin::grid(Settings::class, function (Grid $grid) {

			$grid->email('Email');
			$grid->facebook('Facebook');
			$grid->twitter('Twitter');
			$grid->google('Google');
			$grid->skype('Skype');
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
		return Admin::form(Settings::class, function (Form $form) {

			$form->text('email', 'Email');
			$form->text('facebook', 'Facebook');
			$form->text('twitter', 'Twitter');
			// $form->text('google', 'Google');
			$form->text('skype', 'Skype');
			$form->url('website_url', 'Website URL');
			$form->hidden('mail_to');
			$form->image('logo_url')->move('app_logo');
			//$form->image('logo_url')->name(function ($file) {  return env("APP_URL").$file->guessExtension();});
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
			$form->saving(function (Form $form) {
				//$form->website_url = env("APP_URL");
				$form->mail_to = "mailto:".$form->email;
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
