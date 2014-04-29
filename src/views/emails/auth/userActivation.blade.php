@extends('webshopauthenticate::mail')
@section('email_content')
<div style="padding-bottom:15px;">Hi {{ $user->first_name }},</div>

<div style="padding-bottom:25px; line-height:18px;">
	<p style="margin:0; padding:0 0 15px 0;">Please click the following link to activate your account on {{ \Config::get('webshopauthenticate::package_name') }}</p>
    <p style="margin:0; padding:0 0 20px 0;"><a href="{{ $activationUrl }}" style="font:bold 13px Arial; color:#00a1b1; text-decoration:none;">"{{ $activationUrl }}"</a></p>
</div>

<p style="padding-bottom:5px; margin:0;">Regards</p>
<span style="text-transform:capitalize">The {{ \Config::get('webshopauthenticate::package_name') }} </span> Team
@stop

