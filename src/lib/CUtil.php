<?php namespace Agriya\Webshopauthenticate;
//Common Utils
class CUtil
{
	//@added by Vasanthi_004at09
	public static $u_details = array();
	public static $u_image = array();

 	public static function populateConfigValues()
	{
		$data = DB::table('config_data')->get();
		foreach($data as $row)
		{
			$value = trim($row->config_value);
			$var   = trim($row->config_var);
			$file_name = '';
			if(trim($row->file_name) != '')
			{
				$file_name = $row->file_name.'.';
			}
			Config::set($file_name.$var, trim($value));
		}
	}
	//Added by periyasami_145at11
	public static function generateRandomUniqueCode($prefix_code, $table_name, $field_name)
	{
		if($table_name == 'users')
			$unique_code = $prefix_code.mt_rand(10000000,99999999);
		else
			$unique_code = $prefix_code.mt_rand(100000,999999);
		$code_count = 	\DB::table($table_name)->whereRaw($field_name." = ? ", array($unique_code))->count();
		if($code_count > 0)
		{
			return CUtil::generateRandomUniqueCode($prefix_code, $table_name, $field_name);
		}
		else
		{
			return $unique_code;
		}
		return $unique_code;
	}
	public static function getUserDetails($user_id, $out = 'all', $in_arr = array(), $cache = true)
	{
		$arr = array('display_name', 'profile_url', 'email', 'user_code', 'activated');
		$o_arr = array('display_name' => '',
						 'profile_url' => '',
						 'email' => '',
						 'user_code' => '');
		//all
		if($out == 'all')
		{
			$out = $ret = $arr;
		}
		else if(!is_array($out))
		{
			$ret = array($out);
		}
		else if(is_array($out))
		{
			$ret = $out;
		}
		//if cache is true
		//fetch display name
		if(in_array('display_name', $ret))
		{
			if(!isset(self::$u_details[$user_id]['display_name']))
			{
			//	$o_arr['display_name'] = self::$u_details[$user_id]['display_name'];
				if(!isset($in_arr['first_name']) OR !isset($in_arr['last_name']))
				{
					$in_arr = User::whereRaw('id = ? ', array($user_id))->first();
				}
				self::$u_details[$user_id]['display_name'] = ucfirst($in_arr['first_name']).' '.
															ucfirst(substr($in_arr['last_name'], 0, 1));
			}
			$o_arr['display_name'] = self::$u_details[$user_id]['display_name'];
		}

		//fetch profile url
		if(in_array('profile_url', $ret))
		{
			if(!isset(self::$u_details[$user_id]['profile_url']))
			{
			//	$o_arr['display_name'] = self::$u_details[$user_id]['display_name'];
				if(!isset($in_arr['first_name']) OR !isset($in_arr['user_code']))
				{
					$in_arr = User::whereRaw('id = ? ', array($user_id))->first();
				}
				self::$u_details[$user_id]['profile_url'] = url('/')."/".$in_arr['user_code']."-". strtolower(str_replace(" ","", $in_arr['first_name']));
			}
			$o_arr['profile_url'] = self::$u_details[$user_id]['profile_url'];
		}

		//fetch admin profile url
		if(in_array('admin_profile_url', $ret))
		{
			if(!isset(self::$u_details[$user_id]['admin_profile_url']))
			{
				if(!isset($in_arr['first_name']) OR !isset($in_arr['user_code']))
					$in_arr = User::whereRaw('id = ? ', array($user_id))->first();
				self::$u_details[$user_id]['admin_profile_url'] = url("admin/profile")."/".$in_arr['user_code']."-". strtolower(str_replace(" ","", $in_arr['first_name']));
			}
			$o_arr['admin_profile_url'] = self::$u_details[$user_id]['admin_profile_url'];
		}
		//fetch profile url
		if(in_array('email', $ret))
		{
			if(!isset(self::$u_details[$user_id]['email']))
			{
			//	$o_arr['display_name'] = self::$u_details[$user_id]['display_name'];
				if(!isset($in_arr['email']))
				{
					$in_arr = User::whereRaw('id = ? ', array($user_id))->first();
				}
				self::$u_details[$user_id]['email'] = $in_arr['email'];
			}
			$o_arr['email'] = self::$u_details[$user_id]['email'];
		}
		if(in_array('user_code', $ret))
		{
			if(!isset(self::$u_details[$user_id]['user_code']))
			{
			//	$o_arr['display_name'] = self::$u_details[$user_id]['display_name'];
				if(!isset($in_arr['user_code']))
				{
					$in_arr = User::whereRaw('id = ? ', array($user_id))->first();
				}
				self::$u_details[$user_id]['user_code'] = $in_arr['user_code'];
			}
			$o_arr['user_code'] = self::$u_details[$user_id]['user_code'];
		}
		if(in_array('activated', $ret))
		{
			if(!isset(self::$u_details[$user_id]['activated']))
			{
			//	$o_arr['display_name'] = self::$u_details[$user_id]['display_name'];
				if(!isset($in_arr['activated']))
				{
					$in_arr = User::whereRaw('id = ? ', array($user_id))->first();
				}
				self::$u_details[$user_id]['activated'] = $in_arr['activated'];
			}
			$o_arr['activated'] = self::$u_details[$user_id]['activated'];
		}
		if($out == '*')
		{
			return $o_arr;
		}
		else if(!is_array($out))
		{
			return isset($o_arr[$out]) ? $o_arr[$out] : '';
		}
		else if(is_array($out))
		{
			return $o_arr;
		}

	}


