@extends(Config::get('webshopauthenticate::package_layout'))
@section('content')
	<h1 class="title-one">{{ \Lang::get("webshopauthenticate::auth/form.change_password.legend") }}</h1>

    @if(isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @else
	    {{ Form::open(array('url' => array(\Config::get('webshopauthenticate::uri').'/change-password/'), 'class' => 'form-horizontal', 'method'=> 'post', 'id' => 'changepassword_frm')) }}
	        <input type="hidden" name="token" value="{{ $token }}">
	        <fieldset>
	            <div class="form-group {{{ $errors->has('password') ? 'error' : '' }}}">
	                {{ Form::label('password', \Lang::get("webshopauthenticate::auth/form.change_password.new_password"), array('class' => 'col-lg-2 control-label required-icon')) }}
	                <div class="col-lg-3">
	                    {{  Form::password('password', array ('class' => 'form-control')); }}
	                    <label class="error">{{{ $errors->first('password') }}}</label>
	                </div>
	            </div>

	            <div class="form-group {{{ $errors->has('password_confirmation') ? 'error' : '' }}}">
	                {{ Form::label('password_confirmation', \Lang::get("webshopauthenticate::auth/form.change_password.confirm_password"), array('class' => 'col-lg-2 control-label required-icon')) }}
	                <div class="col-lg-3">
	                    {{  Form::password('password_confirmation', array ('class' => 'form-control')); }}
	                    <label class="error">{{{ $errors->first('password_confirmation') }}}</label>
	                </div>
	            </div>

	            <div class="form-group">
	                <div class="col-lg-offset-2 col-lg-10">
	                    <button name="login" id="login" data-complete-text="Login" data-loading-text='Loading' class="btn btn-success">{{ \Lang::get('webshopauthenticate::users.credential.submit') }}</button>
	                </div>
	            </div>
	        </fieldset>
	    {{ Form::close() }}
	@endif
@stop
@section('script_content')
	<script language="javascript" type="text/javascript">
		var mes_required = "{{ Lang::get('webshopauthenticate::auth/form.required') }}";
		$("#changepassword_frm").validate({
			rules: {
				password: {
				required: true,
				minlength: "{{ Config::get('webshopauthenticate::fieldlength_password_min') }}",
				maxlength: "{{ Config::get('webshopauthenticate::fieldlength_password_max') }}"
			},
			password_confirmation: {
				required: true,
				equalTo: "#password"
			}
			},
			messages: {
				password: {
					required: mes_required,
					minlength: jQuery.format("{{ Lang::get('webshopauthenticate::auth/form.register.validation_password_length_low') }}"),
	                maxlength: jQuery.format("{{ Lang::get('webshopauthenticate::auth/form.register.validation_maxLength') }}")
				},
				password_confirmation: {
					required: mes_required,
					equalTo: "{{ Lang::get('webshopauthenticate::auth/form.register.validation_password_mismatch') }}"
				}
			}
		});
	</script>
@stop