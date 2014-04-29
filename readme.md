## Webshop - Authentication Package
A Laravel 4 package for basic authentication

## Installation

Add the following to you composer.json file

    "ahsan/webshopauthenticate": "dev-master"

Run

    composer update

Add the following to app/config/app.php

    'Ahsan\Webshoppack\WebshoppackServiceProvider',

Publish the config

    php artisan config:publish ahsan/webshopauthenticate

Run the migration

    php artisan migrate --package="ahsan/webshopauthenticate"

Add the following to app/routes.php

	Route::get(
		\Config::get('webshopauthenticate::uri').'/login',
		'Ahsan\Webshopauthenticate\AuthController@index'
	);
	Route::post(
		\Config::get('webshopauthenticate::uri').'/login',
		'Ahsan\Webshopauthenticate\AuthController@postLogin'
	);
	Route::get(
		\Config::get('webshopauthenticate::uri').'/logout',
		'Ahsan\Webshopauthenticate\AuthController@getLogout'
	);
	Route::get(
		\Config::get('webshopauthenticate::uri').'/signup',
		'Ahsan\Webshopauthenticate\AuthController@signUp'
	);
	Route::post(
		\Config::get('webshopauthenticate::uri').'/signup',
		'Ahsan\Webshopauthenticate\AuthController@postSignup'
	);
	Route::get(
		\Config::get('webshopauthenticate::uri').'/forgotpassword',
		'Ahsan\Webshopauthenticate\AuthController@forgotPassword'
	);
	Route::post(
		\Config::get('webshopauthenticate::uri').'/forgotpassword',
		'Ahsan\Webshopauthenticate\AuthController@postForgotpassword'
	);
	Route::get(
		\Config::get('webshopauthenticate::uri').'/reset-password/{token}',
		'Ahsan\Webshopauthenticate\AuthController@getResetPassword'
	);
	Route::get(
		\Config::get('webshopauthenticate::uri').'/change-password/{token}',
		'Ahsan\Webshopauthenticate\AuthController@getChangePassword'
	);
	Route::post(
		\Config::get('webshopauthenticate::uri').'/change-password',
		'Ahsan\Webshopauthenticate\AuthController@postChangePassword'
	);
	Route::get(\Config::get('webshopauthenticate::uri').'/activation/{activationCode}', 'Ahsan\Webshopauthenticate\AuthController@getActivate');
	Route::get(\Config::get('webshopauthenticate::uri').'/myaccount', 'Ahsan\Webshopauthenticate\AccountController@getIndex');
	Route::post(\Config::get('webshopauthenticate::uri').'/myaccount', 'Ahsan\Webshopauthenticate\AccountController@postIndex');
	
