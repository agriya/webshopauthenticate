@extends(Config::get('webshopauthenticate::package_layout'))
@section('content')
{{-- Breadcrums start
    {{ Breadcrumbs::render('view-profile', $breadcrumb_arr) }}
Breadcrums end --}}

@if($d_arr['error_msg'] != '')
     <div class="alert alert-error">{{ $d_arr['error_msg'] }}</div>
@else
	<div class="row profile-block">
        <div class="col-lg-9 profile-mainblock">
            <div class="well">
            	<h1 class="title-one">{{ str_replace('VAR_USERNAME', $user_details['display_name'], trans('webshopauthenticate::myaccount/viewProfile.profile_title')) }}</h1>
	            <div class="row">
                    <div class="clearfix mt20 col-lg-6 mb20">
                        <div class="panel panel-info no-mar">
                            <div class="panel-heading"><h4>{{ trans('webshopauthenticate::myaccount/viewProfile.user_about') }}</h4></div>
                            @if($user_arr['about_me'] != '')
                                <p>{{ $user_arr['about_me'] }}</p>
                            @endif
                            <p>{{ str_replace('VAR_DATE_OF_JOIN', '<strong class="text-muted">'.date('M j, Y', strtotime($user_arr['created_at'])).'</strong>', trans('webshopauthenticate::myaccount/viewProfile.joined_text')) }}</p>
                        </div>
                    </div>
                    {{-- Right side block start --}}
                    <div class="col-lg-6">
                        @if($user_arr['is_shop_owner'])
                        	{{\Webshoppack::getShopDetailsView($user_arr['id'], true)}}
                        @endif
                    </div>
                {{-- Right side block end --}}
                </div>
           	</div>
        </div>
		{{-- Left user profile block start --}}
        <div class="col-lg-3">
        	@include(\Config::get('webshopauthenticate::leftUserProfile'))
		</div>
		{{-- Left user profile block end --}}
    </div>
@endif
@stop
@section('script_content')

	<script language="javascript" type="text/javascript">
		$(".fn_signuppop").fancybox({
	        maxWidth    : 800,
	        maxHeight   : 630,
	        fitToView   : false,
	        width       : '70%',
	        height      : '430',
	        autoSize    : false,
	        closeClick  : false,
	        type        : 'iframe',
	        openEffect  : 'none',
	        closeEffect : 'none'
	    });
	</script>
@stop