@extends('webshopauthenticate::mail')
@section('email_content')
<div style="background:#f9f9f9; border:1px solid #efefef; padding:15px;">
	<div style="padding-bottom:30px; font:normal 13px Arial, Helvetica, sans-serif; color:#0C4261;">Hi,</div>
	<div style="line-height:18px;">
		{{ $content }}
	</div>
	<div style="font:normal 13px Arial, Helvetica, sans-serif; color:#0C4261; padding-top:45px;">
		<p>Regards</p>
		<strong style="text-transform:capitalize">The {{ \Config::get("site.site_name") }} Team</strong>
	</div>
</div>
@stop