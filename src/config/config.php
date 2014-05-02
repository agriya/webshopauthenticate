<?php

return array(

/*
*Admin URI
*/
'package_name'	=>	'Webshop',

/*
*Application URI
*/
'uri'	=>	'webshop/users',
/*
*This view for Login
*/
'login_view'	=>	'login',

/*
*Admin URI
*/
'admin_uri'	=>	'webshop/admin',

/*
*	This view for Sign up
*/
'signup'	=>	'webshopauthenticate::users/signup',

/*
* Setting - package layout
*/
'package_layout' => 'webshopauthenticate::base',

/*
* Setting - package layout for admin
*/
'package_admin_layout' => 'webshopauthenticate::admin',

/*
*	This view for User Profile
*/
'leftUserProfile' => 'webshopauthenticate::myaccount/leftUserProfileBlock',

/*
*	This view for forgot password
*/
'forgot_password'	=>	'webshopauthenticate::users/forgotPassword',

'change_password'	=>	'webshopauthenticate::users/changePassword',

/*
*	User actuvation value
*/
'user_auto_activate'	=>	0,

/**
 * Page title of the members list page
 *
 * @type string
 */
'page_title' => 'Authentication package',

/**
 * Meta description of the members list page
 *
 * @type string
 */
'page_meta_description' => 'This is the description for the authentication package',

/**
 * Meta keywords of the members list page
 *
 * @type string
 */
'page_meta_keywords' => 'These are the keywords for the authentication package',

/*
*Auth config
*/
'screen_name_restrict_keywords_like' => 'admin,webmaster',
'screen_name_restrict_keywords_exact' => "user,users,payment,pages,cron",
'fieldlength_password_min' => 6,
'fieldlength_password_max' => 20,
'fieldlength_phone_max' => 15,
'fieldlength_company_name_min' => 3,
'fieldlength_name_min_length' => 2,
'fieldlength_name_max_length' => 20,
'site_captcha_display' => 0,

/**
 * The settings for list the users in multiple pages
 */
'list_paginate' => 5,


);

?>