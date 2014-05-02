<?php
	//$user_img = CUtil::getUserPersonalImage($user_id, 'thumb');
	//$logged_user_id = (Sentry::check()) ? Sentry::check()->id : 0;
	//$feedback_arr = $mp_product_service->getFeedbackStatus($user_id);
	//$feedback_url = FeedbackService::getFeedbackViewURL($user_id);
	$route_action = Route::currentRouteAction();
?>
<div class="aside-bar">
	<div class="title-block">
		<h3>shop owner</h3>
    </div>
	<div class="clearfix">
		<a href='{{$user_details['profile_url']}}' class="light-link">{{$user_details['display_name']}}</a>
	</div>
	<ul class="list-unstyled no-mar clearfix">
		@if($logged_user_id == $user_id )
			<li><i class="fa fa-angle-right"></i> <span>{{ HTML::link(URL::to(\Config::get('webshopauthenticate::uri').'/myaccount'), trans('webshopauthenticate::myaccount/viewProfile.edit'), array()) }}</span></li>
		@endif
		<li @if($route_action == 'Agriya\Webshopauthenticate\ProfileController@viewProfile') class="active" @endif><i class="fa fa-angle-right"></i> <span>{{ HTML::link($user_details['profile_url'], trans('webshopauthenticate::myaccount/viewProfile.profile'), array()) }}</span></li>

		@if(Sentry::check())
			@if($logged_user_id != $user_id )
				<li><a href="{{Url::to(\Config::get('webshoppack::shop_uri').'/user/message/add/'.$user_details['user_code']) }}" class="fn_signuppop"><i class="fa fa-angle-right"></i><span>{{ trans('webshopauthenticate::myaccount/viewProfile.contact') }}</span></a></li>
			@endif
		@else
			<?php $login_url = \url(\Config::get('webshopauthenticate::uri').'/login?form_type=selLogin'); ?>
			<li><a href="{{ $login_url }}" class="fn_signuppop"><i class="fa fa-angle-right"></i><span>{{ trans('webshopauthenticate::myaccount/viewProfile.contact') }}</span></a></li>
		@endif
	</ul>
</div>
{{-- Shop block start --}}
<div class="aside-bar">
	<div class="title-block">
		<h3>{{ trans('webshopauthenticate::myaccount/viewProfile.shop') }}</h3>
    </div>
	@if($user_arr['is_shop_owner'] == 'Yes')
		<?php $d_arr['shop_details'] = \Webshoppack::getShopDetails($user_arr['id']); ?>
		<a href="{{ $d_arr['shop_details']['shop_url'] }}"><strong>{{{ $d_arr['shop_details']['shop_name'] }}}</strong></a>
		<p class="text-muted mt10">{{{ $d_arr['shop_details']['shop_slogan'] }}}</p>
	@else
		<p class="alert alert-info">{{ trans('webshopauthenticate::myaccount/viewProfile.shop_not_added_yet') }}</p>
	@endif
</div>
{{-- Shop block end --}}