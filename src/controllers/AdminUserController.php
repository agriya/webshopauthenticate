<?php namespace Agriya\Webshopauthenticate;

class AdminUserController extends \BaseController {

	/**
	 * To list members
	 * AdminUserController::index()
	 *
	 * @return
	 */
	public function index()
	{
		$d_arr = array();
		$d_arr['pageTitle'] = "Members List";
		$user_list = $user_details = array();

		$this->manageUserService = new AdminManageUserService();
		$this->manageUserService->setMemberFilterArr();
		$this->manageUserService->setMemberSrchArr(\Input::All());

		$q = $this->manageUserService->buildMemberQuery();

		$page 		= (\Input::has('page')) ? \Input::get('page') : 1;
		$start 		= (\Input::has('start')) ? \Input::get('start') : \Config::get('webshopauthenticate::list_paginate');
		$perPage	= \Config::get('webshopauthenticate::list_paginate');
		$user_list 	= $q->paginate($perPage);
		foreach($user_list AS $userKey => $user)
		{
			$user_details[$userKey] = $user;
		}
		return \View::make('webshopauthenticate::admin.index', compact('d_arr', 'user_list', 'user_details'));
	}

	/**
	 * AdminUserController::getAddUsers()
	 *
	 * @return
	 */
	public function getAddUsers()
	{
		$d_arr = array();
		$d_arr['pageTitle'] = \Lang::get('webshopauthenticate::admin/addMember.addmember_page_title');
		$d_arr['mode'] = 'add';
		$d_arr['user_id'] = 0;
		$user_details = array();
		return \View::make('webshopauthenticate::admin.addMember', compact('d_arr', 'user_details'));
	}

	/**
	 * AdminAddUserController::postAddUsers()
	 *
	 * @return
	 */
	public function postAddUsers()
	{
		$messages = array();
		$this->userAccountService = new UserAccountService();
		$rules = array('first_name' => $this->userAccountService->getValidatorRule('first_name'),
						'last_name' => $this->userAccountService->getValidatorRule('last_name'),
						'email' => $this->userAccountService->getValidatorRule('email'),
						'password' => $this->userAccountService->getValidatorRule('password')
					  );
		$validator = \Validator::make(\Input::all(), $rules, $messages);
		if ($validator->passes())
		{
			$input = \Input::all();
			$user_id = $this->userAccountService->addNewUser($input, false, true);
			if($user_id)
			{
				$group_exists = UsersGroups::whereRaw('user_id = ?', array($user_id))->count('user_id');
				if($group_exists == 0) {
					UsersGroups::insert(array('user_id' => $user_id, 'group_id' => 0));
				}
				\Session::flash('success', \Lang::get('webshopauthenticate::admin/addMember.member_add_success'));
				return \Redirect::to(\Config::get('webshopauthenticate::admin_uri'));
			}
		}
		else
		{
			return \Redirect::to(\Config::get('webshopauthenticate::admin_uri').'/users/add')->withInput()->withErrors($validator);
		}
	}

	/**
	 * AdminAddUserController::getEditUsers()
	 *
	 * @param mixed $user_id
	 * @return
	 */
	public function getEditUsers($user_id='')
	{
		if($user_id)
		{
			$this->adminmanageuserservice = new AdminManageUserService();
			$is_valid_user = $this->adminmanageuserservice->chkValidUserId($user_id);
			if($is_valid_user)
			{
				$d_arr = array();
				$d_arr['pageTitle'] = \Lang::get('webshopauthenticate::admin/addMember.editmember_page_title');
				$d_arr['mode'] = 'edit';
				$d_arr['user_id'] = $user_id;
				$user_details = $this->adminmanageuserservice->fetchUserDetailsById($user_id);
				return \View::make('webshopauthenticate::admin.addMember', compact('d_arr', 'user_details'));
			}
			else
			{
				$user_details = array();
				$d_arr['mode'] = 'edit';
				$d_arr['pageTitle'] = \Lang::get('webshopauthenticate::admin/addMember.editmember_page_title');
				$d_arr['error_msg'] = \Lang::get('webshopauthenticate::admin/addMember.invalid_user_id');
				return \View::make('webshopauthenticate::admin.addMember', compact('d_arr', 'user_details'));
			}
		}
		else
		{
			return \Redirect::to('admin/users/add');
		}
	}

