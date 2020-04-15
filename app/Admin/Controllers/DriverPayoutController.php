<?php

namespace App\Admin\Controllers;

use App\AppSetting;
use App\Currency;
use App\Admin\Extensions\DriverPayoutExport;
use App\Admin\Extensions\MassPay;
use App\BankPayouts;
use App\Driver;
use App\DriverPayout;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Request;

class DriverPayoutController extends Controller {
	use HasResourceActions;

	/**
	 * Index interface.
	 *
	 * @param Content $content
	 * @return Content
	 */
	public function index(Content $content) {
		return $content
			->header('Driver Payout')
			->description('View')
			->body($this->grid());
	}

	public function payout_report(Content $content) {
		return $content
			->header('Payout Report')
			->description('View')
			->body($this->grid_payout());
	}

	/**
	 * Show interface.
	 *
	 * @param mixed $id
	 * @param Content $content
	 * @return Content
	 */
	public function show($id, Content $content) {
		return $content
			->header('Detail')
			->description('description')
			->body($this->detail($id));
	}

	/**
	 * Edit interface.
	 *
	 * @param mixed $id
	 * @param Content $content
	 * @return Content
	 */
	public function edit($id, Content $content) {
		return $content
			->header('Edit')
			->description('description')
			->body($this->form()->edit($id));
	}

	/**
	 * Create interface.
	 *
	 * @param Content $content
	 * @return Content
	 */
	public function create(Content $content) {
		return $content
			->header('Pay to Bank')
			->description('')
			->body($this->form());
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		$grid = new Grid(new Driver);

		$grid->id('Id');
		$grid->name('Driver Name');
		$grid->type('Default Payment')->display(function ($default_payment) {
			return ucfirst($default_payment);
		});
		$grid->column('Credit')->display(function () {
			if ($this->wallet > 0) {
				return round($this->wallet, 2);
			} else {
				return "-";
			}

		});
		$grid->column('Debit')->display(function () {
			if ($this->wallet < 0) {
				return round($this->wallet, 2);
			} else {
				return "-";
			}

		});

		$grid->column('Pay to Paypal')->display(function () {
			$paypal = BankPayouts::where('driverid', $this->id)->where('type', 'paypal')->first();

			if (is_object($paypal) AND $this->wallet > 0) {
				?>
				<script>
				    $("#paypal_pay<?php echo $this->id; ?>").on("click", function(){
				        return confirm("Are you sure want to pay using PayPal?");
				    });
				</script>
				<?php
				return '<a href="pay_with_paypal/' . $this->id . '"<button class="btn btn-success" id="paypal_pay' . $this->id . '">Pay</button></a>';

			}
				
		}); 
		$grid->column('Pay to Bank')->display(function () {
			$bank = BankPayouts::where('driverid', $this->id)->where('type', 'bank')->first();
			if (is_object($bank) AND $this->wallet > 0) {
				return '<a href="pay_to_driver/create?driver_id=' . $this->id . '"<button class="btn btn-success">Pay</button></a>';
			}
		});

		$grid->disableActions();
		$grid->disableCreateButton();
		$grid->disableExport();
		$grid->filter(function ($filter) {
			$filter->disableIdFilter();
			$filter->like('name', 'Driver Name');
			//$filter->equal('default_payment', 'Default Payment')->select(["bank" => "Bank", "paypal" => "Paypal"]);
			$filter->between('created_at', 'Created Date')->date();	
		});
		$grid->tools(function ($tools) {

			$tools->batch(function ($batch) {
				$batch->disableDelete();
				//$batch->add('Click To Payment', new MassPay(1));
			});
		});
		return $grid;
	}

	protected function grid_payout() {

		$grid = new Grid(new DriverPayout);

		$grid->id('Id');
		$grid->driver_id('Driver Name')->display(function ($driver_id) {
			return Driver::where('id', $driver_id)->value('name');
		});
		$grid->type('Type');
		//$grid->amount('Amount');

		$grid->amount()->display(function ($amount) {
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				if (isset($amount)) {
					if (is_object($currency)) {
						return $currency->symbol . " " . round($amount, 2);
					} else {
						return round($amount, 2);
					}
				} else {
					return 0;
				}
			});

		$grid->ref_no('Reference Number');
		$grid->date('Date');

		$grid->actions(function ($actions) {
			$actions->disableDelete();
			$actions->disableEdit();
			$actions->disableView();
			$actions->append('<a title="View" href="viewReport?id=' . $actions->getKey() . '"><i class="fa fa-eye"></i></a>');
		});
		$grid->disableCreateButton();
		$grid->tools(function ($tools) {
			$tools->batch(function ($batch) {
				$batch->disableDelete();
			});
		});
		$grid->filter(function ($filter) {
			$drivers = Driver::pluck('name', 'id');			
			$filter->like('ref_no', 'Reference Number');
			$filter->equal('driver_id', 'Driver')->select($drivers);
			$filter->equal('type', 'Type')->select(["bank" => "Bank", "paypal" => "Paypal"]);
			$filter->between('created_at', 'Date')->date();	
		});
		$grid->exporter(new DriverPayoutExport());
		return $grid;
	}

