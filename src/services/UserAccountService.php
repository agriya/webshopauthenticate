<?php namespace Agriya\Webshopauthenticate;

use Cartalyst\Sentry\UserNotFoundException;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserAlreadyActivatedException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\WrongPasswordException;
use Cartalyst\Sentry\Users\UserNotActivatedException;
use Cartalyst\Sentry\Throttling\UserSuspendedException;
use Cartalyst\Sentry\Throttling\UserBannedException;

class UserAccountService
{
	public function doLogin($user, $remember, $data=array())
	{
		$error = '';
		try
		{
			\Sentry::authenticate($user, $remember);
		}
		catch (\Exception $e)
		{
			if($e instanceOf \Cartalyst\Sentry\Users\UserNotFoundException)
				$error =  'Invalid';
			else if($e instanceOf \Cartalyst\Sentry\Users\UserNotActivatedException)
				$error =  'ToActivate';
			else
				$error = $e->getMessage();
		}
		return $error;
	}

	//@added by Vasanthi_004at09
	public function getValidatorRule($field)
	{
		$rules = array(
				'first_name' => 'required|Min:'.\Config::get('webshopauthenticate::fieldlength_name_min_length').
									'|Max:'.\Config::get('webshopauthenticate::fieldlength_name_max_length').
									'|regex:'."/^[a-zA-Z0-9',\/&() -]*$/",

				'last_name' => 'required'.
									'|Min:'.\Config::get('webshopauthenticate::fieldlength_name_min_length').
									'|Max:'.\Config::get('webshopauthenticate::fieldlength_name_max_length').
									'|regex:'."/^[a-zA-Z0-9',\/&() -]*$/",

				'email' => 'Required|Email|unique:users,email',
				'password' =>'Required|Min:'.\Config::get('webshopauthenticate::fieldlength_password_min').
							'|Max:'.\Config::get('webshopauthenticate::fieldlength_password_max').'|confirmed',
				'hash'		  =>  'required|Min:'.\Config::get('webshopauthenticate::fieldlength_hash_min').
									'|Max:'.\Config::get('webshopauthenticate::fieldlength_hash_max').'|regex:'."/^[a-zA-Z0-9',\/&() -]*$/",
		);
		return isset($rules[$field])? $rules[$field] : 'Required';
	}

	public function updateUserDetails($input)
	{
		$update_user_details = array('first_name' => $input['first_name'], 'last_name' => $input['last_name'], 'email' => $input['email'] );
		if(isset($input['password']) && $input['password'] != '')
		{
			/*$bba_token = str_random(8);
			$password = md5($input['password']. $bba_token);
			$update_user_details['bba_token'] = $bba_token;*/
			$update_user_details['password'] = $input['password'];
		}
		User::where('id', $input['user_id'])->update($update_user_details);
		return true;
	}

	public function addNewUser($input, $notify_user_create = false, $admin_user_create = false)
	{
		//$bba_token = str_random(8);
		//$password = md5($input['password']. $bba_token);
		$password = $input['password'];
		$activated = 0;
		$api_key  = str_random(16);
		$user = \Sentry::register(array(
				'first_name' => $input['first_name'],
				'last_name'  => $input['last_name'],
				'email'      => $input['email'],
				'password'   => $password,
				'activated'	  => $activated
			));
		if(!$admin_user_create)
		{
			//Update the user analytics info
			if($user->id)
			{
				$data_arr = $input;
				$data_arr['user_id'] = $user->id;
				//$this->addUserAnalyticsInfo($data_arr);
			}
			$this->sendActivationCode($user);
		}
		else
		{
			$this->sendUserCreatedMail($input['first_name'], $input['email'], $input['password']);
			$this->sendActivationCode($user, $admin_user_create);
		}
		return $user->id;
	}

	public function updateApiKeyForUser($user_id)
	{
		$api_key_details = array();
		$api_key_count = UserApiKey::where('user_id', $user_id)->count();
		$api_key = str_random(16);
		$hash = str_random(8);
		if(!$api_key_count)
		{
			$api_key_details['user_id'] = $user_id;
			$api_key_details['api_key'] = $api_key;
			$api_key_details['hash'] = $hash;
			$api_key_details['date_added'] = date('Y-m-d H:i:s');
			$user_api_key = new UserApiKey;
			$user_api_key->addNew($api_key_details);
		}
	}

