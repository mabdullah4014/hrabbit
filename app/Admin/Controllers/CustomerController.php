<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\CustomerExport;
use App\Country;
use App\Customer;
use App\DriverTrip;
use App\Http\Controllers\Controller;
use App\State;
use App\Status;
use App\Email;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Mail;
use App\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class CustomerController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {

			$content->header('Customer');
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

			$content->header('Customer');
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

			$content->header('Customer');
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
		return Admin::grid(Customer::class, function (Grid $grid) {
			
			$grid->model()->where('member_type', null, false);
			
			$grid->id('ID')->sortable();
			$grid->name('First Name')->sortable();
			$grid->last_name('Last Name')->sortable();
			$grid->phone_number('Mobile Number')->sortable();
			// ->display(function ($phone_number){
			// 	$phone=str_replace(substr($phone_number, 4,5), $this->hide_phone($phone_number), $phone_number);		
			// 	return $phone;		
			// })
			$grid->email('Email')->display(function ($email){
				//$hidden_email = $this->hide_email($email);
				return $email;
			})->sortable();
			$grid->status('Status')->display(function ($status) {
				$value = Status::find($status)->status;
				if ($status == 1) {
					return "<span class='label label-success'>$value</span>";
				} else {
					return "<span class='label label-danger'>$value</span>";
				}
			});

			$grid->user_photo()->display(function ($image) {
				$base_url=env('APP_URL');
				$external_link = $base_url . "/" .$image;
				//$external_link       =  . "images/customer/" . $image;
				//$external_link_admin = $_ENV['APP_URL'] . "/uploads/" . $image;
				/*$external_link = "/images/customer/" . $image;
				$external_link_admin = "/uploads/" . $image;
				if (@getimagesize($external_link)) {
					$path = $external_link;
				} elseif (@getimagesize($external_link_admin)) {
					$path = $external_link_admin;
				} else {
					$path = '';
				}*/
				$path = str_replace('public', '/CustomerImages', $image);
				if($image==''){
					
					//$path = $external_link_admin;
					$path= env('APP_URL')."/uploads/DriverProof/dummy_user_icon.png";
				}
				elseif($image!=''){
					if (@getimagesize($external_link)) {
						$path = $external_link;
					} else  {
						$path = env('APP_URL')."/uploads/DriverProof/dummy_user_icon.png";
					}
					//$path='/'.$image;
				}
				// else{
				// 	$path='/'.$image;
				// }
				//$path = '/storage/CustomerImages/' . $image;
				//if ($image != '') {
					return '<img src= "' . $path . '" alt="Photo" width="75" class="img img-thumbnail" />';
				//}

			});
			/*$grid->status('Status')->display(function($status) {
				                 $value = Status::find($status)->status;
				                if($status == 1){
				                     return "<span class='label label-success'>$value</span>";
				                }else{
				                     return "<span class='label label-danger'>$value</span>";
				                }
			*/
			$grid->created_at();
			$grid->updated_at();
			$grid->filter(function ($filter) {

				// Remove the default id filter
				$filter->disableIdFilter();

				$status = Status::pluck('status', 'id');

				// Add a column filter
				$filter->like('name', 'First Name');
				$filter->like('last_name', 'Last Name');
				$filter->like('phone_number', 'Phone Number');
				$filter->like('email', 'Email Address');
				$filter->equal('status', 'Status')->select($status);
				//$filter->like('created_at', 'Registered date')->date();
				$filter->between('created_at', 'Registration Dates')->date();
				
			});
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
			$grid->actions(function ($actions) {
				$actions->disableView();
				$actions->disableDelete();
			//	$actions->append('<a href="/admin/DeleteCustomer/' . $actions->getKey() . '"> <i class="fa fa-trash" aria-hidden="true"></i>  </a>');
			});
			$grid->exporter(new CustomerExport());
			
		});
	}
	public function DeleteCustomer($id) {
		
		if ($id != '') {
			$inv = Customer::find($id);
		
			if ($inv) {
				$trips=DriverTrip::where('cus_id',$id)->whereIN('status',[1,2,3,4])->get();
				if(count($trips)==0){
				$inv->status =2;
				$inv->save();
				$redirecturl='admin/customers';
				admin_toastr('Customer is inactive now', 'success');
				}
				else{
					admin_toastr('Oops,Customer is on Trip', 'error');
					$redirecturl='admin/customers';	
				}
			} else{
				$inv->status =2;
				$inv->save();
				$redirecturl='admin/customers';
			}
		}else{
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
		return Admin::form(Customer::class, function (Form $form) {

			//Data get from model
			$country = Country::pluck('name', 'id');
			$status = Status::pluck('status', 'id');
			$form->display('id', 'ID');
			$form->text('name', 'First Name *')->rules("required");
			$form->text('last_name', 'Last Name *')->rules('required');
		
			// $form->password('password', 'Password')->help('Note! Password must be atleast 8 characters with atleast 1 number and alphabets')->rules(function ($form) {

			// 	// If it is not an edit state, add field unique verification
			// 	if (!$id = $form->model()->id) {
			// 		return ['required',
			// 			//'min:8',
			// 			'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).{8,16}$/'];
			// 	} else {
			// 		return ['required',
			// 			//'min:8',
			// 			'regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X]).{8,16}$/'];
			// 	}

			// });
			
		
			//$form->text('password');
			// $form->password('password', 'Password')->rules('required|alpha_num:8|')->rules('required|min:8|');
			$form->image('user_photo')->move('/uploads/CustomerImages')->uniqueName();
			$form->email('email', 'Email *')->rules(function ($form) {

				// If it is not an edit state, add field unique verification
				if (!$id = $form->model()->id) {
					return 'required|unique:customers,email|unique:drivers,email';
				} else {
					return 'required|unique:drivers,email|unique:customers,email,'.$form->model()->id;
				}

			});
			$form->password('password', 'Password *')->help('Note! Password must be atleast 8 characters with atleast 1 number and alphabets')->rules(function ($form) {

				// If it is not an edit state, add field unique verification
				if (!$id = $form->model()->id) {
					return 'required|min:8|alpha_num';
				} else {
					return 'required|min:8';				
				}

			});

			$form->text('phone_number', 'Phone Number *')->rules(function ($form) {

				// If it is not an edit state, add field unique verification
				if (!$id = $form->model()->id) {
					return 'required|regex:/^\+[0-9]+$/|min:10|unique:customers,phone_number|unique:drivers,phone_number';
				} else {
					return 'required|regex:/^\+[0-9]+$/|min:10|unique:drivers,phone_number|unique:customers,phone_number,'.$form->model()->id;
				}
			})->help('<span style="color:#dd4b39;">Phone Number like it should be added with country code and + symbol and Hyphen eg. (+91-9757879366)</span>');
			$form->hidden('otp');
			//$form->date('cus_dob', 'Date Of Birth')->rules("required|before:today");
			$form->date('cus_dob', 'Date Of Birth');
			$form->textarea('address', 'Residence Address');
			$form->select('country', 'Country')->options($country)->load('state', '/admin/province', 'id', 'state_name');
			$form->select('state', "Province")->options(function ($id) {
				$province = State::find($id);
				if ($province) {
					return [$province->id => $province->state_name];
				}
			});
			$form->select('status', 'Status')->options($status)->default(1)->rules('required');

			$form->text('city', 'City');
			$form->text('postal_code', 'Postal Code');
			/* $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');*/
			$form->saving(function (Form $form) {
				$digits = 4;
				$otp=rand(pow(10, $digits - 1), pow(10, $digits) - 1);
				$form->otp=$otp;
				$form->salt = "";

			//	$form->password = $this->cryptpass($form->password);
			// if (!$id = $form->model()->id) {
			// 	$form->password = $this->cryptpass($form->password);
			// 	} else {
			// 	$form->ignore('password');
			// 	}
			// if($form->password && $form->model()->password != $form->password)
			// {
			// $form->password = $this->cryptpass($form->password);
			// }
			// });
				 if (!$id = $form->model()->id) {
			 	$form->password = $this->cryptpass($form->password);
			 	} else if($form->model()->password != $form->password){
			 		$form->password = $this->cryptpass($form->password);
			 	}else {
			 	   $form->password = $form->password;
			 	}
			});
			$form->saved(function (Form $form) {
				$email = $form->model()->email;
				$user = Customer::where('email', '=', $email)->first();
				if (is_object($user) && ($user->created_at == $user->updated_at)) {
					
					$username = Customer::select('name')->where('email', '=', $email)->first();
					$name = $username->name;								
					$social_url=DB::table('settings')->first();
					$skype=$social_url->skype;
					$facebook=$social_url->facebook;
					$twitter=$social_url->twitter;
					$app_name=env("APP_NAME");
					$from_mail=env("MAIL_USERNAME");
					$website=$social_url->website_url;
					$email_to=$social_url->mail_to;
					$logo=$social_url->logo_url;
					$admin_mail=$social_url->email;
					
					$content=Email::select('content')->where('template_name', '=', 'Customer Signup')->first();
										
					$mail_header = array("name" => $name,'skype'=>$skype,'facebook'=>$facebook,'twitter'=>$twitter,'website'=>$website,'email_to'=>$email_to,'logo'=>$logo,'app_name'=>$app_name,'admin_mail'=>$admin_mail,'content'=>$content->content );
					Mail::send('mails.addCustomer', $mail_header, function ($message)
						 use ($user,$from_mail, $app_name) {
							$message->from($from_mail, $app_name);
							$message->subject('Registration');
							$message->to($user->email);

						});
					

					$response['message'] = "Mail sent successfully";
					return $response;
				}

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
	}/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function editform() {
		return Admin::form(Customer::class, function (Form $form) {

			//Data get from model
			$country = Country::pluck('name', 'id');
			$cus_id = \Request::segment(3);
			$state_id = Customer::where('id',$cus_id)->select('country')->first();
			$province = State::where('country_id',$state_id->country)->pluck('state_name','id');
			$status = Status::pluck('status', 'id');
			$form->display('id', 'ID');
			$form->text('name', 'First Name *')->rules("required");
			$form->text('last_name', 'Last Name *')->rules('required');
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
			
			// $form->password('password', 'Password')->rules('required|alpha_num:8|')->rules('required|min:8|');
			//$form->ignore($form->password);
			$form->image('user_photo')->move('/uploads/CustomerImages')->uniqueName();
			// ->disable()
			$form->text('email', 'Email')->disable();

			$form->text('phone_number', 'Phone Number')->disable();
			$form->password('password', 'Password *')->help('Note! Password must be atleast 8 characters with atleast 1 number and alphabets')->rules(function ($form) {

				                // If it is not an edit state, add field unique verification
								if (!$id = $form->model()->id) {
					return 'required|min:8|alpha_num';
				} else {
					return 'required|min:8';				
				}

							});
			// ->disable()
			// $form->hidden('phone_number', 'Phone Number *');
			//$form->date('cus_dob', 'Date Of Birth')->rules("required|before:today");
			$form->date('cus_dob', 'Date Of Birth');
			$form->textarea('address', 'Residence Address');
			$form->select('country', 'Country')->options($country)->load('state', '/admin/province', 'id', 'state_name');
			$form->select('state', "Province")->options($province)->value(function ($id) {
				$province = State::find($id);	

				if ($province) {
					return [$province->id => $province->state_name];
				}
			});
			
			$form->text('city', 'City');
			$form->text('postal_code', 'Postal Code');
			$form->select('status', 'Status')->options($status)->rules('required');
			/* $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');*/
			$form->saving(function (Form $form) {

				$form->salt = "";

				//$form->password = $this->cryptpass($form->password);
				if($form->password && $form->model()->password != $form->password)
				{
				$form->password = $this->cryptpass($form->password);
				}

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

	function cryptpass($input, $rounds = 12) {
		$salt = "";
		$saltchars = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
		for ($i = 0; $i < 22; $i++) {
			$salt .= $saltchars[array_rand($saltchars)];
		}
		return crypt($input, sprintf('$2y$%2d$', $rounds) . $salt);
	}
}
