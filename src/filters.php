<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

/*
|--------------------------------------------------------------------------
| Event listener
|--------------------------------------------------------------------------
|
*/
$subscriber = new EventHandler;
Event::subscribe($subscriber);

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('sentry.admin', function()
{
	if (!Sentry::check()) return Redirect::to(Config::get('webshopauthenticate::uri').'/login');
	if (!Sentry::getUser()->hasAnyAccess(['system', 'system.Admin'])) return Redirect::to(Config::get('webshopauthenticate::uri').'/myaccount');
});

Route::filter('sentry.member', function()
{
	if (!Sentry::check()) return Redirect::to(Config::get('webshopauthenticate::uri').'/login');
});