	public function addUserAnalyticsInfo($data_arr)
	{
		$data_arr['date_added'] = date('Y-m-d H:i:s');
		/* Region and countries entry starts */
		$maxmind_info = html_entity_decode($data_arr['maxmind_info']);
		$maxmind_arr = json_decode($maxmind_info,true);
		if(isset($maxmind_arr['region_name']) && $maxmind_arr['region_name']!='')
			$data_arr['region'] = $maxmind_arr['region_name'];
		if(isset($maxmind_arr['mx_countryName']) && $maxmind_arr['mx_countryName']!='')
			$data_arr['country'] = $maxmind_arr['mx_countryName'];
		if($data_arr['region'] == '' && $data_arr['country'] == '')
		{
			$geobyte_info = html_entity_decode($data_arr['geobyte_info']);
			$geocode_arr = json_decode($geobyte_info, true);
			if(isset($geocode_arr['region_name']) && isset($geocode_arr['gn_countryName']))
			{
				if($geocode_arr['region_name']!='')
					$data_arr['region'] = $geocode_arr['region_name'];
				if($geocode_arr['gn_countryName']!='')
					$data_arr['country'] = $geocode_arr['gn_countryName'];
			}
		}
		$user_geo_analytics = new UserGeoAnalytics();
		$user_geo_analytics->addNew($data_arr);
	}

	public function sendUserCreatedMail($first_name, $email, $password)
	{
		$data = array(
			'email'	=> $email,
			'password'	=> $password,
			'first_name' => $first_name
		);
		\Mail::send('webshopauthenticate::emails.auth.userCreated', $data, function($m) use ($data) {
				$m->to($data['email'], $data['first_name']);
				$subject = 'Welcome to '.\Config::get('webshopauthenticate::package_name');
				$m->subject($subject);
			});
	}

	public function sendActivationCode($user, $admin_user_create = false)
	{
		//If auto activate false
		if(!\Config::get('webshopauthenticate::user_auto_activate') && !$admin_user_create)
		{
			\Event::fire('send.activation.code', array($user));
//			$data = array('user'          => $user,
//					  'activationUrl' => \URL::to(\Config::get('webshopauthenticate::uri').'/activation/'.$activation_code),
//					);
//			\Mail::send('webshopauthenticate::emails.auth.userActivation', $data, function($m) use ($user){
//				$m->to($user->email, $user->first_name);
//				$subject = \Lang::get('webshopauthenticate::email.userActivation');
//				$m->subject($subject);
//			});
		}
		else
		{
			$activation_code = $user->getActivationCode();
			if($admin_user_create)
				$this->activateUser($user, $activation_code, false, $admin_user_create);
			else
				$this->activateUser($user, $activation_code);
		}

	}
	public function resendActivationCode($email)
	{
		$user = User::where('email', $email)->first();
		if(isset($user['user_id']))
		{
			$this->sendActivationCode($user);
			return 'success';
		}
		return 'failed';

	}

	public function getUserForActivationCode($code)
	{
		try
		{
			$user = \Sentry::getUserProvider()->findByActivationCode($code);
			return $user;
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			return false;
		}

	}

	public function activateUser($user, $activationCode, $auto_login = true, $admin_user_create = false)
	{
		try
		{
			$user->attemptActivation($activationCode);
			\Event::fire('send.welcome.mail', array($user));
			//$this->sendUserWelcomeMail($user, $admin_user_create);
			if($auto_login)
				$resp = \Sentry::login($user, '');	// login once activated account
			return true;
		}
		catch(UserAlreadyActivatedException $e)
		{
			return false;
		}
	}

	/*
		Added periyasami_145at11
		To send welcome mail to user.
	*/
	public function sendUserWelcomeMail($user)
	{
		$mail_template = "webshopauthenticate::emails.auth.welcomeMailForUser";
		// Data to be used on the email view
		$data = array('user'          => $user
					 );

		\Mail::send($mail_template, $data, function($m) use ($user){
			$m->to($user->email, $user->first_name);
			$m->subject(\Lang::get('webshopauthenticate::email.welcomeMailForUser'));
		});

	}

	public function isValidPasswordToken($token)
	{
		return \DB::table('password_reminders')->whereRaw('token = ?', array($token))->count();
	}

	public function resetPassword($input)
	{
		//from the token get the user email and reset the password for the user id with the email
		$email = \DB::table('password_reminders')->whereRaw('token = ?', array($input['token']))->pluck('email');
		if($email != '')
		{
			//generate new bba token and generate password and update the user table with email
			/*$data_arr['bba_token'] 		= $this->generateRandomCode();
			$data_arr['password'] 		= md5($input['password'].$data_arr['bba_token']);*/
			$data_arr['password'] = $input['password'];

			// Find the user using the user id
			//$user = Sentry::getUser();

			$user = User::where('email', $email)->first();

			$logged_user_id = $user->id;
    		$user = \Sentry::getUserProvider()->findById($logged_user_id);
    		// Update the user details
    		/*$user->bba_token = $data_arr['bba_token'];
    		$user->password = md5($input['password'].$data_arr['bba_token']);*/
    		$user->password = $input['password'];

    // Update the user
    if ($user->save())
			\DB::table('password_reminders')->whereRaw('token = ?', array($input['token']))->delete();
			return '';
		}
		else
		{
			return trans('auth/form.change_password.invalid_token');
		}
	}

