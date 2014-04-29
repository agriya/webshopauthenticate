## Webshop - Authentication Package
A Laravel 4 package for basic authentication

## Installation

Add the following to you composer.json file

    "agriya/webshopauthenticate": "dev-master"

Run

    composer update

Add the following to app/config/app.php

    'Agriya\Webshopauthenticate\WebshopauthenticateServiceProvider',

Publish the config

    php artisan config:publish agriya/webshopauthenticate

Publish the asset

    php artisan asset:publish agriya/webshopauthenticate

Run the migration

    php artisan migrate --package="agriya/webshopauthenticate"

Run the db seed

    php artisan db:seed --class="Agriya\Webshopauthenticate\UsersGroupsTableSeeder"

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
	Route::group(array('before' => 'sentry.member'), function()
	{
		Route::get(\Config::get('webshopauthenticate::uri').'/myaccount', 'Agriya\Webshopauthenticate\AccountController@getIndex');
		Route::post(\Config::get('webshopauthenticate::uri').'/myaccount', 'Agriya\Webshopauthenticate\AccountController@postIndex');
	});
	Route::group(array('before' => 'sentry.admin'), function()
	{
		Route::get(Config::get('webshopauthenticate::admin_uri'), 'Agriya\Webshopauthenticate\AdminUserController@index');
		Route::get(Config::get('webshopauthenticate::admin_uri').'/users/add', 'Agriya\Webshopauthenticate\AdminUserController@getAddUsers');
		Route::post(Config::get('webshopauthenticate::admin_uri').'/users/add', 'Agriya\Webshopauthenticate\AdminUserController@postAddUsers');
		Route::get(Config::get('webshopauthenticate::admin_uri').'/users/edit/{user_id}', 'Agriya\Webshopauthenticate\AdminUserController@getEditUsers');
		Route::post(Config::get('webshopauthenticate::admin_uri').'/users/edit/{user_id}', 'Agriya\Webshopauthenticate\AdminUserController@postEditUsers');
		Route::any(Config::get('webshopauthenticate::admin_uri').'/users/changestatus', 'Agriya\Webshopauthenticate\AdminUserController@getChangeUserStatus');
	});
	
##