	public function viewReport(Request $request) {

		return Admin::content(function (Content $content) {

			$content->header('Payout Report');
			$content->description('View...');

			$content->row(function (Row $row) {
				$script = <<<'EOT'
$('.form-history-back').on('click', function (event) {
    event.preventDefault();
    history.back(1);
});
EOT;
				Admin::script($script);
				$text = trans('admin.back');
				$link = <<<EOT
					<div class="btn-group pull-right" style="margin-right: 10px">
						<a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;$text</a>
					</div>
EOT;
				$inv = DriverPayout::where('id', '=', $_REQUEST['id'])->firstOrFail();
				$curr = AppSetting::select('currency')->first();
				$currency = Currency::select('currency', 'symbol')->where('id', $curr->currency)->first();
				$headers = [];
				if($inv->type == 'paypal'){					
				$rows = [
					['Driver Name', $inv->driver->name],
					['Type', $inv->type],
					['Paypal Email', $inv->driver->email],
					['Reference Number', $inv->ref_no],
					['Amount', ($inv->amount != '') ? $currency->symbol . " " . round($inv->amount,2) : "-"],						
					['Date', $inv->date],
				];
				}else{
				$rows = [
					['Driver Name', $inv->driver->name],
					['Type', $inv->type],					
					['Reference Number', $inv->ref_no],
					['Amount', ($inv->amount != '') ? $currency->symbol . " " . round($inv->amount,2) : "-"],					
					['Date', $inv->date],
				];
				}

				$table = new Table($headers, $rows);

				$box = new Box('Report', $table);
				$box->style('default');
				$box->solid();
				$row->column(12, $link);
				$row->column(3, '');
				$row->column(6, $box);
				$row->column(3, '');
			});
		});
	}

	/**
	 * Make a show builder.
	 *
	 * @param mixed $id
	 * @return Show
	 */
	protected function detail($id) {
		$show = new Show(DriverPayout::findOrFail($id));

		$show->id('Id');
		$show->driver_id('Driver id');
		$show->amount('Amount');
		$show->type('Type');
		$show->ref_no('Ref no');
		$show->date('Date');
		$show->status('Status');

		return $show;
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
		$form = new Form(new DriverPayout);
		$id = $_REQUEST['driver_id'];
		$rules = 'max:10';
		$data = Driver::where('id', $id)->first();
		$form->display('Driver id')->value($id);
		$form->number('amount', 'Amount')->value($data->wallet)->min(1)->max($data->wallet);
		$form->display('type', 'Type')->value('Bank');
		$form->text('ref_no', 'Ref no')->rules('required');
		$form->display('date', 'Date')->value(date('Y-m-d h:i:a'));

		$form->hidden('driver_id')->value($id);
		$form->hidden('type')->value('Bank');
		$form->hidden('status')->value('1');
		$form->hidden('date')->value(date('Y-m-d H:i:s'));

		$form->saved(function (Form $form) {
			$remaining = Driver::where('id', $form->model()->driver_id)->value('wallet') - $form->model()->amount;

			$this->update_wallet_amount($form->model()->driver_id, $remaining);
			admin_toastr('Successfully Paid', 'success');
			return redirect('/admin/pay_to_driver');
			return redirect('/admin/pay_to_driver');
		});
		?>
		<script>
		    $(".btn-primary").on("click", function(){
		        return confirm("Are you sure want to pay using Bank?");
		    });
		</script>
		<?PHP
		$form->footer(function ($footer) {
			$footer->disableViewCheck();
			$footer->disableEditingCheck();
			$footer->disableCreatingCheck();
		});
		return $form;
	}
	public function access_token() {
		//    $credentials=DB::table('adaptive_paypal_settings')->first();
		//    $Username=$credentials->paypal_client_id;
		//    $Password=$credentials->paypal_secret;
		//     $curl = curl_init();

		//     curl_setopt_array($curl, array(
		//         CURLOPT_URL => "https://api.sandbox.paypal.com/v1/oauth2/token ",
		//       //CURLOPT_URL => "https://api.sandbox.paypal.com/v1/identity/openidconnect/tokenservice",
		//       CURLOPT_RETURNTRANSFER => true,
		//       CURLOPT_CUSTOMREQUEST => "POST",
		//     // CURLOPT_POSTFIELDS => "grant_type=client_credentials",
		//     CURLOPT_POSTFIELDS=>array(
		//         'Username' => $Username,
		//        // 'redirect_uri' => $callback,
		//         'Password' => $Password,
		//         'grant_type' => 'client_credentials'
		//     ),
		//       CURLOPT_HTTPHEADER => array(
		//         "authorization: Basic QVJ1NURQUm9TMmhzWmpIMnJOVVFVSHN5UXk1OThHeG9MTGtiTVB2UVZRdVY4ZW9vY1FOdEFGZDFlTDR6ZDJNV09pUDA3eVI0NjJLWUlYUnU6RUd2eG9LUVNLdk9JcWhuNDdMbmpybHpUWmRGb0pPRGw3U1JMUUlLeTVpUngzTlNjNTlheFBScXFFOGlQbDh0NTU0Z2ZUbnlGWUtkemlUQmc="
		//         ),
		//     ));
		//     $response = curl_exec($curl);
		//     $err = curl_error($curl);
		//     curl_close($curl);
		//     if ($err) {
		//       echo "cURL Error #:" . $err;
		//     } else {
		//         $results = json_decode($response);
		//         return $response;
		//     }

		$ch = curl_init();
		$clientId = "AW9u4FmsAcUkEuQFmjprfEO_8VSedoLrclmbcfeIjN82oHdhhUnI53s0ArWKjS6L3Pax2WwZhaXTw1pb";
		$secret = 'EJNsmCAI56fEbRN01taffH8QeEcUu_xdXRHl1RRfr_2eg_0F-YjGqqr0sV1Ks1S9HsaxJCpUkvRwRD87';

		curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6); //NEW ADDITION
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

