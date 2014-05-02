<?php
namespace Agriya\Webshopauthenticate;

class AuthController extends \BaseController
{
	/**
	 * AdminUserController::index()
	 *
	 * @return
	 */
	public function getLogin()
	{
		if (\Sentry::check()) {
           	return \Redirect::intended(\Config::get('webshopauthenticate::admin_uri'));
		}
		return \View::make('webshopauthenticate::users.login');
	}

	/**
	 * AdminUserController::postLogin()
	 *
	 * @return
	 */
	public function postLogin()
	{
		$userService = new UserAccountService();
		$rules = array(
				'email' => 'Required|email',
				'password' => 'Required',
		);
		$validator = \Validator::make(\Input::all(), $rules);
		if (!$validator->fails())
		{
			$user = array('email' => \Input::get('email'),
				          'password' => \Input::get('password')
			        	);
			$remember = \Input::get( 'remember');
	        $error = $userService->doLogin($user, $remember);

	        if ($error == '') {
        		$redirect = '';
	        	if (\Sentry::getUser()->hasAnyAccess(['system'])) {
	        		$redirect = \Config::get('webshopauthenticate::admin_uri');
				}
				else {
					$redirect = \Config::get('webshopauthenticate::uri').'/myaccount';
				}
           		return \Redirect::intended($redirect);
        	}

        	$error_msg = '';
        	if($error == 'Invalid'){
				$error_msg = \Lang::get('webshopauthenticate::auth/form.login.invalid_login');
			}
			else if($error == 'ToActivate') {
				$error_msg = \Lang::get('webshopauthenticate::auth/form.login.account_not_confirmed');
			}
        	\Session::flash('error', $error_msg);
	        return \Redirect::to(\Config::get('webshopauthenticate::uri').'/login')->withInput();
        }
        else
        {
        	return \Redirect::to(\Config::get('webshopauthenticate::uri').'/login')->withErrors($validator->messages())->withInput();
		}
	}

	/**
	 * AdminUserController::signUp()
	 *
	 * @return
	*/
	public function getSignup()
	{
		return \View::make(\Config::get('webshopauthenticate::signup'));
	}

	/**
	 * AdminUserController::postSignup()
	 *
	 * @return
	*/
	public function postSignup()
	{
		$userService = new UserAccountService();
		$rules = array(
    						'email' => 'required|between:10,40|email|unique:users',
    						'first_name' => $userService->getValidatorRule('first_name'),
    						'last_name' => $userService->getValidatorRule('last_name'),
    						'password' => $userService->getValidatorRule('password'),
    						'password_confirmation' =>'Required|same:password'
    					);
		$validator = \Validator::make(\Input::all(), $rules);
		if($validator->fails()) {
				return \Redirect::to(\Config::get('webshopauthenticate::uri').'/signup')
    								->with('errors', $validator->messages())
                                 	->withInput(\Input::except('password'));

	    } else {
	    	$user_id = $userService->addNewUser(\Input::all());
	    	if($user_id) {
				$group_exists = UsersGroups::whereRaw('user_id = ?', array($user_id))->count('user_id');
				if($group_exists == 0) {
					UsersGroups::insert(array('user_id' => $user_id, 'group_id' => 0));
				}
			}
	    	if(\Config::get('webshopauthenticate::user_auto_activate')) {
				return \Redirect::to(\Config::get('webshopauthenticate::uri').'/login')->with('success_message', 'account_created');
			}
			else {
				return \View::make('webshopauthenticate::users.signup')->with('success', 1)->with('email', \Input::get('email'));
			}
	  	}
	}

	/**
	 * AdminUserController::getActivate()
	 *
	 * @return
	*/
	public function getActivate($activationCode)
	{
		$userService = new UserAccountService();
		$user = $userService->getUserForActivationCode($activationCode);
		if($user AND $userService->activateUser($user, $activationCode))
		{
			return \Redirect::to(\Config::get('webshopauthenticate::uri').'/myaccount')->with('success', \Lang::get('webshopauthenticate::auth/form.login.activate_sucess'));
	    }
	    else
	    {
	       return \Redirect::to(\Config::get('webshopauthenticate::uri').'/login')->with('error', \Lang::get('webshopauthenticate::auth/form.login.invalid_activation_code'));
	    }
	}

