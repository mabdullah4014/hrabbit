<?php

namespace App\Http;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class Authorize {

	public static function chargeCustomerProfile($profileid, $paymentprofileid, $amount, $details = null) {
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
		$refId = 'ref' . time();
		
		$data = [];
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
					$data["transaction"] = [];
					$data["transaction"]['messages'] = [];
					$data["transaction"]['transId'] = $tresponse->getTransId();
					$data["resultCode"] = "Ok";
					$data["message"] = [];
					$data["message"]["text"] = $response->getMessages()->getMessage()[0]->getText();
					$data["message"]["code"] = $response->getMessages()->getMessage()[0]->getCode();
				} else {
					\Log::info("Transaction Failed \n");
					if ($tresponse->getErrors() != null) {
						\Log::info(" Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n");
						\Log::info(" Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n");
						$data["resultCode"] = "Error";
						$data["message"] = [];
						$data["message"]["text"] = $tresponse->getErrors()[0]->getText();
						$data["message"]["code"] = $tresponse->getErrors()[0]->getCode();
					}
				}
			} else {
				\Log::info("Transaction Failed \n");
				$tresponse = $response->getTransactionResponse();
				if ($tresponse != null && $tresponse->getErrors() != null) {
					\Log::info(" Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n");
					\Log::info(" Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n");
					$data["resultCode"] = "Error";
					$data["message"] = [];
					$data["message"]["text"] = $tresponse->getErrors()[0]->getErrorText();
					$data["message"]["code"] = $tresponse->getErrors()[0]->getErrorCode();
				} else {
					\Log::info(" Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n");
					\Log::info(" Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n");
					$data["resultCode"] = "Error";
					$data["message"] = [];
					$data["message"]["text"] = $response->getMessages()->getMessage()[0]->getText();
					$data["message"]["code"] = $response->getMessages()->getMessage()[0]->getCode();
				}
			}
		} else {
			\Log::info("No response returned \n");
		}

		return $data;
	}

	public static function getCustomerProfile($profileId) {
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
		$refId = 'ref' . time();

		$request = new AnetAPI\GetCustomerProfileRequest();
		$request->setMerchantAuthentication($merchantAuthentication);
		$request->setCustomerProfileId($profileId);
		$controller = new AnetController\GetCustomerProfileController($request);
		$data = [];
		if (!$isProd) {
			$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
		} else {
			$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
		}
		if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
		{
			\Log::info("GetCustomerProfile SUCCESS : " .  "\n");
			$profileSelected = $response->getProfile();
			$paymentProfilesSelected = $profileSelected->getPaymentProfiles();
			\Log::info("Profile Has " . count($paymentProfilesSelected). " Payment Profiles" . "\n");
			
			if($response->getSubscriptionIds() != null) 
			{
				\Log::info("List of subscriptions:");
				foreach($response->getSubscriptionIds() as $subscriptionid)
				\Log::info($subscriptionid . "\n");
			}
			$array = json_decode(json_encode($profileSelected), true);
			$data["customerProfile"] = $array;
			$data["resultCode"] = "Ok";
			$data["message"] = [];
			$data["message"]["text"] = $response->getMessages()->getMessage()[0]->getText();
			$data["message"]["code"] = $response->getMessages()->getMessage()[0]->getCode();
		}
		else
		{
			\Log::info("ERROR :  GetCustomerProfile: Invalid response\n");
			$errorMessages = $response->getMessages()->getMessage();
			\Log::info("Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n");
			$data["resultCode"] = "Error";
			$data["message"] = [];
			$data["message"]["text"] = $errorMessages[0]->getText();
			$data["message"]["code"] = $errorMessages[0]->getCode();
		}
		return $data;
	}
	
	public static function refundTransaction($cardNumber, $expiryDate, $amount, $refTransactionId) {
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
		$refId = 'ref' . time();
		$data = [];
		
		
		$creditCard = new AnetAPI\CreditCardType();
		$creditCard->setCardNumber($cardNumber);
		$creditCard->setExpirationDate($expiryDate);
		$paymentOne = new AnetAPI\PaymentType();
		$paymentOne->setCreditCard($creditCard);
		
		$transactionRequest = new AnetAPI\TransactionRequestType();
		$transactionRequest->setTransactionType( "refundTransaction"); 
		$transactionRequest->setAmount($amount);
		$transactionRequest->setPayment($paymentOne);
		$transactionRequest->setRefTransId($refTransactionId);
		
		
		$request = new AnetAPI\CreateTransactionRequest();
		$request->setMerchantAuthentication($merchantAuthentication);
		$request->setRefId($refId);
		$request->setTransactionRequest( $transactionRequest);
		$controller = new AnetController\CreateTransactionController($request);
		if (!$isProd) {
			$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
		} else {
			$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
		}

		if ($response != null)
		{
			if($response->getMessages()->getResultCode() == "Ok")
			{
				$tresponse = $response->getTransactionResponse();
				
				if ($tresponse != null && $tresponse->getMessages() != null)   
				{
					\Log::info("Transaction Response code : " . $tresponse->getResponseCode() . "\n");
					\Log::info("Refund SUCCESS: " . $tresponse->getTransId() . "\n");
					\Log::info("Code : " . $tresponse->getMessages()[0]->getCode() . "\n"); 
					\Log::info("Description : " . $tresponse->getMessages()[0]->getDescription() . "\n");
					$data["transaction"] = [];
					$data["transaction"]['messages'] = [];
					$data["transaction"]['transId'] = $tresponse->getTransId();
					$data["resultCode"] = "Ok";
					$data["message"] = [];
					$data["message"]["text"] = $response->getMessages()->getMessage()[0]->getText();
					$data["message"]["code"] = $response->getMessages()->getMessage()[0]->getCode();
				}
				else
				{
					\Log::info("Transaction Failed \n");
					if($tresponse->getErrors() != null)
					{
						\Log::info("Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n");
						\Log::info("Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n");
						$data["resultCode"] = "Error";
						$data["message"] = [];
						$data["message"]["text"] = $tresponse->getErrors()[0]->getText();
						$data["message"]["code"] = $tresponse->getErrors()[0]->getCode();
					}
				}
			}
			else
			{
				\Log::info("Transaction Failed \n");
				$tresponse = $response->getTransactionResponse();
				if($tresponse != null && $tresponse->getErrors() != null)
				{
					\Log::info("Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n");
					\Log::info("Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n");
					$data["resultCode"] = "Error";
					$data["message"] = [];
					$data["message"]["text"] = $tresponse->getErrors()[0]->getErrorText();
					$data["message"]["code"] = $tresponse->getErrors()[0]->getErrorCode();
				}
				else
				{
					\Log::info("Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n");
					\Log::info("Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n");
					$data["resultCode"] = "Error";
					$data["message"] = [];
					$data["message"]["text"] = $response->getMessages()->getMessage()[0]->getText();
					$data["message"]["code"] = $response->getMessages()->getMessage()[0]->getCode();
				}
			}      
		}
		return $data;
	}
}