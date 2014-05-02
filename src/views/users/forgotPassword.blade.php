@extends(Config::get('webshopauthenticate::package_layout'))
@section('content')
	@include('webshopauthenticate::notifications')
	<div class = "page-header">
		<h3>{{ \Lang::get('webshopauthenticate::auth/form.forget_password.forgot_password') }}</h3>
	</div>
	<div class = "panel panel-default stocklist-panel">
		<div class = "panel-body">
			{{ Form::open(array('action' => array('Agriya\Webshopauthenticate\AuthController@postForgotpassword'), 'class' => 'form-horizontal',  'id' => 'forgotpassword_frm')) }}
		        <fieldset>
		            <div class="form-group {{{ $errors->has('email') ? 'error' : '' }}}">
		                {{ Form::label('email', \Lang::get('webshopauthenticate::users.forgot_credential.email_address'), array('class' => 'col-lg-2 control-label required-icon')) }}
		                <div class="col-lg-3">
		                    {{  Form::text('email', null, array ('class' => 'form-control')); }}
		                    <label class="error">{{{ $errors->first('email') }}}</label>
		                </div>
		            </div>
		            <div class="form-group">
		                <div class="col-lg-offset-2 col-lg-10">
		                	<button name="login" id="login" data-complete-text="Login" data-loading-text='Loading' class="btn btn-success">{{ \Lang::get('webshopauthenticate::users.forgot_credential.submit') }}</button>
		                    <a href="{{ URL::to(\Config::get('webshopauthenticate::uri').'/login') }}" class="btn btn-success" >{{ \Lang::get('webshopauthenticate::users.forgot_credential.back') }}</a>
		                </div>
		            </div>
		        </fieldset>
			{{ Form::close() }}
		</div>
	</div>
@stop
@section('script_content')
	<script language="javascript" type="text/javascript">
        var mes_required = "{{ Lang::get('webshopauthenticate::auth/form.required') }}";
	    $("#forgotpassword_frm").validate({
	        rules: {
	            email: {
	                required: true,
	                email: true
	            }
	        },
	        messages: {
	            email: {
	                required: mes_required
	            }
	        }
	    });
	</script>
@stop