<?php namespace Agriya\Webshopauthenticate;

use Cartalyst\Sentry\Users\UserNotFoundException as sentrycheck;
//@added by bindu_139at10
class ProfileController extends \BaseController
{
	private $shop_product_list_limit = 8;
	function __construct()
	{
		$this->beforeFilter('auth', array('except' => array('viewProfile', 'emailActivation')));
        $this->userService = new UserAccountService();
        //$this->myaccountService = new MyAccountListService();
    }

	public function getIndex()
	{
		$udetails = array();
		if(!Sentry::check())
		{
			$url = url('/');
			return Redirect::to($url);
		}
		$logged_user_id = Sentry::getUser()->user_id;
		$udetails = $this->userService->getUserinfo($logged_user_id);

		$this->header->setMetaTitle(trans('meta.editprofile_title'));
    	$this->header->setMetaKeyword(trans('meta.editprofile_keyword'));
    	$this->header->setMetaDescription(trans('meta.editprofile_description'));

		return View::make('myaccount/editProfile', compact('udetails'));
	}


	public function postIndex()
	{
		$success_message = "";
		if(!Sentry::check())
		{
			$url = url('/');
			return Redirect::to($url);
		}
		$user = Sentry::getUser();
		$logged_user_id = $user->user_id;
		$input = Input::all();

		$input['user_id'] = $logged_user_id;
		$input['email'] = $user['email'];

		if(Input::has('edit_basic'))
		{
			$rules = array();
			$messages = array();
			if(Input::has('new_email') && Input::get('new_email') != $user['email'])
			{
				$rules['new_email'] = $this->userService->getValidatorRule('email');
			}

			if(Input::has('Oldpassword') && Input::has('password') && Input::get('password') != "" && Input::get('Oldpassword') != Input::get('password'))
			{
				$rules['Oldpassword'] = $this->userService->getValidatorRule('Oldpassword');
				$messages['Oldpassword.is_valid_old_password'] = trans("myaccount/form.edit-profile.wrong_password");
				$rules['password'] = $this->userService->getValidatorRule('password');
				$rules['password_confirmation'] = 'Required|same:password';
			}

			$validator = Validator::make(Input::all(), $rules, $messages);
			if ($validator->fails())
			{
				return Redirect::back()->withInput()->withErrors($validator);
			}
			$success_message = $this->userService->updateBasicDetails($input);
		}
		elseif(Input::has('edit_personal'))
		{
			$rules = array();
			$rules['first_name'] = $this->userService->getValidatorRule('first_name');
			$rules['last_name'] = $this->userService->getValidatorRule('last_name');
			$rules['phone'] = $this->userService->getValidatorRule('phone');

			$messages = array();

			$validator = Validator::make(Input::all(), $rules, $messages);
			if ($validator->fails())
			{
				return Redirect::back()->withInput()->withErrors($validator);
			}
			$this->userService->updateUserPersonalDetails($input);
			$success_message = trans("myaccount/form.edit-profile.personal_details_update_sucess");
		}
		return Redirect::to('users/edit-account')->with('success_message', $success_message);
	}

	public function imageUpload()
	{
		$input = Input::all();
		if (Input::hasFile('file'))
		{
			if($_FILES['file']['error'])
			{
				$errMsg = trans("common.uploader_max_file_size_err_msg");
				return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
			}
			$allowed_ext = Config::get("generalConfig.uploader_allowed_extensions");
			$file = Input::file('file');
			$file_size = $file->getClientSize();
			$image_ext = $file->getClientOriginalExtension();
			$allowed_size = Config::get("generalConfig.user_image_uploader_allowed_file_size");
			$allowed_size = $allowed_size * 1024; //To convert KB to Byte
			if(stripos($allowed_ext, $image_ext) === false)
			{
				$errMsg = trans("common.uploader_allow_format_err_msg");
				return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
			}
			else if(($file_size > $allowed_size)  || $file_size <= 0)
			{
				$errMsg = trans("common.uploader_max_file_size_err_msg");
				return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
			}
			else
			{
				$resize_image = "true";

				$image_id = "";
				$field_name = Input::get("field_name");
				$image_folder = Input::get("image_folder");
				$image_name = Str::random(20);
				$destinationpath = URL::asset(Config::get("generalConfig.user_image_folder"));
				$upload_input = array();
				$upload_input['image_ext'] = $image_ext;
				$upload_input['image_name'] = $image_name;
				$upload_input['image_server_url'] = $destinationpath;
				$image_id = $this->userService->uploadUserImage($file, $image_ext, $image_name, $destinationpath);
				if($image_id > 0)
				{
					$small_img = URL::asset(Config::get("generalConfig.user_image_folder")).'/'.$image_name.'_T.'.$image_ext;
					return Response::json(array('status' => 'success', 'small_url' => $small_img, 'resource_id' => $image_id, 'name' => $image_name, 'ext' => $image_ext));
				}
				else
				{
					$errMsg = trans("common.uploader_invalid_img_err_msg");
					return Response::json(array('status' => 'failure', 'error_message' => $errMsg));
				}
			}
		}
	}

