<?php

use App\Models\Users;

return
	[
		'auth' => [

			/*
	|--------------------------------------------------------------------------
	| Authentication Username
	|--------------------------------------------------------------------------
	|
	} This option should be set to the "username" property of your users.
	| Typically, this will be set to "email" or "username".
	|
	| The value of this property will be used by the "attempt" closure when
	| searching for users by their username. It will also be used when the
	| user is set to be "remembered", as the username is embedded into the
	| encrypted cookie and is used to verify the user's identity.
	|
	*/

			'username' => 'email',
			'admin_panel_path' => envConfig('ADMIN_PANEL_PATH', 'admin'),

			'use_password_expiration' => true, //90 days
			'use_user_lock_after_failed_password_attemps' => true, //90 days
			// 'password_expiration_days' => '7776000', //90 days
			'password_expiration_days' => 86400 * envConfig('USER_PASSWORD_EXPIRE_TIME_IN_DAYS', 90), //90 days
			'failed_password_locked_minutes' => 5 * 60,
			'lock_account_after_failed_password_attemps' => 5, //90 days


		]
	];
