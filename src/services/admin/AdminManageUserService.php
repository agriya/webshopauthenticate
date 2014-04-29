<?php  namespace Agriya\Webshopauthenticate;
class AdminManageUserService
{

	public function chkValidUserId($user_id)
	{
		$user_count = User::where('id', $user_id)->count();
		if($user_count)
			return true;
		return false;
	}

	public function fetchUserDetailsById($user_id)
	{
		$user_details = User::where('users.id', $user_id)->first();
		return $user_details;
	}

	public function getSrchVal($key)
	{
		return (isset($this->srch_arr[$key])) ? $this->srch_arr[$key] : "";
	}


	public function setMemberFilterArr()
	{
		$this->filter_arr['user_name']= '';
		$this->filter_arr['user_email']= '';
		$this->filter_arr['status']= '';
	}

	public function setMemberSrchArr($input)
	{
		$this->srch_arr['user_name']= (isset($input['user_name']) && $input['user_name'] != '') ? $input['user_name'] : "";
		$this->srch_arr['user_email']= (isset($input['user_email']) && $input['user_email'] != '') ? $input['user_email'] : "";
		$this->srch_arr['status']= (isset($input['status']) && $input['status'] != '') ? $input['status'] : "";
	}

	public function buildMemberQuery()
	{
		$this->qry = User::Select("users.created_at", "users.first_name", "users.last_name", "users.email",
								"users.activated", "users.id" );
		$this->qry->Where('users.id', '<>', 0);

		//form the search query
		if($this->getSrchVal('user_code'))
		{
			$this->qry->whereRaw("( users.user_code = ?  OR users.user_id =  ? )", array($this->getSrchVal('user_code'), $this->getSrchVal('user_code')));
		}

		if($this->getSrchVal('user_name'))
		{
			$name_arr = explode(" ",$this->getSrchVal('user_name'));
			if(count($name_arr) > 0)
			{
				$or_str = '(';
				foreach($name_arr AS $names)
				{
					if($or_str != '(')
						$or_str = $or_str.' OR ';
					$or_str = $or_str.' (users.first_name LIKE \'%'.addslashes($names).'%\' OR users.last_name LIKE \'%'.addslashes($names).'%\' )';
				}
				$or_str = $or_str.' )';
				$this->qry->whereRaw(\DB::raw($or_str));
			}
		}
		if($this->getSrchVal('user_email'))
		{
			$this->qry->Where('users.email', $this->getSrchVal('user_email'));
		}

		if($this->getSrchVal('status'))
		{
			if($this->getSrchVal('status') == 'ToActivate')
			{
				$this->qry->Where('users.activated', 0);
			}
			else
				$this->qry->Where('users.user_status', $this->getSrchVal('status'));
		}

		$this->qry->orderBy('users.created_at', 'desc');
		return $this->qry;
	}

	public static function getUserAnalyticsInfo($user_id)
	{
		$analytics_info = array();
		$analytics_info = UserGeoAnalytics::where('user_id', $user_id)->first();
		if(count($analytics_info) > 0)
		{
			$campaign_url = '';
			$ga_content = trim($analytics_info['content']);
			if(isset($analytics_info['content']) && $analytics_info['content'] != '-')
			{
				//Condition to add http if not exist in ga_source
				if(!preg_match('/http/', $analytics_info['source']))
				{
					$campaign_url = 'http://'.$analytics_info['source'].$ga_content;
				}
				else
				{
					$campaign_url = $analytics_info['source'].$ga_content;
				}
			}
			$analytics_info['campaign_url'] = $campaign_url;
			// geo byte info
			$geobyte_info = json_decode($analytics_info['geobyte_info']);
			if(isset($geobyte_info))
			{
				$geobyte_info_list['region_name'] = isset($geobyte_info->region_name) ? $geobyte_info->region_name : "";
				$geobyte_info_list['city'] = isset($geobyte_info->city) ? $geobyte_info->city : "";
				$geobyte_info_list['certainty'] = isset($geobyte_info->certainty) ? $geobyte_info->certainty : "";

				$others_arr = array();
				foreach($geobyte_info as $geoKey => $geoValue)
				{
					if($geoKey!= "region_name" && $geoKey!= "city" && $geoKey!= "certainty")
					{
						$others_arr[] = ucwords(str_replace("_", " ", $geoKey)).": ".$geoValue;
					}
				}
				$geobyte_info_list['others'] = implode(", ", $others_arr);
				$analytics_info['geobyte_info_list'] = $geobyte_info_list;
			}

			// maxmind info
			$maxmind_info = json_decode($analytics_info['maxmind_info']);
			if(isset($maxmind_info))
			{
				$maxmind_info_list['region_name'] = isset($geobyte_info->region_name) ? $geobyte_info->region_name : "";
				$maxmind_info_list['city'] = isset($geobyte_info->city) ? $geobyte_info->city : "";

				$others_arr = array();
				foreach($maxmind_info as $maxmindKey => $maxmindValue)
				{
					if($maxmindKey!= "region_name" && $maxmindKey!= "city")
					{
						$others_arr[] = ucwords(str_replace("_", " ", $maxmindKey)).": ".$maxmindValue;
					}
				}
				$maxmind_info_list['others'] = implode(", ", $others_arr);
				$analytics_info['maxmind_info_list'] = $maxmind_info_list;
			}
			$brwoser_info = json_decode($analytics_info['browser_info']);

			if(isset($brwoser_info))
			{
				$others_arr = array();
				foreach($brwoser_info as $brwdKey => $brwValue)
				{
					$others_arr[] = ucwords(str_replace("_", " ", $brwdKey)).": ".$brwValue;

				}
				$analytics_info['browser_info_list'] = implode(", ", $others_arr);;
			}
		}
		return $analytics_info;
	}