	/**
	 * AdminAddUserController::postEdit()
	 *
	 * @return
	 */
	public function postEditUsers()
	{
		$mode = \Input::get('mode');
		$user_id = \Input::get('user_id');
		$messages = array();
		if($mode == 'edit')
		{
			$this->userAccountService = new UserAccountService();
			$this->adminmanageuserservice = new AdminManageUserService();

			$is_valid_user = $this->adminmanageuserservice->chkValidUserId($user_id);
			if($is_valid_user)
			{
				$user_input = \Input::all();
				$rules = array('first_name' => $this->userAccountService->getValidatorRule('first_name'),
							'last_name' => $this->userAccountService->getValidatorRule('last_name'),
							'email' => 'Required|Email|unique:users,email,'.$user_id.',id'
						  );
				if(\Input::get('password') != '' || \Input::get('password_confirmation') != '')
				{
					$rules['password'] = $this->userAccountService->getValidatorRule('password');
					$rules['password_confirmation'] = 'Required';
				}
				$validator = \Validator::make($user_input, $rules, $messages);
				if ($validator->passes())
				{
					$is_user_updated = $this->userAccountService->updateUserDetails(\Input::all());
					if($is_user_updated)
					{
						$success_msg = \Lang::get('webshopauthenticate::admin/addMember.member_update_success');
						return \Redirect::to(\Config::get('webshopauthenticate::admin_uri').'/users/edit/'.$user_id)->with('success_message', $success_msg);
					}
				}
				else
				{
					return \Redirect::to(\Config::get('webshopauthenticate::admin_uri').'/users/edit/'.$user_id)->withInput()->withErrors($validator);
				}
			}
			else
			{
				$error_msg = \Lang::get('webshopauthenticate::admin/addMember.invalid_user_id');
				return \Redirect::to(\Config::get('webshopauthenticate::admin_uri').'/users/edit/'.$user_id)->with('error_message', $error_msg);
			}
		}
	}

	/**
	 * AdminUserController::getChangeUserStatus()
	 *
	 * @return
	 */
	public function getChangeUserStatus()
	{
		if(\Input::has('user_id') && \Input::has('action'))
		{
			$user_id = \Input::get('user_id');
			$action = \Input::get('action');
			$success_msg = "";
			//echo "Yes this was called", $user_id," action ", $action;
			$success_msg = $this->updateUserActivationByAdmin($user_id, $action);
		}
		\Session::flash('success', $success_msg);
		return \Redirect::to(\Config::get('webshopauthenticate::admin_uri'));
	}

	/**
	 * AdminUserController::updateUserActivationByAdmin()
	 *
	 * @return success_msg
	 */
	public function updateUserActivationByAdmin($user_id, $action)
	{
		if(strtolower($action) == 'activate')
		{
			$user = User::where("id", $user_id)->where('activated', 0)->first();
			if($user)
			{
				$activation_code = $user->getActivationCode();
				$userService = new UserAccountService();
				$userService->activateUser($user, $activation_code, $auto_login = false);
			}
			$success_msg = \Lang::get('webshopauthenticate::admin/manageMembers.memberlist_activated_suc_msg');
		}
		else
		{
			$user = User::where("id", $user_id)->first();
			$data_arr['activated'] = 0;
			User::where('id', $user_id)->update($data_arr);
			$success_msg = \Lang::get('webshopauthenticate::admin/manageMembers.memberlist_deactivated_suc_msg');
		}
		return $success_msg;
	}
}