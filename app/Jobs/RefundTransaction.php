<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RefundTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $transactions = \App\RefundTransaction::where('settled', false)->get();
        foreach ($transactions as $key => $transaction) {
            $trip = \App\DriverTrip::find($transaction->trip_id);
            if($trip){
                $customer = \App\Customer::find($trip->cus_id);
                if($customer){
                    $response = \App\Http\Authorize::getCustomerProfile($customer->customerProfileId);
                    if ($response != null) {
                        if ($response['resultCode'] == "Ok") {
                            $profile = $response['customerProfile'];
                            if ($profile != null) {
                                $cardDetails = $profile['paymentProfiles'][0]['payment']['creditCard'];
                                $refundResponse = \App\Http\Authorize::refundTransaction($cardDetails['cardNumber'], $cardDetails['expirationDate'], $transaction->amount, $trip->advance_transaction_id);
                                if ($refundResponse['resultCode'] == "Ok") {
                                    $transactionResp = $refundResponse['transaction'];
                                    if ($transactionResp != null) {
                                        $transaction->transaction_id = $transactionResp['transId'];
                                        $transaction->settled = true;
                                        $transaction->save();
                                        return;
                                    } 
                                }
                            } 
                        }
                    }
                }
            }
            $transaction->retries += 1;
            $transaction->save();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
