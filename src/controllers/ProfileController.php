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

	public function viewProfile($user_code_seo_title)
    {
		$error_msg = trans('webshopauthenticate::myaccount/viewProfile.invalid_user');
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

				$user_details = \Webshoppack::getUserDetails($user_id); //\Agriya\Webshoppack\CUtil::getUserDetails($user_id);

				$breadcrumb_arr[] = $user_details['display_name'];
				$title = str_replace('VAR_USER_NAME', $user_details['display_name'], trans('meta.viewprofile_title'));
				$user_arr['is_shop_owner'] = CUtil::isShopOwner($user_id);
				//if($user_arr['is_shop_owner'])
		    	//{
		    		//$mp_product_service = new \Agriya\Webshoppack\ProductService();
		    		//$d_arr['shop_details'] = \Webshoppack::getShopDetails($user_id);
		    		//$d_arr['shop_product_list'] = \Webshoppack::fetchShopItems($user_id, $this->shop_product_list_limit);
		    		//$shop_url = \Webshoppack::getProductShopURL($d_arr['shop_details']['id'], $d_arr['shop_details']);
		    	//}
		    }
	    }
	    $d_arr['error_msg'] = $error_msg;
	    //$d_arr['shop_url'] = $shop_url;

	    $user = \Config::get('webshoppack::logged_user_id');
		$logged_user_id = $user();

		return \View::make('webshopauthenticate::myaccount.userProfile', compact('user_details', 'breadcrumb_arr', 'd_arr', 'user_id', 'user_arr', 'logged_user_id'));
	}

}