	public function getDeleteUserImage()
	{
		if(!Sentry::check())
		{
			$url = url('/');
			return Redirect::to($url);
		}

		$resource_id 	= Input::get("resource_id");
		$resource_type 	= Input::get("resource_type");
		$imagename 		= Input::get("imagename");
		$imageext 		= Input::get("imageext");
		$imagefolder 	= Input::get("imagefolder");

		if($imagename != "")
		{
			if($resource_type == "User")
			{
				$affectedRows = UserImage::whereRaw('image_id = ?', array($resource_id))->delete();
				if($affectedRows)
				{
					$this->userService->deleteImageFiles($imagename, $imageext, Config::get($imagefolder));
				}
			}
			return Response::json(array('result' => 'success'));
		}
		else
		{
			return Response::json(array('result' => 'error'));
		}
	}

	public function emailActivation($activationCode)
	{
		$status = $this->userService->updateEmail($activationCode);
		$url = Url::action('ProfileController@emailActivationResponse', $status);
		return Redirect::to($url);
	}

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

	public function viewProfile($user_code_seo_title)
    {
		$error_msg = trans('myaccount/viewProfile.invalid_user');
		$d_arr = $breadcrumb_arr = $user_arr = array();

		$user_id = CUtil::getUserIdFromSlug($user_code_seo_title);
		$shop_url = '';

		if($user_id != '')
		{
			$user_arr = User::where('id', '=', $user_id)
							->first(array('id', 'created_at'));
			if(count($user_arr) > 0)
			{
				$error_msg = '';
				$user_details = array();
				$user_details = \Agriya\Webshoppack\CUtil::getUserDetails($user_id);

				$breadcrumb_arr[] = $user_details['display_name'];
				$title = str_replace('VAR_USER_NAME', $user_details['display_name'], trans('meta.viewprofile_title'));
				//$this->header->setMetaTitle($title);
		    	//$this->header->setMetaKeyword(trans('meta.viewprofile_keyword'));
		    	//$this->header->setMetaDescription(trans('meta.viewprofile_description'));
		    	$user_arr['is_shop_owner'] = CUtil::isShopOwner($user_id);
				if($user_arr['is_shop_owner'])
		    	{
		    		$mp_product_service = new \Agriya\Webshoppack\ProductService();
		    		$d_arr['shop_details'] = $mp_product_service->getShopDetails($user_id);
		    		$d_arr['shop_product_list'] = $mp_product_service->fetchShopItems($user_id, $this->shop_product_list_limit);
					$shop_url = $mp_product_service->getProductShopURL($d_arr['shop_details']['id'], $d_arr['shop_details']);
		    	}
		    }
	    }
	    $d_arr['error_msg'] = $error_msg;
	    $d_arr['shop_url'] = $shop_url;

	    $user = \Config::get('webshoppack::logged_user_id');
		$logged_user_id = $user();

		return \View::make('webshopauthenticate::myaccount.userProfile', compact('user_details', 'breadcrumb_arr', 'd_arr', 'user_id', 'mp_product_service', 'user_arr', 'logged_user_id'));
	}

}