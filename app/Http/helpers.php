<?php

use Illuminate\Support\Str;

if (!function_exists('send_sms')) {
	function send_sms($body, $to) {
		\Log::info(Str::random(16));
		// $data = [
		// 	'from' => Config::get('telnyx.from'),
		// 	'to' => $to,
		// 	'text' => $body,
		// ];

		// $curl = curl_init();

		// curl_setopt_array($curl, array(
		// 	CURLOPT_URL => "https://api.telnyx.com/v2/messages",
		// 	CURLOPT_CUSTOMREQUEST => "POST",
		// 	CURLOPT_POSTFIELDS => json_encode($data),
		// 	CURLOPT_HTTPHEADER => array(
		// 		"Authorization: Bearer " . Config::get('telnyx.secret_key'),
		// 		"Content-Type: application/json",
		// 	),
		// ));

		// $response = curl_exec($curl);
		// curl_close($curl);
		// \Log::info(json_decode($response));

	}
}