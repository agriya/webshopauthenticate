<?php

use Cartalyst\Sentry\UserNotFoundException;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserAlreadyActivatedException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\WrongPasswordException;
use Cartalyst\Sentry\Users\UserNotActivatedException;
use Cartalyst\Sentry\Throttling\UserSuspendedException;
use Cartalyst\Sentry\Throttling\UserBannedException;

class EventHandler {

	public function handle($user)
  	{
	    $data['content'] = 'To register a listener using a class instead of a closure, you pass the name of the class as the second parameter, rather than closure:';
	    \Mail::send('webshopauthenticate::emails.commonEmail', $data, function($message)
		{
		    $message->to('foo@example.com', 'John Smith')->subject('Webshop: Listening to events using classes@handle!');
		});
  	}

	public function sendActivationCode($user)
	{
		//DB::table('users')->whereRaw('id = ?', array($user->user_id))->update(array('activation_code' => time()));
		$activation_code = $user->getActivationCode();
		$data = array('user'      	  => $user,
				  	  'activationUrl' => \URL::to(\Config::get('webshopauthenticate::uri').'/activation/'.$activation_code),
					);
		\Mail::send('webshopauthenticate::emails.auth.userActivation', $data, function($m) use ($user){
			$m->to($user->email, $user->first_name);
			$m->subject(\Lang::get('webshopauthenticate::email.userActivation'));
		});

	}

	public function sendUserWelcomeMail($user)
	{
		$mail_template = "webshopauthenticate::emails.auth.welcomeMailForUser";
		// Data to be used on the email view
		$data = array('user' => $user);

		\Mail::send($mail_template, $data, function($m) use ($user){
			$m->to($user->email, $user->first_name);
			$m->subject(\Lang::get('webshopauthenticate::email.welcomeMailForUser'));
		});
	}

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('auth.login', 'EventHandler');

        $events->listen('send.activation.code', 'EventHandler@sendActivationCode');

        $events->listen('send.welcome.mail', 'EventHandler@sendActivationCode');
    }
}
?>