	public function checkIsValidMember($user_id, $user_type='Member')
	{
		$memberCount = User::where('id', $user_id)->count();
		if($memberCount)
			return true;
		return false;
	}

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
			$success_msg = trans('admin/manageMembers.memberlist_activated_suc_msg');
		}
		else
		{
			$user = User::where("id", $user_id)->first();
			$data_arr['activated'] = 0;
			User::where('id', $user_id)->update($data_arr);
			$success_msg = trans('admin/manageMembers.memberlist_deactivated_suc_msg');
		}
		// Add user log entry
		$data_arr['user_id'] 	= $user_id;
		$data_arr['added_by'] 	= getAuthUser()->id;
		$data_arr['date_added'] = date('Y-m-d H:i:s');
		$data_arr['log_message'] = $success_msg." Added by: ".getAuthUser()->first_name." On.".date('Y-m-d H:i:s');
		$userlog = new UserLog();
		$userlog->addNew($data_arr);
		return $success_msg;
	}

	public function fetchUserDetails($ident, $type)
	{
		$user_details = array();
		$user_details['err_msg'] = '';
		$user_details['own_profile'] = 'No';

		$search_cond = "users.id = '".addslashes($ident)."'";
		if($type == 'code')
			$search_cond =" users.user_code = '".addslashes($ident)."'";

		$udetails = User::whereRaw($search_cond)
					->first(array('users.first_name', 'users.user_code', 'users.id', 'users.last_name', 'users.email', 'users.activated',
									'users.activated_at','users.last_login', 'users.about_me', 'users.user_status', 'users.user_access', 'users.phone'));

		if(count($udetails) > 0)
		{
			$user_details['user_code'] 		= $udetails['user_code'];
			$user_details['email'] 			= $udetails['email'];
			$user_details['user_id'] 		= $user_id = $udetails['id'];
			$user_details['first_name'] 	= $udetails['first_name'];
			$user_details['last_name'] 		= $udetails['last_name'];
			$user_display_name 				= $udetails['first_name'].' '.substr($udetails['last_name'], 0,1);
			$user_details['display_name'] 	= ucwords($user_display_name);
			$user_details['activated_at'] 	= $udetails['activated_at'];
			$user_details['last_login'] 	= $udetails['last_login'];
			$user_details['activated'] 		= $udetails['activated'];
			$user_details['phone'] 			= $udetails['phone'];
			$user_details['about_me'] 		= $udetails['about_me'];

			if($udetails['activated'] == 0)
				$user_details['user_status']= "ToActivate";
			elseif($udetails['user_status'] == "Deleted")
				$user_details['user_status']= "Locked";
			else
				$user_details['user_status']= $udetails['user_status'];
			$user_details['user_access']	= $udetails['user_access'];
			$admin_profile_url = CUtil::getUserDetails($user_id, 'admin_profile_url', $user_details);
			$user_details['profile_url'] = $admin_profile_url;
			$user_groups = $this->fetchUserGroupNames($user_details['user_id']);
			$user_details['user_groups'] = $user_groups;
		}
		else
		{
			$user_details['err_msg'] = 'No such user found';
			$user_details['profile_url'] = '';
		}
		return $user_details;
	}


	public function fetchUserGroupNames($user_id)
	{
		return  UserGroup::select("user_group.id", "user_group.group_name", 'user_group.has_admin_access')
									->join('user_group_members', 'user_group_members.group_id', '=', 'user_group.id')
									->where('user_group_members.user_id', $user_id)->get();
	}
}