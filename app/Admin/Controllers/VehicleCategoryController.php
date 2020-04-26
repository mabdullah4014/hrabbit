<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\RejectVehicle;
use App\Admin\Extensions\VehicleCategoryExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Status;
use App\VehicleCategory;
use App\DriverCheckin;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
class VehicleCategoryController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Vehicle Category');
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

			$content->header('Vehicle Category');
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

			$content->header('Vehicle Category');
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
		return Admin::grid(VehicleCategory::class, function (Grid $grid) {

			$grid->id('ID')->sortable();
			$grid->vehicle_type('Vehilce Type')->sortable();
			$grid->base_fare('Base Fare')->sortable();
			$grid->price_per_km('Price Per KM')->sortable();
			$grid->image()->display(function ($image) {
				if ($image != '') {
					return '<img src="/' . $image . '" alt="vehicle categroy Image" width="75"/>';
				}

			});
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
				$actions->disableDelete();		

				$actions->append(new RejectVehicle($actions->getKey()));

			});
			$grid->created_at();
			$grid->updated_at();
			$grid->exporter(new VehicleCategoryExport());
			$grid->filter(function ($filter) {
			$service_category = VehicleCategory::pluck('vehicle_type', 'vehicle_type');
			$filter->disableIdFilter();
			$filter->equal('vehicle_type', 'Vehicle Type')->select($service_category);
			$filter->like('base_fare', 'Base Fare');
			$filter->like('price_per_km', 'Price Per KM');
			$filter->between('created_at', 'Created Date')->date();	
		});
		});
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(VehicleCategory::class, function (Form $form) {
			$status = Status::pluck('status', 'id');
			$form->display('id', 'ID');
			$form->text('vehicle_type','Vehicle Type')->rules(function($form) {
	            if (!$id = $form->model()->id) {
	               return 'required|unique:vehicle_categories,vehicle_type';
	            } else {
	               return 'required|unique:vehicle_categories,vehicle_type,'.$form->model()->id;
	            }
        	})->help("Do not leave space between words instead use _");
			$form->number('base_fare', 'Base Fare')->min(1)->rules('required|min:1|numeric');
			$form->number('price_per_km', 'Price Per KM')->min(1)->rules('required|min:1|numeric');
			$form->number('commission_percentage', 'Commission')->min(1)->rules('required|min:1|');
			$form->select('status', 'Status')->options($status)->rules('required');
			$form->image('image')->rules('dimensions:max_width=100,max_height=100')->move('uploads')->uniqueName()->rules('required')->help('Upload png images with transparent background to get better result in App. Max_width:100 and max_height:100');
			
			$form->saving(function (Form $form) {
			
				$form->vehile_type = str_replace(' ', '_', $form->vehile_type);
				
				
			});
			$form->saved(function (Form $form) {
				
				/// Update in firebase
				$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/'.env('FIREBASE_KEY'));
				$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
				$database = $firebase->getDatabase();

				$data[$form->model()->status] = $form->model()->status;
				$updates = [
					'category/' . $form->model()->id. '/Status' =>$form->model()->status,
				];
				$newpost = $database->getReference() // this is the root reference
					->update($updates);

			});
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

	public function deletevehicle(Request $request){
		$id =  $request->input('ids');
		$data =  DriverCheckin::where('vehicle_id',$id)->get()->toArray();
		$val = '';
		if(empty($data)){
			VehicleCategory::find($id)->delete();
			echo 'success';
			exit;
		} else {
			$data =  DriverCheckin::where('vehicle_id',$id)->where('booking_status','1')->get()->toArray();
			if(!empty($data)){
				foreach ($data as $key => $value) {
					$val.= '<strong>'.$value['name'].' '.$value['lname'].'</strong><br>';
				}
				$vale = trim($val,',');
				echo $vale;
				exit;
			} else {

				$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/'.env('FIREBASE_KEY'));
			   	$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
			   	$database = $firebase->getDatabase();

				$data=DriverCheckin::where('vehicle_id',$id)->where('checkin_status','1')->get()->toArray();
				foreach ($data as $key => $value) {
				
			   		/*$updates = array(
						'admin_status' => '1'
					);*/

					/*$newpost = $database->getReference('drivers_status/' . $value['driver_id'])->update($updates);*/	
				}
				$updates = array(
						'Status' => '0'
					);
				$newpost = $database->getReference('category/' .$id)->update($updates);

				$newpost=$database->getReference('drivers_location/'.$id)->remove();
				VehicleCategory::find($id)->delete();
				echo 'success';
				exit;
			}
		}
	}
}
