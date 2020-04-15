<?php

namespace App\Admin\Controllers;

use App\AppSetting;
use App\Currency;
use App\Http\Controllers\Controller;
use App\Status;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Currency');
			$content->description('Management');

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

			$content->header('Currency');
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

			$content->header('Currency');
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
		return Admin::grid(Currency::class, function (Grid $grid) {
			$status = Status::pluck('status', 'id');
			$grid->id('ID')->sortable();
			$grid->currency('Currency')->sortable();
			$grid->symbol("Symbol")->sortable();
			$grid->short_name("Short Name")->sortable();
			$grid->converions_ratio('Conversion Ratio')->sortable();
			$grid->status('Status')->display(function ($status) {
				$value = Status::find($status)->status;
				if ($status == 1) {
					return "<span class='label label-success'>$value</span>";
				} else {
					return "<span class='label label-danger'>$value</span>";
				}
			});

			$grid->actions(function ($actions) {
				$actions->disableView();
				$settings_currency = AppSetting::first();
				if($settings_currency['currency'] == $actions->getKey())
				{
					$actions->disableDelete();
					$actions->disableEdit();					
				}
			});
			$grid->filter(function ($filter) {
				$filter->disableIdFilter();
				$filter->like('currency', 'Currency');
				$filter->like('symbol', 'Symbol');
				$filter->like('short_name', 'Short Name');
				$filter->equal('converions_ratio', 'Conversion Ratio');				
			});
			$grid->disableRowSelector();
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(Currency::class, function (Form $form) {
			$status = Status::pluck('status', 'id');
			$form->display('id', 'ID');
			$form->text('currency', 'Currency')->rules('required');
			$form->text('symbol', 'Symbol')->rules('required');
			$form->text('short_name', 'Short Name')->rules('required');
			$form->text('converions_ratio', 'Conversion Ratio')->rules('required');
			$id= \Request::segment(3);

			$cur_status=DB::table('app_settings')->value('currency'); 

			if($cur_status != $id){			
				$form->select('status', 'Status')->options($status)->rules('required');
			}
			
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
			$form->saved(function (Form $form) {
				
				$this->firebase_updates($form->short_name,$form->converions_ratio);

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
	public function firebase_updates($name,$status){

        $serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/'.env('FIREBASE_KEY'));
         
        $firebase = (new Factory)
         
        ->withServiceAccount($serviceAccount)
         
        ->withDatabaseUri(env('FIREBASE_DB'))
         
        ->create();
         
        $database = $firebase->getDatabase();

        $newPost = $database
         
        ->getReference('currency/'.$name)
         
        ->set($status);

    }
}