	/**
	 * CUtil::wordWrap()
	 * added by ravikumar_131at10
	 *
	 * @param mixed $text
	 * @param integer $textLimit
	 * @return
	 */
	public static function wordWrap($text, $textLimit = 100, $extra_char = '...')
	{	if(strlen($text) > $textLimit)
		{
			$return_str = preg_replace('/\s+?(\S+)?$/', '', substr($text, 0, $textLimit));
			return $return_str.$extra_char;
		}
		return $text;
	}

	/**
	 * CUtil::isMember()
	 * added by periyasami_145at11
	 *
	 * @return boolean
	 */
	public static function isMember()
	{
		if(Sentry::getUser() AND Sentry::getUser()->id)
		{
			return true;
		}
		return false;
	}


	/**
	 * CUtil::isStaff()
	 * added by periyasami_145at11
	 *
	 * @return boolean
	 */
	public static function isStaff()
	{
		return hasAdminAccess();
	}

	/**
	 * CUtil::isSuperAdmin()
	 * added by periyasami_145at11
	 * To check whether the logged user is admin
	 * @return boolean
	 */
	public static function isSuperAdmin()
	{
		return isSuperAdmin();
	}


	/**
	 * CUtil::FMTDate()
	 * Added by ravikumar_131at10
	 *
	 * @param mixed $value
	 * @param mixed $in
	 * @param mixed $out
	 * @return
	 */
	public static function FMTDate($value, $in, $out)
	{
		if(!$value OR $value == '0000-00-00' OR $value == '0000-00-00 00:00:00')
		{
			return '';
		}
		if($out == '')
		{
			$out = 'Y-m-d H:i:s';
		}
		if($out == 'ago')
		{
			return CUtil::timeElapsedString($value);
		}
		return DateTime::createFromFormat($in, $value)->format($out);
	}
	public static function populateAnalyticsHiddenFields()
	{
		foreach(Config::get('generalConfig.user_geo_analytics') as $analytics_val)
		{
			?>
			<input type="hidden" name="<?php echo $analytics_val; ?>" id="<?php echo $analytics_val; ?>">
	<?php
		}
	}