		$result = curl_exec($ch);

		if (empty($result)) {
			die("Error: No response.");
		} else {
			return $result;
			// $json = json_decode($result);
			// print_r($json->access_token);
		}

		curl_close($ch);
	}

	public function single_payment($id) {

		/********************************/
		$sender_batch_id = "91205" . time();
		$email_subject = "You have a payout!";
		$email_message = "You have received a payout! Thanks for using our service!";

		/********************************/
		$receivers = array();
		$recipient_type = "EMAIL";
		$value1 = $this->get_driver_wallet($id);
		$value = round($value1, 2);
		$currency = "USD";
		$note = "Thanks for your patronage!";
		$sender_item_id = "91206" . time();
		$receiver = $this->getPaypalEmail($id);

		/********************************/
		$response = $this->access_token();
		$response1 = json_decode($response, true);

		$access = $response1['access_token'];
		//          print_r($access);
		//    exit;
		//$access_token = "A21AAEboDiAhqbaxKi0TwAYODZGIv01-TrIKNbp7kVTXvBKkJlwzVwVTbJG7iVh39i89W4LjIcoUZwf6xaiE3LXJlxd6AkTTQ";
		$access_token = $access;
		// print_r($access_token);exit;
		//$receiver = "sarath-buyer-123@gmail.com";
		// $value = "10";
		//echo $sender_batch_id."-".$email_subject."-".$email_message."-".$recipient_type."-".$value."-".$currency."-".$note."-".$sender_item_id."-".$receiver;
		//exit;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.sandbox.paypal.com/v1/payments/payouts",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\n\"sender_batch_header\": {\n\"sender_batch_id\": \"$sender_batch_id\",\n\"email_subject\": \"$email_subject\",\n\"email_message\": \"$email_message\"\n},\n\"items\": [\n{\n\"recipient_type\": \"$recipient_type\",\n\"amount\": {\n\"value\": \"$value\",\n\"currency\": \"$currency\"\n},\n\"note\": \"$note\",\n\"sender_item_id\": \"$sender_item_id\",\n\"receiver\": \"$receiver\"\n}\n]\n}",
			//CURLOPT_POSTFIELDS => $post_params,
			CURLOPT_HTTPHEADER => array(
				"authorization: Bearer $access_token",
				"content-type: application/json",
				"postman-token: cb8a6de6-26b2-cf9e-213b-38392376a5d8",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$results = json_decode($response);

			if (@$results->batch_header->payout_batch_id) {
				if (@$this->update_wallet_details($id, $value, $results->batch_header->payout_batch_id, $results)) {
					$wallet = $this->get_driver_wallet($id);
					$deduct_amount = round($wallet, 1) - round($value, 1);
					$this->update_wallet_amount($id, $deduct_amount);
					admin_toastr('Successfully Paid', 'success');
					return redirect('/admin/pay_to_driver');
				}
			} else {
				admin_toastr('Something went wrong', 'warning');
				return redirect('/admin/pay_to_driver');
			}
		}
	}

	public function getPaypalEmail($id) {
		return BankPayouts::where('type', 'paypal')->where('driverid', $id)->value('bank_email');
	}

	public function get_driver_wallet($id) {
		return Driver::where('id', $id)->value('wallet');
	}

	public function update_wallet_details($id, $amount, $ref, $results) {
		$driver_payout = new DriverPayout;
		$driver_payout->driver_id = $id;
		$driver_payout->amount = $amount;
		$driver_payout->type = 'paypal';
		$driver_payout->ref_no = $ref;
		$driver_payout->date = date('Y-m-d H:i:s');
		$driver_payout->status = 1;
		$driver_payout->paypal_results = serialize($results);
		$driver_payout->save();
		return TRUE;
	}

	public function update_wallet_amount($driver_id, $amount) {
		Driver::where('id', $driver_id)->update([
			'wallet' => $amount,
		]);
	}
}
