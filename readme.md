## Webshop - Authentication Package
A Laravel 4 package for basic authentication

## Installation

Add the following to you composer.json file

    "agriya/webshopauthenticate": "dev-master"

Run

    composer update

Add the following to app/config/app.php

    'Agriya\Webshopauthenticate\WebshoppackServiceProvider',

Publish the config

    php artisan config:publish agriya/webshopauthenticate

Run the migration

    php artisan migrate --package="agriya/webshopauthenticate"

Add the following to app/routes.php

	Route::get(
		\Config::get('webshopauthenticate::uri').'/login',
		'Agriya\Webshopauthenticate\AuthController@index'
	);
	Route::post(
		\Config::get('webshopauthenticate::uri').'/login',
		'Agriya\Webshopauthenticate\AuthController@postLogin'
	);
	Route::get(
		\Config::get('webshopauthenticate::uri').'/logout',
		'Agriya\Webshopauthenticate\AuthController@getLogout'
	);
	Route::get(
		\Config::get('webshopauthenticate::uri').'/signup',
		'Agriya\Webshopauthenticate\AuthController@signUp'
	);
	Route::post(
		\Config::get('webshopauthenticate::uri').'/signup',
		'Agriya\Webshopauthenticate\AuthController@postSignup'
	);
	Route::get(
		\Config::get('webshopauthenticate::uri').'/forgotpassword',
		'Agriya\Webshopauthenticate\AuthController@forgotPassword'
	);
	Route::post(
		\Config::get('webshopauthenticate::uri').'/forgotpassword',
		'Agriya\Webshopauthenticate\AuthController@postForgotpassword'
	);
	Route::get(
		\Config::get('webshopauthenticate::uri').'/reset-password/{token}',
		'Agriya\Webshopauthenticate\AuthController@getResetPassword'
	);
	Route::get(
		\Config::get('webshopauthenticate::uri').'/change-password/{token}',
		'Agriya\Webshopauthenticate\AuthController@getChangePassword'
	);
	Route::post(
		\Config::get('webshopauthenticate::uri').'/change-password',
		'Agriya\Webshopauthenticate\AuthController@postChangePassword'
	);
	Route::get(\Config::get('webshopauthenticate::uri').'/activation/{activationCode}', 'Agriya\Webshopauthenticate\AuthController@getActivate');
	Route::get(\Config::get('webshopauthenticate::uri').'/myaccount', 'Agriya\Webshopauthenticate\AccountController@getIndex');
	Route::post(\Config::get('webshopauthenticate::uri').'/myaccount', 'Agriya\Webshopauthenticate\AccountController@postIndex');
	
