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

	Route::get(\Config::get('webshopauthenticate::uri').'/activation/{activationCode}', 'Agriya\Webshopauthenticate\AuthController@getActivate');
	Route::group(array('before' => 'sentry.member'), function()
	{
		Route::get(\Config::get('webshopauthenticate::uri').'/myaccount', 'Agriya\Webshopauthenticate\AccountController@getIndex');
		Route::post(\Config::get('webshopauthenticate::uri').'/myaccount', 'Agriya\Webshopauthenticate\AccountController@postIndex');
	});
	Route::get(\Config::get('webshopauthenticate::uri').'/{user_code_seo_title}', 'Agriya\Webshopauthenticate\ProfileController@viewProfile')->where('user_code_seo_title', 'U[0-9]{6}'); //Call when parameter has user code format value
	Route::controller(\Config::get('webshopauthenticate::uri'), 'Agriya\Webshopauthenticate\AuthController');

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

Add the following links in member & admin layouts

	Signup - URL::to(Config::get('webshopauthenticate::uri').'/signup')
	Login - URL::to(Config::get('webshopauthenticate::uri').'/login')
	Forgot password - URL::to(\Config::get('webshopauthenticate::uri').'/forgotpassword')

	if (Sentry::check()) use below links
		Edit profile - URL::to(Config::get('webshopauthenticate::uri').'/myaccount')

	if (Sentry::check() && hasAdminAccess) use below links
		Manage Member - URL::to(Config::get('webshopauthenticate::admin_uri'))