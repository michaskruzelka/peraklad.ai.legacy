<?php

return [

	/**
	 * Application Name
	 *
	 * Name of your project in `https://console.developers.google.com/`.
	 */
	'applicationName' => 'Belarusian Subtitles',

	/**
	 * P12 File
	 *
	 * After creating a project, go to `APIs & auth` and choose `Credentials` section.
	 * 
	 * Click `Create new Client ID` and select `Service Account` choose `P12` as the `Key Type`.
	 *
	 * After downloading the `p12` file copy and paste it in the `storage` directory.
	 * 		Example:
	 * 			storage/MyProject-2a4d6aaa4413.p12
	 * 
	 */
	'p12FilePath' => storage_path('Belarusian_Subtitles-4360246cfd23.p12'),

	/**
	 * You will find this information under `Service Account` > `Client ID`
	 *
	 * 		Example:
	 * 			122654635465-u7io2injkjniweklew48knh7158.apps.googleusercontent.com
	 */
	'serviceClientId' => '4360246cfd235a6af9559c71cef95cb9bcb7e95b',
	
	/**
	 * You will find this information under `Service Account` > `Email Address`
	 *
	 * 		Example:
	 * 			122654635465-u7io2injkjniweklew48knh7158@developer.gserviceaccount.com
	 */
	'serviceAccountName' => 'belsub@belarusian-subtitles.iam.gserviceaccount.com',
	
	/**
	 * Here you should pass an array of needed scopes depending on what service you will be using.
	 *
	 * 		Example:
	 * 			For analytics service:
	 * 			
	 * 				'scopes' => [
	 *					'https://www.googleapis.com/auth/analytics.readonly',
	 *				],
	 */
	'scopes' => [
		'https://www.googleapis.com/auth/analytics.readonly'
	],

];