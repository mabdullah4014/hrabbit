<?php

return [
	'driver' => env('FCM_PROTOCOL', 'http'),
	'log_enabled' => false,

	'http' => [
		'server_key' => env('FCM_SERVER_KEY', 'AAAA-_jvJTY:APA91bGM6Bb_cEyBeXSDYDr3p1yWaroZgmj1rzOnM2AxItVMznfN9rj1510iekA9KZtGh_wjJBXAi-ZKf0-HDjjY_-D9ee7ik3eL0YLyM_kA5mjxuuaxHkbgTSwjSsHdElw73CRE7Vuk'),
		'sender_id' => env('FCM_SENDER_ID', '1082213213494'),
		'legacy_server_key' => env('FCM_LSERVER_ID', 'AIzaSyAiQKuVEUPOrLMp9IpP7Wi0nzBOcJdVqZ4'),
		'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
		'server_group_url' => 'https://android.googleapis.com/gcm/notification',
		'timeout' => 30.0, // in second
	],
];
