<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DriverExport;
use App\Admin\Extensions\ReleaseDriver;
use App\Country;
use App\Driver;
use App\DriverTrip;
use App\Email;
use App\Http\Controllers\Controller;
use App\State;
use App\Status;
use App\VehicleCategory;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use FCM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Mail;

class DriverController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Driver');
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
	public function edit_new($id) {
		return Admin::content(function (Content $content) use ($id) {

			$content->header('Driver');
			$content->description('Update');
			$data['base_url'] = url('/');
			$data['url'] = str_ireplace('/edit', '', url()->current());
			$data['driver'] = Driver::find($id);
			$data['vehicle'] = VehicleCategory::pluck('vehicle_type', 'vehicle_type')->all();
			$data['country'] = Country::pluck('id', 'name')->all();
			$data['status'] = Status::pluck('id', 'status')->all();
			$data['states'] = State::where('country_id', $data['driver']->country_name)->pluck('id', 'state_name')->all();
			//echo "<pre>";print_r($data['vehicle']); exit;
			// $roles = Roles::lists('name', 'id');
			$content->body(view('Admin.driver.editdriver', $data));
			// $content->body($this->editform()->edit($id));
		});
	}

	public function edit($id) {
		return Admin::content(function (Content $content) use ($id) {

			$content->header('Driver');
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

			$content->header('Driver');
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
		return Admin::grid(Driver::class, function (Grid $grid) {

			//$grid->id('ID')->sortable();
			$grid->name('First Name')->sortable();
			$grid->last_name('Last Name')->sortable();
			$grid->phone_number('Mobile Number')->sortable();
			/*->display(function ($phone_number){
				$phone=str_replace(substr($phone_number, 4,5), $this->hide_phone($phone_number), $phone_number);
				return $phone;
			})*/
			$grid->email('Email')->display(function ($email) {
				// $hidden_email = $this->hide_email($email);
				return $email;
			})->sortable();
			// $grid->email('Email')->sortable();
			$grid->vehicle_num('Vehicle Number')->sortable();
			$grid->license_no('license Number')->sortable();
			$grid->status('Status')->display(function ($status) {
				$value = Status::find($status)->status;
				if ($status == 1) {
					return "<span class='label label-success'>$value</span>";
				} else {
					return "<span class='label label-danger'>$value</span>";
				}
			});

			$grid->id_proof()->display(function ($image) {
				$path = str_replace('public', '/storage', $image);

				$external_link = $image;

				$external_link_admin = "/uploads/DriverProof" . "/dummy_proof_icon.png";
				// print_r($external_link_admin);
				// exit;
				if ($image == '') {

					$path = $external_link_admin;
					//$path="http://spotnrides2.uplogictech.com/uploads/DriverProof/dummy_proof_icon.png";
				} elseif ($image != '') {
					//if (@getimagesize($external_link)) {
					$path = env('APP_URL') . "/" . $external_link;
					$path = str_replace('uploads/uploads', 'uploads', $path);

					/*} else  {
						$path = "/uploads/DriverProof/dummy_proof_icon.png";
					}*/
					//$path='/'.$image;
				}
				// elseif (@getimagesize($external_link)) {
				// 	$path = $external_link;
				// } elseif($external_link==''){
				// 	$path = $external_link_admin;
				// }else {
				// 	$path = $external_link_admin;
				// }

				//$external_link = $_ENV['API_URL'] . "/images/driver/" . $image;
				//$external_link_admin = $_ENV['APP_URL'] . "/uploads/" . $image;
				/*				if (@getimagesize($external_link)) {
					$path = $external_link;
				} elseif (@getimagesize($external_link_admin)) {
					$path = $external_link_admin;
				} else {
					$path = '';
				}*/

				//new commented
				// $path = str_replace('public', '/storage', $image);
				// if ($path != '') {
				return '<img src="' . $path . '" alt="ID Proof"  class="img img-thumbnail" width="75"/>';
				// }

			});
			$grid->photo()->display(function ($image) {

				$external_link = 'uploads/' . $image;

				//$external_link_admin = $_ENV['APP_URL'] . "/uploads/" . $image;
				//$path = "/uploads/" . $image;
				$path = str_replace('public', '/DriverImages', $image);

				if ($image == '') {

					//$path = $external_link_admin;
					$path = "/uploads/DriverProof/dummy_user_icon.png";
				} elseif ($image != '') {
					//if (@getimagesize($external_link)) {
					$path = env('APP_URL') . "/" . $external_link;

					$path = str_replace('uploads/uploads', 'uploads', $path);
					/*} else  {
						$path = "/uploads/DriverProof/dummy_user_icon.png";
					}*/
					//$path='/'.$image;//
				}
				/*if (@getimagesize($external_link)) {
						$path = $external_link;
					} elseif (@getimagesize($external_link_admin)) {
						$path = $external_link_admin;
					} else {
						$path = '';
				*/
				if ($path != '') {
					return '<img src="' . $path . '" alt="Photo" width="75" class="img img-thumbnail" />';
				}

			});
			$grid->created_at();
			/*$grid->id('Clear')->display(function ($id) {
				$value =  DriverTrip::select(DB::raw('id'))->where('driver_id','=',$id)->orderBy('id', 'desc')->limit(1)->get();
				$value = json_decode($value);
				if(isset($value[0])){ $value1 = $value[0]->id; } else{ $value1=''; }
				if($value1!=''){
					return $value1;
				}
				//return $value1;
			});*/
			//	$grid->disableRowSelector();
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
			$grid->filter(function ($filter) {
				// Remove the default id filter
				$filter->disableIdFilter();

				$service_category = VehicleCategory::pluck('vehicle_type', 'id');
				$status = Status::pluck('status', 'id');
				// Add a column filter
				$filter->like('name', 'First Name');
				$filter->like('last_name', 'Last Name');
				$filter->like('vehicle_num', 'Vehicle Number');
				$filter->like('license_no', 'Licence Number');
				$filter->like('phone_number', 'Phone Number');
				$filter->like('email', 'Email Address');
				$filter->equal('vehicle_type', 'Service Category')->select($service_category);
				$filter->equal('status', 'Status')->select($status);
				$filter->between('created_at', 'Registration Dates')->date();
			});
			$grid->actions(function ($actions) {
				$actions->disableView();
				$actions->disableDelete();
				//$actions->append('<a href="/admin/ReleaseDriver/' . $actions->getKey() . '"> <i class="fa fa-refresh" aria-hidden="true"></i>  </a>');
				$actions->append(new ReleaseDriver($actions->getKey()));
			});
			$grid->exporter(new DriverExport());

		});
	}

	protected function sendFCMDriver($token, $msg) {
		$optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60 * 20);

		$notificationBuilder = new PayloadNotificationBuilder(env('APP_NAME'));
		$notificationBuilder->setBody($msg)
			->setSound('default');

		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['msg' => $msg]);

		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();

		$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

	}
	protected function sendFCMCustomer($token, $msg) {
		$optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60 * 20);

		$notificationBuilder = new PayloadNotificationBuilder(env('APP_NAME'));
		$notificationBuilder->setBody($msg)
			->setSound('default');

		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['msg' => $msg]);

		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();

		$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

	}
	public function ReleaseDriver(Request $request) {

		$id = $request->input('id');
		if ($id != '') {
			$value = DriverTrip::select('id', 'status', 'cus_id', 'driver_id', 'vehicle_id')->where('driver_id', '=', $id)->orderBy('id', 'desc')->limit(1)->get();
			//print_r($value[0]->cus_id);exit;
			// 	/// Update in firebase

			$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
			$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
			$database = $firebase->getDatabase();

			$updates = [
				'drivers_trips/' . $id . '/Status' => "0",
			];

			$newpost = $database->getReference()
				->update($updates);

			if (isset($value)) {
				$updates1 = [
					'customer_trips/' . $value[0]->cus_id . '/Status' => "5",
				];
				$newpost = $database->getReference()
					->update($updates1);
			}

			$updates2 = [
				'drivers_status/' . $id . '/status' => "available",
			];

			$newpost = $database->getReference()
				->update($updates2);

			$updates3 = [
				'drivers_location/' . $value[0]->vehicle_id . '/' . $value[0]->driver_id . '/status' => "1",
			];

			$newpost = $database->getReference()
				->update($updates3);

			// $value = json_decode($value);

			if (isset($value)) {
				$value1 = $value[0]->id;
				$status1 = $value[0]->status;
			} else {
				$value1 = '';
				$status1 = '';
			}

			$trip = DriverTrip::find($value1);
			if ($value1 != '' && $status1 == 10) {
				$trip->status = 4;
				$trip->save();
				//$redirecturl='admin/trips';
				$msg = 'Trip Ended! Driver & Customer is released now';
				// admin_toastr('Trip Ended! Driver is released now', 'success');
				// return redirect($redirecturl);

				$customer = Customer::where('id', $value->cus_id)->first();
				$token1 = $customer->device_id;
				$msgs1 = "Customer Released From Ongoing Trip";
				if ($token1 != '' && $token1 != '0') {
					$this->sendFCMCustomer($token1, $msgs1);
				}
				$driver = Driver::where('id', $value->driver_id)->first();
				$token = $driver->device_id;
				$msgs = "Driver Released From Ongoing Trip";
				if ($token != '' && $token != '0') {
					$this->sendFCMDriver($token, $msgs);
				}
			} else if (($value1 != '' && $status1 == 2) || ($value1 != '' && $status1 == 3)) {
				$trip->status = 4;
				$trip->save();
				//$redirecturl='admin/trips';
				$msg = 'Trip Ended! Driver & Customer is released from current Trip';
				// admin_toastr('Trip Ended! Driver is released now', 'success');
				// return redirect($redirecturl);

				$customer = Customer::where('id', $value->cus_id)->first();
				$token1 = $customer->device_id;
				$msgs1 = "Customer Released From Engaged Trip";
				if ($token1 != '' && $token1 != '0') {
					$this->sendFCMCustomer($token1, $msgs1);
				}
				$driver = Driver::where('id', $value->driver_id)->first();
				$token = $driver->device_id;
				$msgs = "Driver Released From Engaged Trip";
				if ($token != '' && $token != '0') {
					$this->sendFCMDriver($token, $msgs);
				}
			} else {
				$msg = 'Driver & Customer Already Released!';
				// admin_toastr('New Driver! No Trips', 'error');
				//$redirecturl='admin/trips';
				// return redirect('admin/drivers');
			}
			echo $msg;exit;
			// echo 'success'

			/*$inv = Driver::find($id);

				if ($inv) {
					$trips=DriverTrip::where('driver_id',$id)->whereIN('status',[2,3,4])->get();
					if(count($trips)==0){
					$inv->status =2;
					$inv->save();

					$redirecturl='admin/drivers';
					admin_toastr('Trip Ended! Driver is released now', 'success');
					}else{
						admin_toastr('Oops,Driver is on Trip', 'error');
						$redirecturl='admin/drivers';
					}
				} else{
					$inv->status =2;
					$inv->save();
					$redirecturl='admin/drivers';
			*/
		} else {
			return ("id not found");
		}
		return redirect($redirecturl);
	}

	public function DeleteDriver($id) {

		if ($id != '') {
			$inv = Driver::find($id);

			if ($inv) {
				$trips = DriverTrip::where('driver_id', $id)->whereIN('status', [2, 3, 4])->get();
				if (count($trips) == 0) {
					$inv->status = 2;
					$inv->save();

					$redirecturl = 'admin/drivers';
					admin_toastr('Driver is inactive now', 'success');
				} else {
					admin_toastr('Oops,Driver is on Trip', 'error');
					$redirecturl = 'admin/drivers';
				}
			} else {
				$inv->status = 2;
				$inv->save();
				$redirecturl = 'admin/drivers';
			}
		} else {
			return ("id not found");
		}
		return redirect($redirecturl);
	}
	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		return Admin::form(Driver::class, function (Form $form) {

			$form->display('id', 'ID');
			$country = Country::pluck('name', 'id');
			$service_category = VehicleCategory::pluck('vehicle_type', 'vehicle_type');
			$status = Status::pluck('status', 'id');
			$form->text('name', 'First Name *')->rules('required');
			$form->text('last_name', 'Last Name *')->rules('required');
			$form->email('email', 'Email *')->rules(function ($form) {

				// If it is not an edit state, add field unique verification
				if (!$id = $form->model()->id) {
					return 'unique:drivers,email';
				} else {
					return 'unique:drivers,email,' . $form->model()->id;
				}

			});

			$form->text('phone_number', 'Phone Number *')->rules(function ($form) {

				// If it is not an edit state, add field unique verification
				if (!$id = $form->model()->id) {
					return 'required|regex:/^\+[0-9]+$/|min:10|unique:drivers,phone_number';
				} else {
					return 'required|regex:/^\+[0-9]+$/|min:10|unique:drivers,phone_number,' . $form->model()->id;
				}

			})->help('<span style="color:#dd4b39;">Phone Number like it should be added with country code and + symbol and Hyphen eg. (+91-9757879366)</span>');
			$form->tools(function (Form\Tools $tools) {

				// Disable `List` btn.
				//$tools->disableList();

				// Disable `Delete` btn.
				$tools->disableDelete();

				// Disable `Veiw` btn.
				$tools->disableView();
				//$tools->disableAction();
				// Add a button, the argument can be a string, or an instance of the object that implements the Renderable or Htmlable interface
				//$tools->add('<a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;delete</a>');
			});
			// 	if (!$id = $form->model()->id) {
			// 		$form->password('password', 'Password')->help('Note! Password must be atleast 8 characters with atleast 1 number and alphabets')->rules(function ($form) {

			// 			// If it is not an edit state, add field unique verification
			// 			if (!$id = $form->model()->id) {
			// 				return ['required',
			// 					//'min:8',
			// 					'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).{8,16}$/'];
			// 			} else {
			// 				return ['required',
			// 					//'min:8',
			// 					'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).{8,16}$/'];
			// 			}

			// 		});
			// }
			$form->password('password', 'Password *')->help('Note! Password must be atleast 8 characters with atleast 1 number and alphabets')->rules(function ($form) {

				// If it is not an edit state, add field unique verification
				if (!$id = $form->model()->id) {
					return 'required|min:8|alpha_num';
				} else {
					return 'required|min:8';
				}

			});
			// $form->text('password', 'Password')->rules(function ($form) {

			// 	// If it is not an edit state, add field unique verification
			// 	if (!$id = $form->model()->id) {
			// 		return ['required',
			// 			'min:8',
			// 			'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).*$/'];
			// 	} else {
			// 		return ['required',
			// 			'min:8',
			// 			'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).*$/'];
			// 	}

			// });

			// $form->password('password', 'Password')->rules('required|alpha_dash:8|')->rules('required|min:8|');
			//$form->date('dob', 'Date Of Birth')->rules("required|before:today");
			$form->date('dob', 'Date Of Birth');
			$form->text('vehicle_num', 'Vehicle Number *')->rules('required');
			$form->select('vehicle_type', 'Service Category *')->options($service_category)->rules('required');
			$form->text('license_no', 'Licence Number *')->rules('required');
			$form->image('id_proof')->move('uploads/DriverProof')->uniqueName();
			$form->image('photo')->move('uploads/DriverImages')->uniqueName();
			$form->textarea('address', 'Residence Address');
			$form->text('postal_code', 'Postal Code');
			$form->select('country_name', 'Country')->options($country)->load('state', '/admin/province', 'id', 'state_name');

			$form->select('state', "Province")->options(function ($id) {
				$province = State::find($id);

				if ($province) {
					return [$province->id => $province->state_name];
				}
			});
			$form->text('city', 'City');

			$form->hidden('vehicle_id');
			$form->hidden('otp');
			$form->select('status', 'Status')->options($status)->default(1)->rules('required');
			$form->select('approved', 'Approved')->options([1 => 'Approved', 0 => 'Not Approved']);
			$form->saving(function (Form $form) {
				\Log::info("saving");
				$digits = 4;
				$otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
				$form->otp = $otp;
				// if (!$id = $form->model()->id) {
				// 	$form->password = $this->cryptpass($form->password);
				// 	} else {
				// 	$form->ignore('password');
				// 	}

				// if($form->password && $form->model()->password != $form->password) {
				// 	$form->password = $this->cryptpass($form->password);
				// }
				if (!$id = $form->model()->id) {
					$form->password = $this->cryptpass($form->password);
				} else if ($form->model()->password != $form->password) {
					$form->password = $this->cryptpass($form->password);
				} else {
					$form->password = $form->password;
				}

				$form->vehicle_id = $this->getVehicleId($form->vehicle_type);
				$form->salt = "";
				// if ($form->password != '') {
				// 	$form->password = $this->cryptpass($form->password);
				// }

				$admin_check = $this->admin_checkin_process(\Request::segment(3), $form->vehicle_type, $form->vehicle_id, $form->model()->vehicle_id, $form->status);
				if ($admin_check) {
					$error = new MessageBag([
						'title' => 'Error',
						'message' => 'This driver is currently on trip,try again later.',
					]);
					return back()->with(compact('error'));
				}

			});
			$form->saved(function (Form $form) {
				\Log::info("saved");
				$email = $form->model()->email;
				$user = Driver::where('email', '=', $email)->first();
				if (is_object($user) && ($user->created_at == $user->updated_at)) {

					$username = Driver::select('name')->where('email', '=', $email)->first();
					$name = $username->name;
					$social_url = DB::table('settings')->first();
					$skype = $social_url->skype;
					$facebook = $social_url->facebook;
					$twitter = $social_url->twitter;
					$app_name = env("APP_NAME");
					$from_mail = env("MAIL_USERNAME");
					$website = $social_url->website_url;
					$email_to = $social_url->mail_to;
					$logo = $social_url->logo_url;
					$admin_mail = $social_url->email;

					$content = Email::select('content')->where('template_name', '=', 'Driver Signup')->first();

					$mail_header = array("name" => $name, 'skype' => $skype, 'facebook' => $facebook, 'twitter' => $twitter, 'website' => $website, 'email_to' => $email_to, 'logo' => $logo, 'app_name' => $app_name, 'admin_mail' => $admin_mail, 'content' => $content->content);
					Mail::send('mails.addDriver', $mail_header, function ($message)
						 use ($user, $from_mail, $app_name) {
							$message->from($from_mail, $app_name);
							$message->subject('Registration');
							$message->to($user->email);

						});

					$response['message'] = "Mail sent successfully";
					return $response;
				}

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

			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
		});
	}
	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function editform() {
		\Log::info("dadada");
		return Admin::form(Driver::class, function (Form $form) {

			$form->display('id', 'ID');
			$country = Country::pluck('name', 'id');
			$driver_id = \Request::segment(3);
			$state_id = Driver::where('id', $driver_id)->select('country_name')->first();
			$province = State::where('country_id', $state_id->country_name)->pluck('state_name', 'id');
			$service_category = VehicleCategory::pluck('vehicle_type', 'vehicle_type');
			$status = Status::pluck('status', 'id');

			$form->text('name', 'First Name *')->rules('required');
			$form->text('last_name', 'Last Name');
			$form->hidden('email', 'Email')->disable();
			$form->hidden('phone_number', 'Phone Number')->disable();
			// $form->password('password', 'Password')->help('Note! Password must be atleast 8 characters with atleast 1 number and alphabets')->rules(function ($form) {

			// 	                // If it is not an edit state, add field unique verification
			// 					if (!$id = $form->model()->id) {
			// 						return ['required',
			// 							//'min:8',
			// 							'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).{8,16}$/'];
			// 					} else {
			// 						return ['required',
			// 							//'min:8',
			// 							'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).{8,16}$/'];
			// 					}

			// 				});
			$form->password('password', 'Password *')->help('Note! Password must be atleast 8 characters with atleast 1 number and alphabets')->rules(function ($form) {

				// If it is not an edit state, add field unique verification
				if (!$id = $form->model()->id) {
					return 'required|min:8|alpha_num';
				} else {
					return 'required|min:8';
				}

			});
			/* $form->text('password', 'Password')->rules(function ($form) {

				            // If it is not an edit state, add field unique verification
				            if (!$id = $form->model()->id) {
				            return ['required',
				            'min:8',
				            'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).*$/'];
				            } else {
				            return ['required',
				            'min:8',
				            'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).*$/'];
				            }

			*/

			// $form->password('password', 'Password')->rules('required|alpha_dash:8|')->rules('required|min:8|');
			//	$form->ignore('password');
			//$form->date('dob', 'Date Of Birth')->rules("required|before:today");
			$form->date('dob', 'Date Of Birth');
			$form->text('vehicle_num', 'Vehicle Number');
			$form->select('vehicle_type', 'Service Category *')->options($service_category)->rules('required');
			$form->text('license_no', 'Licence Number *')->rules('required');
			$form->image('id_proof')->move('DriverProof')->uniqueName()->attribute(['id' => 'driverProof']);
			$form->image('photo')->move('DriverImages')->uniqueName()->attribute(['id' => 'driverPhoto']);
			//$form->image('photo')->move('DriverImages')->uniqueName();
			$form->textarea('address', 'Residence Address');
			$form->text('postal_code', 'Postal Code');
			$form->select('country_name', 'Country')->options($country)->load('state', '/admin/province', 'id', 'state_name');

			$form->select('state', "Province")->options($province)->value(function ($id) {
				$province = State::find($id);

				if ($province) {
					return [$province->id => $province->state_name];
				}
			});
			$form->text('city', 'City');

			$form->select('status', 'Status')->options($status)->rules('required');

			$form->tools(function (Form\Tools $tools) {

				// Disable `List` btn.
				//$tools->disableList();

				// Disable `Delete` btn.
				$tools->disableDelete();

				// Disable `Veiw` btn.
				$tools->disableView();
				//$tools->disableAction();
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

			$form->display('created_at', 'Created At');
			$form->display('updated_at', 'Updated At');
		});
	}

	public function cryptpass($input, $rounds = 12) {
		$salt = "";
		$saltchars = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
		for ($i = 0; $i < 22; $i++) {
			$salt .= $saltchars[array_rand($saltchars)];
		}
		return crypt($input, sprintf('$2y$%2d$', $rounds) . $salt);
	}

	public function getVehicleId($type) {

		$id = VehicleCategory::where('vehicle_type', $type)->value('id');
		if ($id) {
			return $id;
		} else {
			return 0;
		}
	}

	public function admin_checkin_process($id, $vehicle_type, $vehicle_id, $old_vehicle, $status) {

		if ($id != '') {
			$usercount = DB::table('driver_checkins')->where([['driver_id', '=', $id]])->count();
			$users = DB::table('driver_checkins')->where([['driver_id', '=', $id], ['booking_status', '!=', '1']])->count();
			if ($usercount != '0') {
				if ($users > 0) {

					$serviceAccount = ServiceAccount::fromJsonFile(public_path() . '/' . env('FIREBASE_KEY'));
					$firebase = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DB'))->create();
					$database = $firebase->getDatabase();

					if ($vehicle_id != $old_vehicle) {

						$upd = ['drivers_location/' . $old_vehicle . '/' . $id . '/l/0' => 0,
							'drivers_location/' . $old_vehicle . '/' . $id . '/l/1' => 0,
							'drivers_location/' . $old_vehicle . '/' . $id . '/status' => "0"];

						$newpost = $database->getReference()
							->update($upd);

						$stat = '2';
					} else if ($status == '2') {
						$stat = '3';
					} else {
						$stat = '1';
					}

					$updates = [
						'drivers_status/' . $id . '/admin_status' => $stat,
						'drivers_status/' . $id . '/category' => $vehicle_type,
						'drivers_status/' . $id . '/categoryid' => $vehicle_id,
					];
					$newpost = $database->getReference()
						->update($updates);
					return false;
				} else {
					return true;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

}