	public function generateRandomCode($size = 8)
	{
		$text = microtime();
		$start = rand(0, 24);
		return substr(md5($text), $start, $size);
	}

	public function getUserinfo($user_id = 0)
	{
		$udetails = $user_destination = $company_info = array();
		$udetails = User::where('id', $user_id)->first();
		//$user_image = UserImage::where('user_id', $user_id)->first();
		//$udetails['user_image'] = $user_image;
		return $udetails;
	}

	public function updateUserPersonalDetails($input)
	{
		$data_arr['first_name'] = $input['first_name'];
		$data_arr['last_name'] = $input['last_name'];
		User::where('id', $input['user_id'])->update($data_arr);
		//$this->userImageUpload($input['user_id']);
	}


	public static function chkIsBannedIP($ip)
	{
		return DB::table('user_banned_ip')->whereRaw('banned_ip = ?', array($ip))->count();
	}



	public function chkAndCreateFolder($folderName)
	{
		$folder_arr = explode('/', $folderName);
		$folderName = '';
		foreach($folder_arr as $key=>$value)
			{
				$folderName .= $value.'/';
				if($value == '..' or $value == '.')
					continue;
				if (!is_dir($folderName))
					{
						mkdir($folderName);
						@chmod($folderName, 0777);
					}
			}
	}


	public function updateBasicDetails($input)
	{
		$success_message = "";
		if(isset($input['new_email']) && isset($input['email'])&&  $input['new_email'] != "" &&  $input['new_email'] != $input['email'])
		{
			// update email
			$this->changeEmail($input);
			$success_message .= \Lang::get('webshopauthenticate::myaccount/form.edit-profile.alternateEmail_newEmail_activation_msg');
		}
		if(isset($input['Oldpassword']) && isset($input['password']) && $input['password'] != "" && $input['Oldpassword'] != $input['password'])
		{
			//generate new bba token and generate password and update the user table with email
			/*$data_arr['bba_token'] 		= $this->generateRandomCode();
			$data_arr['password'] 		= md5($input['password'].$data_arr['bba_token']);*/
			$data_arr['password'] = $input['password'];

    		$user = \Sentry::getUserProvider()->findById($input['user_id']);

    		// Update the user details
    		/*$user->bba_token = $data_arr['bba_token'];
    		$user->password = md5($input['password'].$data_arr['bba_token']);*/
    		$user->password = $input['password'];

    		// Update the user
    		$user->save();
    		$success_message .= \Lang::get('webshopauthenticate::auth/form.changepassword_success_message');
		}
		return $success_message;
	}


	public function changeEmail($input)
	{
		$user = User::where('id', $input['user_id'])->first();
		if (count($user))
		{
			$temp_userinfo = $user;
			$user_id = $input['user_id'];
			$activation_code = $user->getActivationCode();

			$user_data['new_email'] = $input['new_email'];
			$user_data['activation_code'] = $activation_code;

			User::where('id', $user_id)->update($user_data);

			$data = array(
				'user'          => $user,
				'email'	=> $input['new_email'],
				'first_name' => $user->first_name,
				'activationUrl' => \URL::to(\Config::get('webshopauthenticate::uri').'/newemailactivate/'.$activation_code),
			);

			\Mail::send('webshopauthenticate::emails.auth.newEmailActivation', $data, function($m) use ($data) {
				$m->to($data['email'], $data['first_name']);
				$subject = trans('email.newEmailActivation');
				$m->subject($subject);
			});
		}
	}

	public function userImageUpload($user_id)
	{
		$file = Input::file('user_image');
		if($file != '')
		{
			$image_ext = $file->getClientOriginalExtension();
			$image_name = Str::random(20);
			$destinationpath = URL::asset(Config::get("generalConfig.user_image_folder"));

			$image_id = $this->uploadUserImage($file, $image_ext, $image_name, $destinationpath, $user_id);
		}
	}


