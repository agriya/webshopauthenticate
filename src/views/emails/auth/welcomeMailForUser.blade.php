@extends('webshopauthenticate::mail')
@section('email_content')
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td>
        	<h2 style="margin:0 0 15px 0; font:normal 14px Arial, Helvetica, sans-serif; color:#383838;">Hi {{ $user->first_name }}, </h2>
        </td>
    </tr>
    <tr>
        <td>
            <table width="96%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <h2 style="margin:0;padding:0 0 12px 0; font:normal 14px Arial, Helvetica, sans-serif; color:#383838;">Your new account is ready! Thank you for joining {{ \Config::get('webshopauthenticate::package_name') }}. </h2>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="font:normal 14px Arial, Helvetica, sans-serif; color:#383838; padding:20px 0 5px;">
            <p style="padding-bottom:5px; margin:0;">Thanks</p>
            <p style="margin:0;">
            	<span style="text-transform:capitalize">The {{ \Config::get('webshopauthenticate::package_name') }} Team </span>
            </p>
        </td>
    </tr>
</table>
@stop