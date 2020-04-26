<?php

if (!function_exists('send_sms')) {
	function send_sms($body, $to) {
		$data = [
			'from' => Config::get('constants.telnyx_from'),
			'to' => $to,
			'text' => $body,
		];

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.telnyx.com/v2/messages",
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . Config::get('constants.telnyx_secret_key'),
				"Content-Type: application/json",
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		ob_clean();
	}
}