	public function uploadUserImage($file, $image_ext, $image_name, $destinationpath, $user_id= 0)
	{
		$config_path = Config::get('generalConfig.user_image_folder');
		$this->chkAndCreateFolder($config_path);
		$logged_user_id = $user_id;
		if(!$logged_user_id && isset(getAuthUser()->id))
		{
			$logged_user_id = getAuthUser()->id;
		}

		// open file a image resource
		Image::make($file->getRealPath())->save(Config::get("generalConfig.user_image_folder").$image_name.'_O.'.$image_ext);

		list($width,$height)= getimagesize($file);
		list($upload_img['width'], $upload_img['height']) = getimagesize(base_path().'/public/'.$config_path.$image_name.'_O.'.$image_ext);

		$large_width = Config::get('generalConfig.user_image_large_width');
		$large_height = Config::get('generalConfig.user_image_large_height');
		if(isset($large_width) && isset($large_height))
		{
			$img_size = CUtil::DISP_IMAGE($large_width, $large_height, $upload_img['width'], $upload_img['height'], true);

			Image::make($file->getRealPath())
				->resize($large_width, $large_height, true, false)
				->save($config_path.$image_name.'_L.'.$image_ext);
		}
		$small_width = Config::get("generalConfig.user_image_small_width");
		$small_height = Config::get("generalConfig.user_image_small_height");
		if(isset($small_width) && isset($small_height))
		{
			$simg_size = CUtil::DISP_IMAGE($small_width, $small_height, $upload_img['width'], $upload_img['height'], true);
			Image::make($file->getRealPath())
				->resize($small_width, $small_height, true, false)
				->save($config_path.$image_name.'_S.'.$image_ext);
		}
		$thumb_width = Config::get("generalConfig.user_image_thumb_width");
		$thumb_height = Config::get("generalConfig.user_image_thumb_height");
		if(isset($thumb_width) && isset($thumb_height))
		{
			$timg_size = CUtil::DISP_IMAGE($thumb_width, $thumb_height, $upload_img['width'], $upload_img['height'], true);
			Image::make($file->getRealPath())
				->resize($thumb_width, $thumb_height, true, false)
				->save($config_path.$image_name.'_T.'.$image_ext);
		}

		$img_path = Request::root().'/'.$config_path;
		list($upload_input['small_width'], $upload_input['small_height']) = getimagesize($img_path.$image_name.'_S.'.$image_ext);
		list($upload_input['thumb_width'], $upload_input['thumb_height']) = getimagesize($img_path.$image_name.'_T.'.$image_ext);
		list($upload_input['large_width'], $upload_input['large_height']) = getimagesize($img_path.$image_name.'_L.'.$image_ext);

		$user_image = new UserImage();

		$user_data = array(	'image_ext' => $image_ext,
							'image_name' => $image_name,
							'image_server_url' => $destinationpath,
							'large_height' => $upload_input['large_height'],
                            'large_width' => $upload_input['large_width'],
							'small_width' => $upload_input['small_width'],
                            'small_height' => $upload_input['small_height'],
							'thumb_width' => $upload_input['thumb_width'],
                            'thumb_height' => $upload_input['thumb_height']);

		$user_image_details = UserImage::where('user_id',$logged_user_id)->first();
		if(count($user_image_details) > 0)
		{
			$this->deleteImageFiles($user_image_details->image_name, $user_image_details->image_ext, $config_path);
			UserImage::where('user_id', $logged_user_id)->update($user_data);
			$id = $user_image_details->image_id;
		}
		else
		{
			$user_data['date_added'] = new DateTime;
			$user_data['user_id'] = $logged_user_id;
			$id = $user_image->insertGetId($user_data);
		}
		return $id;
	}

	public function deleteImageFiles($filename, $ext, $folder_name)
	{
		if (file_exists($folder_name.$filename."_L.".$ext))
			unlink($folder_name.$filename."_L.".$ext);
		if (file_exists($folder_name.$filename."_L1.".$ext))
			unlink($folder_name.$filename."_L1.".$ext);
		if (file_exists($folder_name.$filename."_M.".$ext))
			unlink($folder_name.$filename."_M.".$ext);
		if (file_exists($folder_name.$filename."_T.".$ext))
			unlink($folder_name.$filename."_T.".$ext);
		if (file_exists($folder_name.$filename."_S.".$ext))
			unlink($folder_name.$filename."_S.".$ext);
		if (file_exists($folder_name.$filename."_O.".$ext))
			unlink($folder_name.$filename."_O.".$ext);
	}

	public function updateEmail($activation_code)
	{
		$status = 'fail';
		$user = User::where('activation_code', $activation_code)->where('new_email', '<>', '')->first();
		if(count($user) > 0)
		{
			$user_id = $user['id'];
			$temp_email = $user['new_email'];

			$CheckUser = User::where('email', $temp_email)->where('id', '<>', $user_id)->count();
			if($CheckUser > 0)
			{
				$status = 'fail';
			}
			else
			{
				$data_arr['email'] = $temp_email;
				$data_arr['new_email'] = '';
				$data_arr['activation_code'] = '';
				User::where('id', $user_id)->update($data_arr);
				$status = 'success';
			}
		}
		return $status;
	}

}