	/**
	 * Added by: ravikumar_131at10
	 *
	 * @return 		void
	 * @access 		public
	 */
	public static function getMemberbreadCramb()
	{
		$page_name = 'home';

		if(Request::is('*/signup')) {
			$page_name = 'signup';
		}

		if(Request::is('*/external-signup')) {
			$page_name = 'external-signup';
		}

		if(Request::is('*/login')) {
			$page_name = 'login';
		}

		if(Request::is('*/forgot-password')) {
			$page_name = 'forgot-password';
		}

		if(Request::is('*/change-password/*')) {
			$page_name = 'change-password';
		}

		if(Request::is('*/unsubscribe/*')) {
			$page_name = 'unsubscribe/update';
		}

		if(Request::is('*/about-us')) {
			$page_name = 'about-us';
		}

		if(Request::is('*/privacy')) {
			$page_name = 'privacy';
		}

		if(Request::is('*/terms')) {
			$page_name = 'terms';
		}

		if(Request::is('*/my-account')) {
			$page_name = 'my-account';
		}

		return $page_name;
	}

	/**
	 * CUtil::getAdminBreadCrumb()
	 * Added by: ravikumar_131at10
	 *
	 * @return
	 */
	public static function getAdminBreadCrumb()
	{
		$page_name = 'home';

		if(Request::is('*/memberlist'))
		{
			$page_name = 'member_list';
		}

		if(Request::is('*/members') || Request::is('*/members/*'))
		{
			$page_name = 'add_user';
			if(Request::is('*/edit/*'))
				$page_name = 'edit_user';
		}

		if(Request::is('*/config-manage'))
		{
			$page_name = 'config_manage';
		}

		if(Request::is('*/groups/*') || Request::is('*/groups'))
		{
			$page_name = 'list_groups';
			if(Request::is('*/list-members'))
				$page_name = 'list_group_members';

			else if(Request::is('*/list-group-access'))
				$page_name = 'list_group_access';
			else if(Request::is('*/list-member-access'))
				$page_name = 'list_member_access';
			else if(Request::is('*/edit-group-access'))
				$page_name = 'edit_group_access';
		}
		return $page_name;
	}

 	/**
 	 * CUtil::DISP_IMAGE()
 	 * Added by: ravikumar_131at10
 	 *
 	 * @param integer $cfg_width
 	 * @param integer $cfg_height
 	 * @param integer $img_width
 	 * @param integer $img_height
 	 * @param mixed $as_array
 	 * @return
 	 */
 	public static function DISP_IMAGE($cfg_width = 0, $cfg_height = 0, $img_width = 0, $img_height = 0, $as_array = false)
	{
		$img_attrib = array('width'=>'', 'height'=>'');

		if ($cfg_width > 0 AND $cfg_height > 0 AND ($cfg_width < $img_width) AND ($cfg_height < $img_height))
			{
				$tmpHeight = ( $cfg_width / $img_width ) * $img_height;

				if( $tmpHeight <= $cfg_height )
					{
						$attr = " width=\"".$cfg_width."\"";
						$img_attrib['width'] = $cfg_width;
					}
				else
					{
						$height = $tmpHeight - ( $tmpHeight - $cfg_height );
						$attr = " height=\"".$height."\"";
						$img_attrib['height'] = $height;
					}
			}
		else if ($cfg_width > 0 AND $cfg_width < $img_width)
			{
				$attr = " width=\"".$cfg_width."\"";
				$img_attrib['width'] = $cfg_width;
			}
		else if ($cfg_height > 0 AND $cfg_height < $img_height)
			{
				$attr = " height=\"".$cfg_height."\"";
				$img_attrib['height'] = $cfg_height;
			}
		else
			{
				$attr = "";
			}

		if ($as_array)
			{
				return $img_attrib;
			}

		return $attr;
	}

	/* Auth functions start */
	public static function getAuthUser()
	{
		return  \Sentry::getUser();
	}
	/* Auth functions end */
}
