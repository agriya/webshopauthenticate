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

	public function __construct()
	{
		$this->notifier = new EmailNotifierInterface();
		//$this->notifier = new LogNotifierInterface();
	}

	public function handle($user)
  	{
  		$data['mail_template'] = 'webshopauthenticate::emails.commonEmail';
  		$data['subject'] = 'Webshop: Listening to events using classes@handle!';
	    $data['content'] = 'To register a listener using a class instead of a closure, you pass the name of the class as the second parameter, rather than closure:';
//	    \Mail::send('webshopauthenticate::emails.commonEmail', $data, function($message)
//		{
//		    $message->to('foo@example.com', 'John Smith')->subject('Webshop: Listening to events using classes@handle!');
//		});
		$this->notifier->notify($user, $data);
  	}

	public function sendActivationCode($user)
	{
		//DB::table('users')->whereRaw('id = ?', array($user->user_id))->update(array('activation_code' => time()));
		$data['mail_template'] = 'webshopauthenticate::emails.auth.userActivation';
		$activation_code = $user->getActivationCode();
		$data['user'] = $user;
		$data['activationUrl'] = \URL::to(\Config::get('webshopauthenticate::uri').'/activation/'.$activation_code);
		$data['subject'] = \Lang::get('webshopauthenticate::email.userActivation');

//		\Mail::send('webshopauthenticate::emails.auth.userActivation', $data, function($m) use ($user){
//			$m->to($user->email, $user->first_name);
//			$m->subject(\Lang::get('webshopauthenticate::email.userActivation'));
//		});
		$this->notifier->notify($user, $data);
	}

	public function sendUserWelcomeMail($user)
	{
		$data['mail_template'] = "webshopauthenticate::emails.auth.welcomeMailForUser";
		// Data to be used on the email view
		$data['user'] = $user;
		$data['subject'] = \Lang::get('webshopauthenticate::email.welcomeMailForUser');

//		\Mail::send($mail_template, $data, function($m) use ($user){
//			$m->to($user->email, $user->first_name);
//			$m->subject(\Lang::get('webshopauthenticate::email.welcomeMailForUser'));
//		});
		$this->notifier->notify($user, $data);
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

        $events->listen('send.welcome.mail', 'EventHandler@sendUserWelcomeMail');
    }
}
?>