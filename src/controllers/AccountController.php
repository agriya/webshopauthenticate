<?php namespace Agriya\Webshopauthenticate;

use Cartalyst\Sentry\Users\UserNotFoundException as sentrycheck;

class AccountController extends \BaseController {

	/**
	 * To edit member profile form
	 * AccountController::index()
	 *
	 * @return
	 */
	public function getIndex()
	{
		$userService = new UserAccountService();
		$udetails = $d_arr = array();
		$logged_user_id = CUtil::getAuthUser()->id;
		$udetails = $userService->getUserinfo($logged_user_id);
		return \View::make('webshopauthenticate::myaccount.editProfile', compact('udetails', 'request_id', 'd_arr'));
	}

	/**
	 * To edit member profile action
	 * AccountController::postIndex()
	 *
	 * @return
	 */
	public function postIndex()
	{
		$this->userService = new UserAccountService();
		$success_message = "";
		$user = CUtil::getAuthUser();
		$logged_user_id = $user->id;
		$input = \Input::all();
		$input['user_id'] = $logged_user_id;
		$input['email'] = $user['email'];

		if(\Input::has('edit_basic'))
		{
			$rules = array();
			$messages = array();
			if(\Input::has('new_email') && \Input::get('new_email') != $user['email'])
			{
				$rules['new_email'] = $this->userService->getValidatorRule('email');
			}
			if(\Input::get('password') != "" || \Input::get('password_confirmation') != "" )
			{
				$rules['Oldpassword'] = 'Required';
			}
			if(\Input::has('Oldpassword') && \Input::has('password') && \Input::get('password') != "" && \Input::get('Oldpassword') != \Input::get('password'))
			{
				$rules['Oldpassword'] = $this->userService->getValidatorRule('Oldpassword');
				$messages['Oldpassword.is_valid_old_password'] = trans("myaccount/form.edit-profile.wrong_password");
				$rules['password'] = $this->userService->getValidatorRule('password');
				$rules['password_confirmation'] = 'Required|same:password';
			}

			$validator = \Validator::make(\Input::all(), $rules, $messages);
			if ($validator->fails())
			{
				return \Redirect::back()->withInput()->withErrors($validator);
			}
			else
			{
				$credential = array('email' => \Sentry::getUser()->email,
				         		'password' => \Input::get('Oldpassword')
			        			);
				try	{
					$user = \Sentry::findUserByCredentials($credential);
					$success_message = $this->userService->updateBasicDetails($input);
				}
				catch (sentrycheck $e) {
			    	return \Redirect::back()->withInput()->with('valid_user', \Lang::get('webshopauthenticate::myaccount/form.current_password') );
				}
			}
		}
		else if(\Input::has('edit_personal'))
		{
			$rules = array();
			$rules['first_name'] = $this->userService->getValidatorRule('first_name');
			$rules['last_name'] = $this->userService->getValidatorRule('last_name');
			$messages = array();

			$validator = \Validator::make(\Input::all(), $rules, $messages);
			if ($validator->fails())
			{
				return \Redirect::back()->withInput()->withErrors($validator);
			}
			$this->userService->updateUserPersonalDetails($input);
			$success_message = \Lang::get('webshopauthenticate::myaccount/form.edit-profile.personal_details_update_sucess');
		}
		return \Redirect::to(\Config::get('webshopauthenticate::uri').'/myaccount')->with('success_message', $success_message);
	}

	/**
	 * Email activation
	 * AccountController::emailActivation()
	 *
	 * @return
	 */
	public function emailActivation($activationCode)
	{
		$status = $this->userService->updateEmail($activationCode);
		$url = Url::action('ProfileController@emailActivationResponse', $status);
		return Redirect::to($url);
	}

	/**
	 * Email activation response
	 * AccountController::emailActivationResponse()
	 *
	 * @return
	 */
	public function emailActivationResponse($status)
	{
		$title = trans("myaccount/form.email-activation.new_email_activation");
		$this->header->setMetaTitle($title);
		$this->header->setMetaKeyword($title);
		$this->header->setMetaDescription($title);
		if($status == 'fail')
		{
			$error_msg = trans("myaccount/form.email-activation.alternateEmail_invalid_activation");
			return View::make('myaccount/alternateEmailActivation', array('error_msg' => $error_msg));
		}
		elseif($status == 'success')
		{
			$success_msg = trans("myaccount/form.email-activation.alternateEmail_newEmail_update_suc_msg");
			return View::make('myaccount/alternateEmailActivation', array('success_msg' => $success_msg));
		}
	}
}