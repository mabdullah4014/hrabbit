<?php

namespace App\Http;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class Authorize {

	public static function chargeCustomerProfile($profileid, $paymentprofileid, $amount, $details = null) {
		/* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
		$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
		\Log::info(env('MERCHANT_LOGIN_ID'));
		\Log::info(env('MERCHANT_TRANSACTION_KEY'));

		$isProd = env('IS_AUTHORIZE_PROD');
		if ($isProd) {
			$merchantAuthentication->setName(env('MERCHANT_LOGIN_ID_PROD'));
			$merchantAuthentication->setTransactionKey(env('MERCHANT_TRANSACTION_KEY_PROD'));
		} else {
			$merchantAuthentication->setName(env('MERCHANT_LOGIN_ID'));
			$merchantAuthentication->setTransactionKey(env('MERCHANT_TRANSACTION_KEY'));
		}
		// Set the transaction's refId
		$refId = 'ref' . time();

		$profileToCharge = new AnetAPI\CustomerProfilePaymentType();
		$profileToCharge->setCustomerProfileId($profileid);
		$paymentProfile = new AnetAPI\PaymentProfileType();
		$paymentProfile->setPaymentProfileId($paymentprofileid);
		$profileToCharge->setPaymentProfile($paymentProfile);

		$transactionRequestType = new AnetAPI\TransactionRequestType();
		$transactionRequestType->setTransactionType("authCaptureTransaction");
		$transactionRequestType->setAmount($amount);
		$transactionRequestType->setProfile($profileToCharge);

		$request = new AnetAPI\CreateTransactionRequest();
		$request->setMerchantAuthentication($merchantAuthentication);
		$request->setRefId($refId);
		$request->setTransactionRequest($transactionRequestType);
		$controller = new AnetController\CreateTransactionController($request);
		if (!$isProd) {
			$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
		} else {
			$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
		}
		\Log::info(json_encode($response));
		if ($response != null) {
			if ($response->getMessages()->getResultCode() == "Ok") {
				$tresponse = $response->getTransactionResponse();

				if ($tresponse != null && $tresponse->getMessages() != null) {
					\Log::info(" Transaction Response code : " . $tresponse->getResponseCode() . "\n");
					\Log::info("Charge Customer Profile APPROVED  :" . "\n");
					\Log::info(" Charge Customer Profile AUTH CODE : " . $tresponse->getAuthCode() . "\n");
					\Log::info(" Charge Customer Profile TRANS ID  : " . $tresponse->getTransId() . "\n");
					\Log::info(" Code : " . $tresponse->getMessages()[0]->getCode() . "\n");
					\Log::info(" Description : " . $tresponse->getMessages()[0]->getDescription() . "\n");
				} else {
					\Log::info("Transaction Failed \n");
					if ($tresponse->getErrors() != null) {
						\Log::info(" Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n");
						\Log::info(" Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n");
					}
				}
			} else {
				\Log::info("Transaction Failed \n");
				$tresponse = $response->getTransactionResponse();
				if ($tresponse != null && $tresponse->getErrors() != null) {
					\Log::info(" Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n");
					\Log::info(" Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n");
				} else {
					\Log::info(" Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n");
					\Log::info(" Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n");
				}
			}
		} else {
			\Log::info("No response returned \n");
		}

		return $response;
	}
}