	/**
	 * AdminUserController::forgotPassword()
	 *
	 * @return
	*/
	public function getForgotpassword()
	{
		return \View::make(\Config::get('webshopauthenticate::forgot_password'));
	}

	/**
	 * AdminUserController::postForgotpassword()
	 *
	 * @return
	*/
	public function postForgotpassword()
	{
		$rules = array('email' => 'required|email',	);
		$validator = \Validator::make(\Input::all(), $rules);
		if ($validator->fails())
		{
			return \Redirect::to(\Config::get('webshopauthenticate::uri').'/forgotpassword')->withInput()->withErrors($validator);
		}
		else
		{
			$user = \Sentry::getUserProvider()->findByLogin(\Input::get('email'));
			$token = $user->getResetPasswordCode();
			// Data to be used on the email view
			$data = array(
				'user'  => $user,
				'token' => $token,
			);

			PasswordReminders::insert(array('email' => \Input::get('email'), 'token' => $token, 'created_at' => \DB::raw('NOW()')));

			// Send the activation code through email
			\Mail::send('webshopauthenticate::emails.auth.reminder', $data, function($m) use ($user)
			{
				$m->to($user->email, $user->first_name . ' ' . $user->last_name);
				$m->subject(\Lang::get('webshopauthenticate::auth/form.forget_password.recovery_password_mail_sub'));
			});
			\Session::flash('success', \Lang::get('webshopauthenticate::auth/form.forget_password.password_mail_sent'));
			return \Redirect::to(\Config::get('webshopauthenticate::uri').'/forgotpassword');
		}
	}

	/**
	 * AdminUserController::getResetPassword()
	 *
	 * @return
	*/
	public function getResetPassword($token)
	{
		$userService = new UserAccountService();
		//check if valid token from the password_reminders table, if not show error message
		$is_valid = $userService->isValidPasswordToken($token);
		if($is_valid)
		{
			return \Redirect::to(\Config::get('webshopauthenticate::uri').'/change-password/'.$token);
		}
		else
		{
			return \Redirect::to(\Config::get('webshopauthenticate::uri').'/change-password/'.$token)->with('error', \Lang::get('webshopauthenticate::auth/form.change_password.invalid_token'));
		}
	}

	/**
	 * AdminUserController::getChangePassword()
	 *
	 * @return
	*/
	public function getChangePassword($token)
	{
		$userService = new UserAccountService();
		//check if valid token from the password_reminders table, if not show error message
		$is_valid = $userService->isValidPasswordToken($token);
		if($is_valid)
		{
			return \View::make(\Config::get('webshopauthenticate::change_password'))->with('token', $token);
		}
		else
		{
			return \View::make(\Config::get('webshopauthenticate::change_password'))->with('token', $token)->with('error', \Lang::get('webshopauthenticate::auth/form.change_password.invalid_token'));
		}
	}

	public function postChangePassword()
	{
		$userService = new UserAccountService();
		//check if valid token from the password_reminders table, if not show error message
		$rules = array('password' => $userService->getValidatorRule('password').'|Confirmed');
		$token = \Input::get('token');
		$v = \Validator::make(\Input::all(), $rules);
		if($v->passes())
		{
			$ret_msg = $userService->resetPassword(\Input::all());
			if($ret_msg == '')
			{
				return \Redirect::to(\Config::get('webshopauthenticate::uri').'/login')->with('success_message', \Lang::get('webshopauthenticate::auth/form.change_password.changepassword_success_message'));
			}
			else
			{
				return \Redirect::to(\Config::get('webshopauthenticate::uri').'/change-password/'.$token)->withInput()->with('change_password_error', $ret_msg);
			}
		}
		else
		{
			return \Redirect::to(\Config::get('webshopauthenticate::uri').'/change-password/'.$token)->withInput()->withErrors($v);
		}
	}

	/**
	 * AdminUserController::getLogout()
	 *
	 * @return
	*/
	public function getLogout()
    {
    	if (\Sentry::check()) {
	        \Sentry::logout();
	    }
        return \Redirect::to(\Config::get('webshopauthenticate::uri').'/login');
    }
}

?>