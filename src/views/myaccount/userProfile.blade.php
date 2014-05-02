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
                            <div class="panel panel-info no-mar">
                                <div class="panel-heading">
                                    @if(count($d_arr['shop_product_list']) > 0)
                                        <a href='{{ $d_arr['shop_url'] }}' class="pull-right"><i class="fa fa-angle-double-right"></i> {{ trans('webshopauthenticate::myaccount/viewProfile.see_more') }}</a>
                                    @endif
                                    <h4>{{ trans('webshopauthenticate::myaccount/viewProfile.shop_products') }}</h4>
                                </div>
                                @if(count($d_arr['shop_product_list']) > 0)
                                    <ul class="list-unstyled list-inline userprofile-shop clearfix mb0">
                                        @foreach($d_arr['shop_product_list'] AS $prd)
                                            <?php
                                                $p_img_arr = $mp_product_service->populateProductDefaultThumbImages($prd->id);
                                                $p_thumb_img = $mp_product_service->getProductDefaultThumbImage($prd->id, 'thumb', $p_img_arr);
                                                $view_url = $mp_product_service->getProductViewURL($prd->id, $prd);
                                            ?>
                                            <li>
                                                <a href="{{ $view_url }}" class="img81x64"><img src="{{ $p_thumb_img['image_url'] }}" @if(isset($p_thumb_img["thumbnail_width"])) width='{{ $p_thumb_img["thumbnail_width"] }}' height='{{ $p_thumb_img["thumbnail_height"] }}' @endif title="{{ $prd->product_name  }}" alt="{{ $prd->product_name  }}" /></a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="alert alert-info">{{ trans('webshopauthenticate::myaccount/viewProfile.profile_noshop_products_found') }}</div>
                                @endif
                            </div>
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