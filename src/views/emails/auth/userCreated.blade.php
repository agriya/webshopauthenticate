@extends('webshopauthenticate::mail')
@section('email_content')
<div style="padding-bottom:25px; font:normal 13px Arial, Helvetica, sans-serif; color:#333;">Hi {{ $first_name }},</div>

<div style="margin-bottom:25px; line-height:18px; padding:10px 20px; background:#fafafa; border:1px solid #eaeaea; border-radius:3px;">
    <p style="margin:10px 0 15px 0; padding:0; font:bold 14px Arial, Helvetica, sans-serif; color:#333;">We have created an account for you </p>
    <table width="98%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="100" valign="top" align="left">
            <p style="padding:0; margin:0 0 10px 0; color:#8c8c8c; font:bold 12px Arial, Helvetica, sans-serif;">Email:</p></td>
            <td valign="top" align="left"><p style="padding:0;margin:0 0 10px 0;color:#1a1a1a; font:normal 12px Arial, Helvetica, sans-serif;">{{ $email }}</p></td>
        </tr>

        <tr>
            <td width="100" valign="top" align="left">
            <p style="padding:0; margin:0 0 10px 0; color:#8c8c8c; font:bold 12px Arial, Helvetica, sans-serif;">Password:</p></td>
            <td valign="top" align="left"><p style="padding:0;margin:0 0 10px 0;color:#1a1a1a; font:normal 12px Arial, Helvetica, sans-serif;">{{ $password }}</p></td>
        </tr>
    </table>
</div>

<p style="margin:0px 0 35px 0; padding:0; font:bold 14px Arial, Helvetica, sans-serif; color:#858889;">You can change your password from the myaccount section.</p>
<p style="padding-bottom:5px; margin:0; font:normal 12px Arial, Helvetica, sans-serif; color:#333;">Regards,</p>
<span style="text-transform:capitalize; margin:0; font:bold 13px Arial, Helvetica, sans-serif; color:#353535;">The {{ \Config::get('webshopauthenticate::package_name') }} Team </span>
@stop
