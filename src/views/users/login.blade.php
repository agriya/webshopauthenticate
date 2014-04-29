@extends('webshopauthenticate::base')
@section('content')
	@include('webshopauthenticate::notifications')
	@if(Session::has('success_message'))
		<div class="alert alert-success alert-block">
			<h4>{{\Lang::get('webshopauthenticate::auth/form.reset-password.password_reset_success')}}</h4>
			<p>{{\Lang::get('webshopauthenticate::auth/form.reset-password.password_reset_success_msg')}}</p>
		</div>
	@endif
	@if(Session::has('change_password_error'))
		<div class="alert alert-danger alert-block">
			<h4>{{\Lang::get('webshopauthenticate::auth/form.reset-password.password_reset_failure')}}</h4>
		</div>
	@endif
	@if(Session::has('success_msg') && Session::get('success_msg') != '')
		<div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4>{{trans('auth/form.register.signup_done')}}</h4>
            @if(Session::has('success_msg'))
               {{trans('auth/form.register.create_account_success')}}
                <p>{{trans('auth/form.register.signup_sent_email_3')}}</p>
            @endif
        </div>
    @else
		<div class = "page-header">
			<h2 class="form-signin-heading">{{ \Lang::get('webshopauthenticate::users.login_heading') }}</h2>
		</div>
		<div class = "panel panel-default stocklist-panel">
			<div class = "panel-body">
				 {{ Form::open(array('action' => array('Agriya\Webshopauthenticate\AuthController@postLogin'), 'method' => 'post', 'class' => 'form-horizontal', 'role' => 'form', 'name' => 'login_form', 'id' => 'login_form')) }}
			        <div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
			        	{{ Form::label('email', \Lang::get('webshopauthenticate::users.credential.email_address'),  array('class' => 'col-lg-2 control-label', 'for' => 'email')) }}
			            <div class="col-lg-3">
			            	{{ Form::text('email', null, array('placeholder' => 'example@example.com', 'class' => 'form-control', 'id' => 'email', 'style' => 'width: 200px;')) }}
			             	<label class="error">{{{ $errors->first('email') }}}</label>
		            	</div>
			        </div>
			        <div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
		    			{{ Form::label('password', \Lang::get('webshopauthenticate::users.credential.password'), array('class' => 'col-lg-2 control-label', 'for' => 'password')) }}
		    			<div class="col-lg-3">
		    				{{ Form::password('password', array('class' => 'form-control', 'id' => 'password', 'style' => 'width: 200px;')) }}
		    				<label class="error">{{{ $errors->first('password') }}}</label>
		            	</div>
					</div>
					<div>
			          <div>
			            <label>
			               {{ Form::checkbox('remember') }}
			               Remember Me
			            </label>
			         </div>
			        </div>
			            <div>
			                <div>
			                    <button name="login" id="login" data-complete-text="Login" data-loading-text='Loading' class="btn btn-success">{{ \Lang::get('webshopauthenticate::users.credential.submit') }}</button>
			                    <a href="{{ URL::to(\Config::get('webshopauthenticate::uri').'/signup') }}" class="btn btn-success">{{ \Lang::get('webshopauthenticate::users.signup_view') }}</a>
			                    <a href="{{ URL::to(\Config::get('webshopauthenticate::uri').'/forgotpassword') }}" class="btn btn-success">{{ \Lang::get('webshopauthenticate::users.forgot_password') }}</a>
			                </div>
			            </div>
			        </fieldset>
			    {{ Form::close() }}
			</div>
		</div>
	@endif
@stop
@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{ Lang::get('webshopauthenticate::auth/form.required') }}";

        $("#login_form").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true
                }
            },
            messages: {
                email: {
                    required: mes_required
                },
                password: {
                    required: mes_required
                }
            }
        });

 		function resendActivationCode() {
			$('#activation_resend_msg').show();
            var email = $('#email').val();
       		displayLoadingImage(true);
			$.post("{{ url('/users/resend-activation-code') }}", {"email": email} , function(data){
				if(data == 'success') {
                    if($('#selErrorMsg').length > 0)
                        $('#selErrorMsg').hide();
                    $('#activation_resend_msg').html("{{trans('auth/form.login.activation_code_send')}}");
                    $("#activation_resend_msg").addClass('alert alert-success');
                }
                else {
                    $('#activation_resend_msg').html(data);
                    $("#activation_resend_msg").addClass('alert alert-error');
                }
            	hideLoadingImage();
			})
        }
    </script>
@stop